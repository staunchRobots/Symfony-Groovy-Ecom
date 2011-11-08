<?php

class carpetbeggersUpdateslugsTask extends sfBaseTask
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
    $this->name             = 'update-slugs';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [carpetbeggers:update-slugs|INFO] task does things.
Call it with:

  [php symfony carpetbeggers:update-slugs|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    $products = ProductTable::getInstance()->findAll();

    foreach ($products as $product) 
    {
      $product->setSlug($product->getName());
      $product->save();
      echo "." . PHP_EOL;
    };
  }
}
