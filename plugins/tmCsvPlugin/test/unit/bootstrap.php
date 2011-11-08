<?php
require_once(dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('tajo', 'test', true);
include($configuration->getSymfonyLibDir().'/vendor/lime/lime.php');
require_once $configuration->getSymfonyLibDir().'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();
sfContext::createInstance($configuration);
