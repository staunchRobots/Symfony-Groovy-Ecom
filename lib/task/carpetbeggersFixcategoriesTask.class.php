<?php

class carpetbeggersFixcategoriesTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'carpetbeggers';
    $this->name             = 'fix-categories';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [carpetbeggers:fix-categories|INFO] task does things.
Call it with:

  [php symfony carpetbeggers:fix-categories|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    $products = Doctrine::getTable('Product')->findAll();
    $category = Doctrine::getTable('Category')->findOneBySlug('uncategorized-rugs');

    foreach ($products as $product) 
    {
      $productCategory = Doctrine::getTable('ProductCategory')->find(array($product->getId(), $category->getId()));
      
      if (!$productCategory)
      {
        $productCategory = new ProductCategory;
      }
      
      $productCategory->setProductId($product->getId());
      $productCategory->setCategoryId($category->getId());
      $productCategory->save();
    }
  }
}
