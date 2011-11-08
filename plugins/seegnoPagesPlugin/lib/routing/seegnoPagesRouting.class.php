<?php

class seegnoPagesRouting
{
  static public function addRouteForSeegnoPagesAdmin(sfEvent $event)
  {
    $event->getSubject()->prependRoute('urlify', new sfRoute('/ajax/urlify', array('module' => 'seegnoPagesAdmin', 'action' => 'url')));
    $event->getSubject()->prependRoute('seegnoPagesAdmin_action', new sfRoute('/admin/pages/:action/*', array('module' => 'seegnoPagesAdmin')));

    $event->getSubject()->prependRoute('seegnoPagesAdmin', new sfDoctrineRouteCollection(array(
      'name'                => 'seegnoPagesAdmin',
      'model'               => 'Page',
      'module'              => 'seegnoPagesAdmin',
      'prefix_path'         => 'admin/pages',
      'with_wildcard_routes' => true,
      'collection_actions'  => array('filter' => 'post', 'batch' => 'post'),
      'requirements'        => array(),
    )));
  }
}