<?php

class BaseseegnoI18NActions extends sfActions
{
  public function _prefix($a, $b)
  {
    $i = 0;
    $ret = '';

    if (isset($a) and isset($b))
    {
      while ((isset($a[$i]) and isset($b[$i])) and $a[$i] == $b[$i])
      {
        $ret .= $a[$i++];
      }
    }

    return $ret;
  }

  public function executeChangeLanguage(sfWebRequest $request)
  {
    if ($request->getParameter('culture') == 'none')
    {
      $this->getUser()->setCulture(false);

      $this->redirect('@homepage');
    }

    $languages = $this->getUser()->getLanguages();

    $culture = $this->getUser()->getCultureFromSlug($this->getRequestParameter('culture', 'english'));

    $this->forward404Unless($culture);

    $this->getUser()->setCulture($culture);

    $url = '';

    if ($request->getReferer())
    {
      if (sfConfig::get('sf_debug') or !sfConfig::get('sf_no_script_name'))
      {
        $url = str_replace($this->_prefix($request->getReferer(), 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '/'), '', $request->getReferer());
      }
      else
      {
        $url = str_replace($this->_prefix($request->getReferer(), 'http://' . $_SERVER['HTTP_HOST'] . '/'), '', $request->getReferer());
      }
    }

    if (!$url)
    {
      $url = '/';
    }

    $route = sfContext::getInstance()->getRouting()->parse($url);

    foreach ($route as $parameter => $value)
    {
      if (!in_array($parameter, array('_sf_route')))
      {
        $parameters[$parameter] = $value;
      }
    }

    if (isset($parameters['sf_culture']))
    {
      $parameters['sf_culture'] = $culture;
    }

    $route = str_replace('%2F', '/', $route['_sf_route']->generate($parameters));

    if (!sfConfig::get('sf_no_script_name') || sfConfig::get('sf_debug'))
    {
      $this->redirect($_SERVER['SCRIPT_NAME']. $route);
    }

    $this->redirect($request->getRelativeUrlRoot() . $route);
  }
}
