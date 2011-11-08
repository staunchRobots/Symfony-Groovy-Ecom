<?php

if (in_array('seegnoModal', sfConfig::get('sf_enabled_modules')))
{
  $this->dispatcher->connect('routing.load_configuration', array('seegnoRouting', 'addRouteForSeegnoModal'));
}