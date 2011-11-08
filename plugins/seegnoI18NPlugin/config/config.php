<?php

if (in_array('seegnoI18N', sfConfig::get('sf_enabled_modules', array())))
{
  $this->dispatcher->connect('routing.load_configuration', array('seegnoI18NRouting', 'listenToRoutingLoadConfigurationEvent'));
}
