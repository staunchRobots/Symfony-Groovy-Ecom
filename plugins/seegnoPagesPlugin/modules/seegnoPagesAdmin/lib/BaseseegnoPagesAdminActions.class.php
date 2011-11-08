<?php

require_once dirname(__FILE__).'/../lib/seegnoPagesAdminGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/seegnoPagesAdminGeneratorHelper.class.php';

class BaseseegnoPagesAdminActions extends autoSeegnoPagesAdminActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $root = Doctrine_Core::getTable('Page')->getTree()->fetchRoots();

    if (!count($root))
    {
      $meta = sfYaml::load(sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . 'view.yml');
      
      if (!isset($meta['default']['metas']['title']))
      {
        throw new Exception("You must define a title in view.yml");
      }
      
      $title = $meta['default']['metas']['title'];

      $root = new Page;
      $root->setTitle($title);
      $root->setTemplate('default');
      $root->setMenu('none');
      $root->setIsPublished(true);

      $root = Doctrine_Core::getTable('Page')->getTree()->createRoot($root);
      
      $root->setSlug(NULL);
      $root->save();
    }
  }

  public function executeUpdate(sfWebRequest $request)
  {
    sfConfig::set('sf_web_debug', false);

    $data = $request->getParameter('page');

    $this->page = Doctrine::getTable('Page')->find($data['id']);
    $this->form = $this->configuration->getForm($this->page);
  
    $this->processForm($request, $this->form);
  
    $this->setTemplate('edit');
  }

  public function executeUrl(sfWebRequest $request)
  {
    $success = false;

    $key = $request->getParameter('key');
    
    $slug = seegno::urlify($key);

    if ($request->hasParameter('id'))
    {
      $currentPage = Doctrine::getTable('Page')->createQuery('p')->where('p.id = ?', array($request->getParameter('id')))->fetchOne();

      // We can use the same slug as before
      if ($currentPage)
      {
        if ($currentPage->getSlug() == $slug)
        {
          $success = true;
        }

        $parentSlug = $currentPage->getNode()->getParent()->getSlug();

        $slug = ($parentSlug && $parentSlug != $slug) ? $parentSlug . '/' . $slug : $slug;
      }
    }
    
    // Check what other slugs are not in use
   
    $slugInUse = Doctrine::getTable('Page')->findOneBySlug($slug);
    
    if (!$slugInUse)
    {
      $success = true;
    }

    return $this->renderText(stripslashes(json_encode(array('success' => $success, 'slug' => $slug))));
  }
  
  public function executeEmbedForm($request)
  {
    $this->page = Doctrine::getTable('Page')->findOneById($request->getParameter('id'));
    $this->form = $this->configuration->getForm($this->page);
    
    $this->configuration = new seegnoPagesAdminGeneratorConfiguration();
    $this->helper = new seegnoPagesAdminGeneratorHelper();

    return $this->renderPartial('embed_form');
  }

  public function getTree($rootId = null)
  {
    $tree = Doctrine_Core::getTable('Page')->getTree();

    return $tree->fetchTree();
  }

  public function executeAdd_child()
  {
    $parent_id = $this->getRequestParameter('parent_id');

    $model = $this->getRequestParameter('model');
    $field = $this->getRequestParameter('field');
    $value = $this->getRequestParameter('value');
    $record = Doctrine_Core::getTable($model)->find($parent_id);

    $child = new $model;
    $child->set($field, $value);
    $record->getNode()->addChild($child);
    
    $this->json = json_encode($child->toArray());
    
    $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    $this->setTemplate('json');
  }
  
  public function executeEdit_field()
  {
    $id = $this->getRequestParameter('id');
    $model = $this->getRequestParameter('model');
    $field = $this->getRequestParameter('field');
    $value = $this->getRequestParameter('value');

    $record = Doctrine_Core::getTable($model)->find($id);
    $record->set($field, $value);
    $record->save();

    $this->json = json_encode($record->toArray());
    $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    $this->setTemplate('json');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $id = $this->getRequestParameter('id');

    $record = Doctrine_Core::getTable('Page')->find($id);
    $record->delete();

    $this->redirect('@seegnoPagesAdmin');
  }

  public function executeMove()
  {
    $id = $this->getRequestParameter('id');
    $to_id = $this->getRequestParameter('to_id');
    $model = $this->getRequestParameter('model');
    $movetype = $this->getRequestParameter('movetype');
    
    $record = Doctrine_Core::getTable($model)->find($id);
    $dest = Doctrine_Core::getTable($model)->find($to_id);
    
    if( $movetype == 'inside' )
    {
      //$prev = $record->getNode()->getPrevSibling();
      $record->getNode()->moveAsLastChildOf($dest);
    }
    else if( $movetype == 'after' )
    {
      $record->getNode()->moveAsNextSiblingOf($dest);
    }
    
    else if( $movetype == 'before' )
    {
      //$next = $record->getNode()->getNextSibling();
      $record->getNode()->moveAsPrevSiblingOf($dest);
    }

    $record->updateSlug();
    $this->json = json_encode($record->toArray());
    $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    $this->setTemplate('json');
  }
}
