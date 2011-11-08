<?php

class seegnoI18NRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $routing = $event->getSubject();

    // preprend our routes
    $routing->prependRoute('seegno_i18n_culture', new sfRoute('/language/:culture', array('module' => 'seegnoI18N', 'action' => 'changeLanguage')));
  }
}
