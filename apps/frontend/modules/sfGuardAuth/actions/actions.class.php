<?php
require_once(sfConfig::get('sf_plugins_dir').'/sfDoctrineGuardPlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions.class.php');

class sfGuardAuthActions extends BasesfGuardAuthActions
{
  public function executeSignout($request)
  {
    $this->getUser()->signOut();

    $this->redirect('@homepage');
  }

  public function executeSignin($request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      if ($request->isXmlHttpRequest())
      {
        return $this->renderPartial('seegnoModal/reload', array('url' => '@homepage'));
      }
      else
      {
        return $this->redirect('@homepage');
      }
    }

    $this->form = new sfGuardFormSignin;

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('signin'));

      if ($this->form->isValid())
      {
        $values = $this->form->getValues();

        $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);

        $route = sfConfig::get('app_sf_guard_plugin_success_signin_url', $this->getUser()->getReferer($request->getReferer()));
        
        $homepage = sfConfig::get('sf_debug') ? $request->getUriPrefix() . $_SERVER['SCRIPT_NAME'] . '/' : $request->getUriPrefix() . '/';

        if ($route == $homepage)
        {
          return $this->renderPartial('seegnoModal/reload', array('url' => '@homepage'));
        }

        return $this->renderPartial('seegnoModal/reload');
      }
    }
    else
    {
      $this->getUser()->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

      $module = sfConfig::get('sf_login_module');

      if ($this->getModuleName() != $module)
      {
        return $this->redirect($module . '/' . sfConfig::get('sf_login_action'));
      }
    }

    if ($request->isXmlHttpRequest())
    {
      return 'Ajax';
    }
    else
    {
      $this->setLayout('layout');
    }
  }
}