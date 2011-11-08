<?php

class seegnoTranslationsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
       new sfCommandArgument('app', sfCommandArgument::REQUIRED, 'Application'),
       new sfCommandArgument('language', sfCommandArgument::OPTIONAL, 'Language'),
    ));

    $this->addOptions(array(
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace        = 'seegno';
    $this->name             = 'translations';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [seegno:translations|INFO] task checks which I18N strings remain untranslated.
Call it with:

  [php symfony seegno:translations|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $pathToTranslationXMLFiles = sfConfig::get('sf_root_dir').'/apps/'.$arguments['app'].'/i18n';

    $myLang = null;
    if (isset($argv[1]))
    {
      $myLang = strtolower($argv[1]);
      echo 'Use '.$myLang . PHP_EOL;
    }

    $files = sfFinder::type('file')->name('*.xml')->in($pathToTranslationXMLFiles);
    
    if (empty($files)) echo 'No translation files found.' . PHP_EOL; return;    
    
    foreach($files as $key => $file) 
    {
      preg_match('/i18n\/(.*?)\/messages.xml/', $file, $lang);
      $lang = $lang[1];
      $languages[$lang] = 'missing';
  
      $xml = new SimpleXmlElement($file, null, true);
  
      $result = $xml->xpath('/xliff/file/body/trans-unit');

      while(list( , $node) = each($result)) 
      {
        $source = (string) $node->source;
        $target = (string) $node->target;

        if (!isset($translations[$source])) 
        {
          $translations[$source] = $languages;
        }

        if ($source == $target || empty($target)) 
        {
          $translations[$source][$lang] = 'not translated';
        } else {
          unset($translations[$source][$lang]);
        }
      }
    }

    $counter = array_fill_keys(array_keys($languages), '');

    foreach($translations as $source => $translation) 
    {
      if (empty($translation)) 
      {
        continue;
      }

      if ($myLang === null) 
      {
        echo $source . PHP_EOL;
        foreach($translation as $lang => $target) {
          echo sprintf('  %s: %s', $lang, $target) . PHP_EOL;
          $counter[$lang]++;
        }
      } 
      else 
      {
        if (!isset($translation[$myLang])) 
        {
          continue;
        }

        echo $source . PHP_EOL;
        echo sprintf('  %s: %s', $myLang, $translation[$myLang]) . PHP_EOL;
        $counter[$myLang]++;
      }
      echo PHP_EOL;
    }

    echo 'Missing: ' . PHP_EOL;
    foreach($counter as $lang => $number) {
      echo sprintf('%s: %u', $lang, $number) . PHP_EOL;
    }
  }
}