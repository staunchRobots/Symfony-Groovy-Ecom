<?php

class seegnoRouting 
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();
  }

  static public function addRouteForSeegnoModal(sfEvent $event)
  {
      $event->getSubject()->prependRoute('seegnoModal', new sfRoute('/js/custom/modal.js',
        array('module'    => 'seegnoModal',
              'action'    => 'show',
              'sf_format' => 'js'),
        array('file' => '.*'))
      );
  }
}
