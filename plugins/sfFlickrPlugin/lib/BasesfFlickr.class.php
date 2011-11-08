<?php
/**
 * This file is part of the sfFlickr package.
 * (c) 2006-2007 Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Correct include path so Phlickr files can be required
 */
set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__));

/**
 * Require Base API for Phlickr 
 */
require_once('Phlickr/Api.php');

/**
 * Base Symfony Flickr Class
 *
 * Class that uses Phlickr open source code for communicating with Flickr api
 * 
 * <code>
 * <?php
 * $sfFlickr = new BasesfFlickr(); // if no arguments specified then it will attempt to load flickr api keys from settings
 * $sfFlickr = new BasesfFlickr('flickr_api_key', 'flickr_api_secret');
 * ?>
 * </code>
 * 
 * @package     sfFlickr
 * @version     SVN: $Id: BasesfAmazonS3.class.php 3285 2007-02-28 20:01:09Z jwage $
 * @author      Jonathan H. Wage <jonwage@gmail.com> 
 */
class BasesfFlickr
{
	/**
	 * Flickr Api Key
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->setApiKey('api_key');
	 * echo $sfFlickr->getApiKey();
	 * ?>
	 * </code>
	 * 
	 * @var string
	 * @access public
	 */
	public $apiKey;
	
	/**
	 * Flickr Api Secret
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->setApiSecret('api_secret');
	 * echo $sfFlickr->getApiSecret();
	 * ?>
	 * </code>
	 * 
	 * @var string
	 * @access public
	 */
	public $apiSecret;
	
	/**
	 * Auth Token 
	 * 
	 * <code>
	 * <?php
	 * $sfFlickr->setAuthToken('auth_token');
	 * echo $sfFlickr->getAuthToken();
	 * 
	 * @var string
	 * @access public
	 */
	public $authToken;
	
	/**
	 * Uploader
	 *
	 * Instance of the Phlickr Uploader class. Does not need to be set, will be set automatically the first time it is retrieved
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getUploader());
	 * ?>
	 * </code>
	 * 
	 * @var object
	 * @access public
	 */
	public $uploader;
	
	/**
	 * Api
	 *
	 * Instance of the Phlickr API, is automatically set in the constructor
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getApi());
	 * ?>
	 * </code>
	 * 
	 * @var object
	 * @access public
	 */
	public $api;
	
	/**
	 * __construct 
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct($flickrApiKey = null, $flickrApiSecret = null)
	{
		if( $flickrApiKey AND $flickrApiSecret )
		{
			$this->setApiKey($flickrApiKey);
			$this->setApiSecret($flickrApiSecret);
		} else {	
			// Load our flickr api key and secret key from sfConfig - values stored in config/app.yml
			$this->setApiKey(sfConfig::get('app_flickr_api_key'));
			$this->setApiSecret(sfConfig::get('app_flickr_api_secret'));
		}
		
		// If we have a auth token then do authenticated connect to api
		if( $authToken = $this->getAuthToken() )
		{
			$this->setApi(new Phlickr_Api($this->getApiKey(), $this->getApiSecret(), $this->getAuthToken()));
		// Normal, un-authenticated connect to api
		} else {
			$this->setApi(new Phlickr_Api($this->getApiKey(), $this->getApiSecret()));
		}
	}

	/**
	 * Get Auth Token
	 *
	 * <code>
	 * <?php
	 * echo $BasesfFlickr->getAuthToken();
	 * ?>
	 * </code>
	 * 
	 * @static
	 * @access public
	 * @return string $authToken
	 */
	public function getAuthToken()
	{
		if( $this->authToken )
		{
			return $this->authToken;
		} else {	
			$authToken = sfContext::getInstance()->getUser()->getAttribute('flickr_auth_token');
			$this->setAuthToken($authToken);
			return $authToken;
		}
	}

	/**
	 * Set Auth Token
	 * 
	 * <code>
	 * <?php
	 * $url = $sfFlickr->getAuthenticationUrl(); // go to url and flickr will return back to your app with the $frob var set in the request
	 * $frob = 'frob';
	 * $authToken = $sfFlickr->getAuthTokenFromFrob($frob); // you must generate the auth token from the frob flickr provided from the auth url
	 * $sfFlickr->setAuthToken($authToken);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $authToken 
	 * @access public
	 * @return void
	 */
	public function setAuthToken($authToken)
	{
		sfContext::getInstance()->getUser()->setAttribute('flickr_auth_token', $authToken);
		
		$this->authToken = $authToken;
	}
	
	/**
	 * Get Authentication Url
	 *
	 * <code>
	 * <?php
	 * echo $sfFlickr->getAuthenticationUrl('read'); // can be read, write, or delete
	 * ?>
	 * </code>
	 * 
   * read 	- permission to read private information
   * write 	- permission to add, edit and delete photo metadata (includes 'read')
   * delete - permission to delete photos (includes 'write' and 'read')
	 *
	 * 
	 * @param string $perms Permissions to authenticated for(read/write/delete)
	 * @access public
	 * @return string $url
	 */
	public function getAuthenticationUrl($perms = 'delete')
	{
		$frob = $this->getApi()->requestFrob();
		
		$url = $this->getApi()->buildAuthUrl($perms, $frob);
		
		return $url;
	}

	/**
	 * Get Auth Token From Frob
	 *
	 * <code>
	 * <?php
	 * $frob = 'frob from request'; // frob is returned from the getAuthenticationUrl() in the request
	 * $authToken = $sfFlickr->getAuthTokenFromFrob('frob');
	 * $sfFlickr->setAuthToken($authToken);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $frob 
	 * @access public
	 * @return string $authToken
	 */
	public function getAuthTokenFromFrob($frob)
	{
		// Convert frob to a token after they have granted permission
		$token = $this->getApi()->setAuthTokenFromFrob($frob);

		$this->setAuthToken($token);

		return $token;
	}

	/**
	 * Add Photo To Authed Group
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->addPhotoToAuthedGroup(1, 2); // flickr photo_id, group_id
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo id to add to group id
	 * @param mixed $group_id Flickr group id to remove photo id from
	 * @access public
	 * @return void
	 */
	public function addPhotoToAuthedGroup($photo_id, $group_id)
	{
		return $this->getAuthedGroup($group_id)->add($photo_id);
	}

	/**
	 * Remove Photo From Authed Group
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->removePhotoFromAuthedGroup(1, 2); // flickr photo_id, group_id
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo id to remove from group id
	 * @param mixed $group_id Flickr group id to remove photo id from
	 * @access public
	 * @return void
	 */
	public function removePhotoFromAuthedGroup($photo_id, $group_id)
	{
		return $this->getAuthedGroup($group_id)->remove($photo_id);
	}

	/**
	 * Delete Authed Photo
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->deleteAuthedPhoto(1); // flickr photo_id
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo id to delete
	 * @access public
	 * @return void
	 */
	public function deleteAuthedPhoto($photo_id)
	{
		return $this->getAuthedPhoto($photo_id)->delete();
	}
	
	/**
	 * Set Authed Photo meta
	 * 
	 * <code>
	 * <?php
	 * $sfFlickr->setAuthedPhotoMeta(1, 'Photo Title', 'Description of photo');
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo_id to set the meta data for
	 * @param mixed $title Title of the flickr photo_id
	 * @param mixed $description Description of the flickr photo_id
	 * @access public
	 * @return void
	 */
	public function setAuthedPhotoMeta($photo_id, $title, $description)
	{
		return $this->getAuthedPhoto($photo_id)->setMeta($title, $description);
	}

	/**
	 * Set Authed Photo Date Posted
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->setAuthedPhotoDatePosted(1, '2007-03-28'); // 2nd arg can be unix timestamp or any date format
	 * $sfFlickr->setAuthedPhotoDatePosted(1, strtotime('2007-03-28'));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo_id to set the date posted for 
	 * @param mixed $timestamp Unix timestamp or string date formatted however you want
	 * @access public
	 * @return void
	 */
	public function setAuthedPhotoDatePosted($photo_id, $timestamp)
	{
		// Make sure we have a unix timestamp
		if( !is_int($timestamp) )
		{
			$timestamp = strtotime($timestamp);
		}
		
		return $this->getAuthedPhoto($photo_id)->setPosted($timestamp);
	}
	
	/**
	 * Set Authed Photo Date Taken
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->setAuthedPhotoDateTaken(1, '2007-03-28 00:00:00', 0);
	 * $sfFlickr->setAuthedPhotoDateTaken(1, '2007-03', 4);
	 * $sfFlickr->setAuthedPhotoDateTaken(1, '2007', 6);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo id
	 * @param mixed $timestamp Date the photo was taken
	 * @param mixed $granularity The accuracy to which we know the date to be true. At present, only three granularities are used: 0 =  Y-m-d H:i:s, 4 = Y-m, 6 = Y
	 * @access public
	 * @return void
	 */
	public function setAuthedPhotoDateTaken($photo_id, $timestamp, $granularity = null)
	{
		return $this->getAuthedPhoto($photo_id)->setTaken($timestamp, $granularity);
	}
	
	/**
	 * Set Authed Photo Tags
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->setAuthedPhotoTags(1, array('tag1','tag2','tag3'));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo_id to set the tags for
	 * @param mixed $tags Array of tags to set to the photo_id
	 * @access public
	 * @return void
	 */
	public function setAuthedPhotoTags($photo_id, $tags)
	{
		return $this->getAuthedPhoto($photo_id)->setTags($tags);
	}
	
	/**
	 * Set Authed Photoset Meta
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->setAuthedPhotosetMeta(1, 'Photoset Title', 'Description of Photoset');
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photoset_id Flickr photoset_id to set the title and description for
	 * @param mixed $title Photoset title
	 * @param mixed $description Photoset description
	 * @access public
	 * @return void
	 */
	public function setAuthedPhotosetMeta($photoset_id, $title, $description)
	{
		return $this->getAuthedPhotoset($photoset_id)->setMeta($title, $description);
	}
	
	/**
	 * Set Authed Photoset Photos
	 * 
	 * <code>
	 * <?php
	 * $sfFlickr->setAuthedPhotosetPhotos(1, 1, array(1, 2, 3));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photoset_id Flickr photoset_id to set the primary photo and photos for
	 * @param mixed $primary_photo_id Flickr photo id of the primary photo
	 * @param mixed $photo_ids Array of flickr photo_ids to associated to photoset_id
	 * @access public
	 * @return void
	 */
	public function setAuthedPhotosetPhotos($photoset_id, $primary_photo_id, $photo_ids)
	{
		return $this->getAuthedPhotoset($photoset_id)->editPhotos($primary_photo_id, $photo_ids);
	}

	/**
	 * Get Authed Photosets
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getAuthedPhotosets());
	 * ?>
	 * </code>
	 * 
	 * @access public
	 * @return array $photosetList
	 */
	public function getAuthedPhotosets()
	{					
		return $this->getAuthedUser()->getPhotosetList();
	}

	/**
	 * Get User Photosets
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->getUserPhotosets(1);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $user_id Flickr user_id to get the photosets for
	 * @access public
	 * @return array $photosetList
	 */
	public function getUserPhotosets($user_id)
	{
		return $this->getUser($user_id)->getPhotosetList();
	}
	
	/**
	 * Created Authed Photoset
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->createAuthedPhotoset('Photoset Title', 'Photoset Description', 1);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $title Title of the photoset
	 * @param mixed $description Description of the photoset
	 * @param mixed $primary_photo_id Primary photo_id for the photoset
	 * @access public
	 * @return void
	 */
	public function createAuthedPhotoset($title, $description, $primary_photo_id)
	{
		return $this->getAuthedPhotosetList()->create($title, $description, $primary_photo_id);
	}
	
	/**
	 * Reorder Authed Photosets
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->reorderAuthedPhotosets(array(1, 2, 3));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $ids Array of photoset ids in the order you want them
	 * @access public
	 * @return void
	 */
	public function reorderAuthedPhotosets($ids)
	{
		return $this->getAuthedPhotosetList()->reorder($ids);
	}

	/**
	 * Delete Authed Photoset
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->deleteAuthedPhotoset(1);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photoset_id Flickr photoset_id to delete
	 * @access public
	 * @return void
	 */
	public function deleteAuthedPhotoset($photoset_id)
	{
		return $this->getAuthedPhotosetList()->delete($photoset_id);
	}
	
	/**
	 * Get Authed Contact User List
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getAuthedContactUserList());
	 * ?>
	 * </code>
	 * 
	 * @access public
	 * @return array $contactUserList
	 */
	public function getAuthedContactUserList()
	{
		return $this->getAuthedUser()->getContactUserList();		
	}

	/**
	 * Get Authed Favorite Photo List
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getAuthedFavoritePhotoList(10); // 10 per page
	 * ?>
	 * </code>
	 * 
	 * @param mixed $perPage Number of favorite photos per page 
	 * @access public
	 * @return array $favoritePhotoList
	 */
	public function getAuthedFavoritePhotoList($perPage = Phlickr_PhotoList::PER_PAGE_DEFAULT)
	{
		return $this->getAuthedUser()->getFavoritePhotoList($perPage);
	}

	/**
	 * Get Authed Group List
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getAuthedGroupList());
	 * ?>
	 * </code>
	 * 
	 * @access public
	 * @return array $groupList
	 */
	public function getAuthedGroupList()
	{
		return $this->getAuthedUser()->getGroupList();
	}

	/**
	 * Get Authed Photo List
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getAuthedPhotoList(5); // 5 per page
	 * ?>
	 * </code>
	 * 
	 * @param mixed $perPage Number of photos to return per page 
	 * @access public
	 * @return array $photoList
	 */
	public function getAuthedPhotoList($perPage = Phlickr__PhotoList::PER_PAGE_DEFAULT)
	{
		return $this->getAuthedUser()->getPhotoList($perPage);
	}

	/**
	 * Get Authed Photoset List
	 *
	 * <code>
	 * <?php
	 * print_r($sfFLickr->getAuthedPhotosetList());
	 * ?>
	 * </code>
	 * 
	 * @access public
	 * @return array $authedPhotosetList
	 */
	public function getAuthedPhotosetList()
	{
		return $this->getAuthedUser()->getPhotosetList();
	}
	
	/**
	 * Add Authed Favorite Photo
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->addAuthedFavoritePhoto(1);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo_id to add to the authed users account
	 * @access public
	 * @return void
	 */
	public function addAuthedFavoritePhoto($photo_id)
	{
		return $this->getAuthedUser()->addFavorite($photo_id);
	}

	/**
	 * Delete Authed Favorite Photo
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->deleteAuthedFavoritePhoto(1);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo_id to delete from authed users favorite photo
	 * @access public
	 * @return void
	 */
	public function deleteAuthedFavoritePhoto($photo_id)
	{
		return $this->getAuthedUser()->removeFavorite($photo_id);
	}
	
	/**
	 * Get User
	 * 
	 * <code>
	 * <?php
	 * print_r($sfFLickr->getUser(1));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $user_id Flickr user_id to get
	 * @access public
	 * @return object $phlickrUser
	 */
	public function getUser($user_id)
	{
		return new Phlickr_User($this->getApi(), $user_id);
	}

	/**
	 * Get Authed User
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getAuthedUser());
	 * ?>
	 * </code>
	 * 
	 * @access public
	 * @return object $phlickrAuthedUser
	 */
	public function getAuthedUser()
	{
		return new Phlickr_AuthedUser($this->getApi());
	}
	
	/**
	 * Get Group
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->getGroup(1);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $group_id Flickr group_id to get the Phlickr Group instance for
	 * @access public
	 * @return object $phlickrGroup
	 */
	public function getGroup($group_id)
	{
		return new Phlickr_Group($this->getApi(), $group_id);
	}

	/**
	 * Get Authed Group
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->getAuthedGroup(1);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $group_id Flickr group_id to get the Phlickr AuthedGroup class for
	 * @access public
	 * @return object $phlickrAuthedGroup
	 */
	public function getAuthedGroup($group_id)
	{
		return new Phlickr_AuthedGroup($this->getApi(), $group_id);
	}

	/**
	 * Get Group Photo List
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getGroupPhotoList(1, 10));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $group_id Group id to get the photos for
	 * @param int $perPage Number of photos to return per page
	 * @access public
	 * @return array $groupPhotoList
	 */
	public function getGroupPhotoList($group_id, $perPage = 10)
	{
		return $this->getGroup($group_id)->getPhotoList($perPage);
	}

	/**
	 * Get Group Url
	 *
	 * <code>
	 * <?php
	 * echo $sfFlickr->getGroupUrl(1);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $group_id Flickr group id to get the url for
	 * @access public
	 * @return string $url
	 */
	public function getGroupUrl($group_id)
	{
		return $this->getGroup($group_id)->buildUrl();
	}

	/**
	 * Get Group Discuss Feed Url
	 *
	 * <code>
	 * <?php
	 * echo $sfFlickr->getGroupDiscussFeedUrl(1, 'atom');
	 * ?>
	 * </code>
	 * 
	 * @param mixed $group_id Flickr group id to get the discuss feed url for
	 * @param string $feed specifying the desired feed format. Acceptable values include 'rss', 'rss2', 'atom', and 'rdf' but you should check http://flickr.com/services/feeds/ for a complete list of formats. 
	 * @access public
	 * @return void
	 */
	public function getGroupDiscussFeedUrl($group_id, $feed = 'atom')
	{	
		return $this->getGroup($group_id)->buildDiscussFeedUrl($feed);	
	}

	/**
	 * Get Group Photo Feed Url
	 *
	 * <code>
	 * <?php
	 * echo $sfFlickr->getGroupPhotoFeedUrl(1, 'atom');
	 * ?>
	 * </code>
	 * 
	 * @param mixed $group_id Flickr group id to get the photo feed url for
	 * @param string $feed specifying the desired feed format. Acceptable values include 'rss', 'rss2', 'atom', and 'rdf' but you should check http://flickr.com/services/feeds/ for a complete list of formats. 
	 * @access public
	 * @return void
	 */
	public function getGroupPhotoFeedUrl($group_id, $feed = 'atom')
	{	
		return $this->getGroup($group_id)->buildPhotoFeedUrl($feed);
	}

	/**
	 * Get Group By Url
	 *
	 * <code>
	 * <?php
	 * echo $sfFlickr->getGroupByUrl('group url');
	 * ?>
	 * </code>
	 * 
	 * @param mixed $url Group url to get the Phlickr Group instance for 
	 * @access public
	 * @return void
	 */
	public function getGroupByUrl($url)
	{
		return Phlickr_Group::findByUrl($this->getApi(), $url);
	}
	
	/**
	 * Get Photo
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getPhoto(1));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo_id to get the Phlickr Photo instance for 
	 * @access public
	 * @return void
	 */
	public function getPhoto($photo_id)
	{
		return new Phlickr_Photo($this->getApi(), $photo_id);
	}

	/**
	 * Get Authed Photo
	 * 
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getAuthedPhoto(1));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photo_id Flickr photo_id to get the Phlickr Authed Photo instance for
	 * @access public
	 * @return void
	 */
	public function getAuthedPhoto($photo_id)
	{
		return new Phlickr_AuthedPhoto($this->getApi(), $photo_id);
	}

	/**
	 * Get Photoset
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getPhotoset(1));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photoset_id Flickr photoset_id to get he Phlickr Photoset for
	 * @access public
	 * @return void
	 */
	public function getPhotoset($photoset_id)
	{	
		return new Phlickr_Photoset($this->getApi(), $photoset_id);
	}
	
	/**
	 * Get Authed Photoset
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getAuthedPhotoset(1));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $photoset_id Flickr photoset_id to get the Phlickr Authed Photoset for
	 * @access public
	 * @return void
	 */
	public function getAuthedPhotoset($photoset_id)
	{
		return new Phlickr_AuthedPhotoset($this->getApi(), $photoset_id);
	}

	/**
	 * Get Uploader
	 *
	 * Returns Phlickr Uploader instance
	 *
	 * <code>
	 * <?php
	 * print_r($sfFlickr->getUploader());
	 * ?>
	 * </code>
	 * 
	 * @access public
	 * @return void
	 */
	public function getUploader()
	{
		if( !$this->uploader )
		{
			$this->uploader = new Phlickr_Uploader($this->getApi());
		}

		return $this->uploader;
	}
	
	/**
	 * Set Uploader Perms 
	 * 
	 * <code>
	 * <?php
	 * $sfFlickr->setUploaderPerms(true, true, true);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $public True/false
	 * @param mixed $friend True/false
	 * @param mixed $family True/false
	 * @access public
	 * @return void
	 */
	public function setUploaderPerms($public, $friend, $family)
	{
		return $this->getUploader()->setPerms($public, $friends, $family);
	}

	/**
	 * Set Uploaded Tags
	 *
	 * Set the tags to be used when uploading files through upload()
	 * 
	 * <code>
	 * <?php
	 * $sfFlickr->setUploaderTags(array('tag1', 'tag2'));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $tags 
	 * @access public
	 * @return void
	 */
	public function setUploaderTags($tags)
	{
		return $this->getUploader()->setTags($tags);
	}

	/**
	 * Upload
	 *
	 * <code>
	 * <?php
	 * $sfFlickr->upload('/path/to/file', 'Title of photo', 'description', 'tags);
	 * ?>
	 * </code>
	 * 
	 * @param mixed $fullFilePath 
	 * @param string $title 
	 * @param string $desc 
	 * @param string $tags 
	 * @access public
	 * @return void
	 */
	public function upload($fullFilePath, $title = '', $desc = '', $tags = '')
	{
		return $this->getUploader()->upload($fullFilePath, $title, $desc, $tags);
	}

	/**
	 * Upload Batch
	 * 
	 * @param Phlickr_Framework_IUploadBatch $batch 
	 * @param Phlickr_Framework_IUploadListener $listener 
	 * @access public
	 * @return void
	 */
	public function uploadBatch(Phlickr_Framework_IUploadBatch $batch, Phlickr_Framework_IUploadListener $listener)
	{
		return $this->getUploader()->uploadBatch($batch, $listener);
	}

	/**
	 * Get Photos Edit Url
	 *
	 * <code>
	 * <?php
	 * echo $sfFlickr->getPhotosEditUrl(array(1, 2, 3));
	 * ?>
	 * </code>
	 * 
	 * @param mixed $ids 
	 * @access public
	 * @return void
	 */
	public function getPhotosEditUrl($ids)
	{
		return Phlickr_Uploader::buildEditUrl($ids);
	}
		
	/**
	 * __call 
	 * 
	 * @param mixed $method 
	 * @param mixed $arguments 
	 * @access public
	 * @return void
	 */
	public function __call($method, $arguments)
	{
		if( substr($method, 0, 2) == 'is' )
		{
			$property = substr($method, 2);
			$property = strtolower($property[0]).substr($property, 1);
			
			if( property_exists($this, $property) )
			{
				return $this->$property;
			} else {
				throw new Exception("$property does not exist on object ".get_class($this));
			}
		}
		else if( substr($method, 0, 3) == 'get' )
		{
			$property = substr($method, 3);
			$property = strtolower($property[0]).substr($property, 1);	
			
			if( property_exists($this, $property) )
			{
				return $this->$property;
			} else {
				throw new Exception("$property does not exist on object ".get_class($this));
			}
		}
		else if( strtolower(substr($method, 0, 3)) == 'set' )
		{
			$property = substr($method, 3);
			$property = strtolower($property[0]).substr($property, 1);	
			
			if( property_exists($this, $property) )
			{
				$value = array_key_exists(0, $arguments) ? $arguments[0]:'';
				$this->$property = $value;
			} else {
				throw new Exception("$property does not exist on object ".get_class($this));
			}
		} else {
			throw new Exception("Method '$method' does not exist on object ".get_class($this));
		}
	}
}
?>
