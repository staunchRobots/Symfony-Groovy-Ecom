<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

class carpetbeggersUpdatethumbnailsTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'carpetbeggers';
    $this->name             = 'update-thumbnails';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [carpetbeggers:update-thumbnails|INFO] task does things.
Call it with:

  [php symfony carpetbeggers:update-thumbnails|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '164M');

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    foreach (Doctrine::getTable('Product')->findAll() as $product)
    {
      if ($product->getIsPublished()) continue;

      $product->updatePhoto();
    }
  }
}
