<?php

require_once(sfConfig::get('sf_symfony_lib_dir') . '/yaml/sfYaml.php');

class seegnoI18NGuardSecurityUser extends sfGuardSecurityUser
{
  private $languages = null;
  private $breadcrumb = array();

  public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
  {
    $options['default_culture'] = null;

    parent::initialize($dispatcher, $storage, $options);

    $culture = $this->getAttribute('culture', false, 'seegnoI18N');

    $choices = seegnoI18N::getChoices();

    if ($culture && !isset($choices[$culture]))
    {
      $culture = false;
    }

    if (!$culture && !$this->getLanguage($culture))
    {
      if (!sfConfig::get('sf_debug'))
      {
        if (class_exists('ip2Country'))
        {
          $language = strtolower(ip2Country::getInstance()->getCountryCode());
        }
        else if (class_exists('ipToCountry'))
        {
          sfContext::getInstance()->getConfiguration()->loadHelpers('sfIpGeoloc');
          $language = strtolower(getCountryByIp());
        }
      }

      if (isset($language) and ($culture = $this->getCultureFromCountryCode($language)))
      {
        $this->setCulture($culture);
      }
      else
      {
        $this->setCulture($this->getDefaultCulture());
      }
    }
  }

  public function isAuthenticated()
  {
    return (parent::isAuthenticated() && $this->getGuardUser());
  }

  public function setCulture($culture)
  {
    if ($culture == 'none')
    {
      $this->setAttribute('culture', false, 'seegnoI18N');

      sfDoctrineRecord::setDefaultCulture(false);
    }
    else
    {
      parent::setCulture($culture);
      sfDoctrineRecord::setDefaultCulture($culture);
      $this->setAttribute('culture', $culture, 'seegnoI18N');
    }
  }

  public function getLanguage($culture = null)
  {
    if ($culture === null)
    {
      $culture = $this->getCulture();
    }

    $e = explode('_', $culture);

    return $e[0];
  }

  public function getLanguages()
  {
    if ($this->languages)
    {
      return $this->languages;
    }

    $config = sfYAML::load(sfConfig::get('sf_config_dir') . '/seegno/seegnoI18NPlugin.yml');
    $this->languages = $config['languages'];

    return $this->languages;
  }

  public function getDefaultCulture()
  {
    foreach ($this->getLanguages() as $culture => $language)
    {
      if (isset($language['default']) and $language['default'])
      {
        return $culture;
      }
    }

    throw new Exception("You must specify a default culture");
  }

  public function getCultureFromCountryCode($country)
  {
    foreach ($this->getLanguages() as $culture => $language)
    {
      if (isset($language['codes']) and is_array($language['codes']) and in_array($country, $language['codes']))
      {
        return $culture;
      }
    }

    return false;
  }

  public function getCultureFromSlug($slug)
  {
    foreach ($this->getLanguages() as $culture => $language)
    {
      if (isset($language['slug']) and (strtolower($slug) == strtolower($language['slug'])))
      {
        return $culture;
      }
    }

    return false;
  }

  public function getId()
  {
    if ($this->getGuardUser())
    {
      return $this->getGuardUser()->getId();
    }

    return false;
  }

  public function resetBreadcrumb()
  {
    $this->breadcrumb = array();

    return $this->breadcrumb;
  }

  public function addBreadcrumb($entry)
  {
    $this->breadcrumb[] = $entry;
  }

  public function getBreadcrumb()
  {
    return $this->breadcrumb;
  }
}
