<?php

if (in_array('seegnoPagesAdmin', sfConfig::get('sf_enabled_modules')))
{
  $this->dispatcher->connect('routing.load_configuration', array('seegnoPagesRouting', 'addRouteForSeegnoPagesAdmin'));
}
