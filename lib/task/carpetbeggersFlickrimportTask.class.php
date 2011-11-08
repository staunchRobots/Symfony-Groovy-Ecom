<?php

class carpetbeggersFlickrimportTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
       new sfCommandArgument('userId', sfCommandArgument::REQUIRED, 'User Id'),
       new sfCommandArgument('setId', sfCommandArgument::REQUIRED, 'Set Id'),
       new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'Unique Id'),
       new sfCommandArgument('category', sfCommandArgument::REQUIRED, 'Category Id'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'carpetbeggers';
    $this->name             = 'flickr-import';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [carpetbeggers:flickr-import|INFO] task does things.
Call it with:

  [php symfony carpetbeggers:flickr-import|INFO]
EOF;
  }

protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '512M');

    $exec = 'touch ' . sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $arguments['id'] . '.lock';

    system($exec);
    
    require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');
    
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
    
    sfContext::createInstance($configuration);
    
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    $category = Doctrine::getTable('Category')->find($arguments['category']);
    
    $flickr = new sfFlickr;
    
    $setCount = $flickr->getPhotoset($arguments['setId'])->getPhotoCount();
    
    $photos = $flickr->getPhotoset($arguments['setId'])->getPhotoList()->getPhotos();

    $i = 0;
    
    foreach ($photos as $photo) 
    { 
      // if ($i == 1) break;
      
      $product = ProductTable::getInstance()->findOneByFlickrId($photo->getId());
      
      $i++;
      
      $sizes = $photo->getSizes();
      $filehash = sha1($photo->getId());
      $extension = $sizes['o']['type'];
      
      $filename1 = $filehash.'.'.$extension;
      $filename2 = $filehash.'_original.'.$extension;

      $photo->saveAs(sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $filename1, 'o');
      $photo->saveAs(sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $filename2, 'o');
      
	  if (!$product)
      {
        $product = new Product;
      }
      
      $product->setName($photo->getTitle());
      $product->setFlickrId($photo->getId());
      $product->setNotes($photo->getDescription());
      $product->setCreatedAt($photo->getPostedDate());
      $product->setStatus('incomplete');

      $dimensions = @getimagesize(sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $filename1);

      $product->setPhoto($filename1);
      $product->updatePhoto();
      
      $productCategory = Doctrine::getTable('ProductCategory')->find(array($product->getId(), $category->getId()));

      if (!$productCategory)
      {
        $productCategory = new ProductCategory;
      }

      $productCategory->setProductId($product->getId());
      $productCategory->setCategoryId($category->getId());
      $productCategory->save();

      echo ".";
    }
    
    unlink('data/' . $arguments['id'] . '.lock');
  }
}
