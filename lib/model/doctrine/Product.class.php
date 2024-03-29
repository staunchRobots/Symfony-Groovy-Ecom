<?php

/**
 * Product
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    carpetbeggers
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Product extends BaseProduct
{
  public function getCategoryIds()
  {
    $ids = array();
    
    foreach ($this->getCategories() as $category) 
    {
      $ids[] = $category->getId();
    }
    
    return $ids;
  }
  
  public function preSave($event)
  {
    if ($this->isFurniture())
    {
      $required = array('price','photo');
    }
    else 
    {
      $required = array('price', 'length', 'width', 'photo', 'pile', 'floor');
    }
    

    $valid = true;

    foreach ($required as $field)
    {
      if (!isset($this[$field]) || empty($this[$field]))
      {
        $valid = false;
      }
    }

    if ($valid && ($this->get('status') == 'incomplete'))
    {
      $this->setStatus('complete');
      $this->setIsPublished(true);
    }
    
    parent::preSave($event);
  }

  public function updatePhoto()
  {
    if (!is_file(sfConfig::get('sf_upload_dir') . '/products/' . $this->getPhoto()))
    {
      return false;
    }

    $dimensions = getimagesize(sfConfig::get('sf_upload_dir') . '/products/' . $this->getPhoto());

    $this->setPhotoX1(0);
    $this->setPhotoY1(0);
    $this->setPhotoX2($dimensions[0]);
    $this->setPhotoY2($dimensions[1]);

    $this->save();

    $this->updateImage('photo');

    return true;
  }
  
  public function isFurniture()
  {
    $isFurniture = false;
    
    foreach ($this->getCategories() as $category) 
    {
      if ($category->getParent() == 'furniture') $isFurniture = true;
    }

    return $isFurniture;
  }
}
