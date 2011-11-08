<?php

require_once(sfConfig::get('sf_symfony_lib_dir') . '/yaml/sfYaml.php');

class seegnoI18N
{
  public static function getConfiguration()
  {
    if (is_file(sfConfig::get('sf_config_dir') . '/seegno/seegnoI18NPlugin.yml'))
    {
      $config = sfYAML::load(sfConfig::get('sf_config_dir') . '/seegno/seegnoI18NPlugin.yml');

      if (!isset($config['languages']))
      {
        throw new Exception("You must define the languages in which the project will be available");
      }
    }
    
    return $config;
  }

  public static function getI18nForms()
  {
    $config = self::getConfiguration();

    return array_keys($config['languages']);
  }
  
  public static function getChoices()
  {
    $choices = array();

    $config = self::getConfiguration();
    
    foreach ($config['languages'] as $slug => $options)
    {
      $choices[$slug] = $options['name'];
    }
    
    return $choices;
  }

  public static function getLabels()
  {
    $labels = array();
    $config = self::getConfiguration();
    
    foreach ($config['languages'] as $slug => $options)
    {
      $labels[$slug] = $options['name'];
    }
    
    return $labels;
  }
}
