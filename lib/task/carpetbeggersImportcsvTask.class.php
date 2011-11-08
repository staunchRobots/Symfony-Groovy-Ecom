<?php

class carpetbeggersImportcsvTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
       new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'Source File (CSV)'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace        = 'carpetbeggers';
    $this->name             = 'import-csv';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [carpetbeggers:import-csv|INFO] task does things.
Call it with:

  [php symfony carpetbeggers:import-csv|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');
    
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
    
    sfContext::createInstance($configuration);
    
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $csv = new tmCsvReader(sfConfig::get('sf_data_dir') . '/' . $arguments['file'], array('header'=>true));
    
    $arr = $csv->toArray();

    foreach ($arr as $fields) 
    {
      $product = ProductTable::getInstance()->findOneByName($fields['name']);
      
      if ($product)
      {
        $product->setLength($fields['length']);
        $product->setWidth($fields['width']);
        $product->setPile($fields['pile']);
        $product->setFloor($fields['floor']);
        $product->setPrice('800');
        
        $product->save();
        
        echo ".";
      }
    }
  }
}
