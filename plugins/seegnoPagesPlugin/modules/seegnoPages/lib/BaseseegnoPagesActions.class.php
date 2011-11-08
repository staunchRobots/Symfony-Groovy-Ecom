<?php

class BaseseegnoPagesActions extends sfActions
{
  public function setTitle($value)
  {
    $meta = sfYaml::load(sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . 'view.yml');

    if (!isset($meta['default']['metas']['title']))
    {
      $title = $value;
    }
    else
    {
      $title = $meta['default']['metas']['title'];

      if ($value)
      {
        $app = sfYaml::load(sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . 'app.yml');

        if (isset($app['all']['seegnoPages']['prepend_title']) && $app['all']['seegnoPages']['prepend_title'] == true)
        {
          $title =  $value . ' » ' . $title;
        }
        else
        {
          $title .= ' » ' . $value;
        }
      }
    }

    $this->getResponse()->setTitle($title);
  }

  public function executeSource(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->isSuperAdmin() || $this->getUser()->hasCredential('edit_pages'));

    $value = $this->getValue($request->getParameter('id'), $this->getRequestParameter('slug'));
    
    return $this->renderText($value);
  }
  

  public function executeShow(sfWebRequest $request)
  {
    if ((stripos($request->getParameter('slug'), 'contact') !== false) && in_array('seegnoContact', sfConfig::get('sf_enabled_modules')))
    {
      $this->setupSeegnoContact($request);
    }

    $slug  = $request->getParameter('slug');
    if (is_numeric($slug))
      $this->page = Doctrine::getTable('Page')->find($slug);
    else
      $this->page = Doctrine::getTable('Page')->retrieveBySlug($slug);
    
    $this->forward404Unless($this->page);

    $culture = $this->getUser()->getCulture();

    if (($this->page->getLang() != $culture) && array_key_exists($culture, $this->getUser()->getLanguages()))
    {
      $this->page->Translation[$culture]->title = $this->page->getTitle();
      $this->page->Translation[$culture]->lang = $culture;
      $this->page->Translation[$culture]->save();
    }
    
    if ($request->getParameter('edit') && !$this->getUser()->isAuthenticated())
    {
      $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    }
    
    if ($this->page->getDescription())
    {
      $this->getResponse()->addMeta('description', $this->page->getDescription());
    }

    if ($this->page->getKeywords())
    {
      $this->getResponse()->addMeta('keywords', $this->page->getKeywords());
    }

    if ($request->getParameter('edit') || !$this->page->getIsPublished())
    {
      if (!($this->getUser()->isSuperAdmin() || $this->getUser()->hasCredential('edit_pages')))
      {
        $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
      }
    }

    if (method_exists('myUser', 'addBreadcrumb'))
    {
      foreach ($this->page->getNode()->getAncestors() as $node)
      {
        if ($node->getLevel() > 0)
        {
          $this->getUser()->addBreadcrumb(array('name' => $node->getTitle(), 'link' => '@page?slug=' . $node->getSlug()));
        }
      }

      $this->getUser()->addBreadcrumb(array('name' => $this->page->getTitle(), 'link' => '@page?slug=' . $this->page->getSlug()));
    }

    if ($this->page->getModule() && $this->page->getAction())
    {
      $request->setParameter('action', $this->page->getAction());

      $this->forward($this->page->getModule(), $this->page->getAction());
    }

    $this->setTemplate($this->page->getTemplate());
    $this->setTitle($this->page->getTitle());

    return 'Template';
  }

  public function executeUpdate(sfWebRequest $request)
  {  
    $value  = $request->getParameter('value');
    $class  = $request->getParameter('className', '');
    $key    = $request->getParameter('key', '');
    $field  = $request->getParameter('field', '');

    if ($class && $key && $field)
    {
      $object = Doctrine::getTable($class)->findOneById($key);
      if ($object)
      {
        $object->set($field, $value);
        $object->save();
      }
      else
        $value  = '';
    }

    $value = $this->updateSlot($value, $request);

    return $this->renderText($value);
  }

  public function executeWidgets(sfWebRequest $request)
  {
    $currentWidget = $this->getValue($request->getParameter('id'), $this->getRequestParameter('slug'));
    
    if (stripos($currentWidget, '='))
    {
      $slotParams = self::explode_with_key($currentWidget);
      
      $currentWidgetName = $slotParams['id'];
    } 
    else
    {
      $currentWidgetName = $currentWidget;
    }
    
    $widgets = $this->getWidgets($currentWidgetName);
    
    return $this->renderText(json_encode($widgets));
  }
  

  protected function updateSlot($value, sfWebRequest $request)
  {
    $page = Doctrine::getTable('Page')->retrieveBySlug($this->getRequestParameter('slug'));
    
    if ($this->hasRequestParameter('params') && $request->getParameter('params'))
    {
      $params = $request->getParameter('params') ? $request->getParameter('params') : array();

      $params['id'] = $value;

      if (is_array($params))
      {
        $paramString = self::implode_with_key($params);

        $value = $paramString;
      }
    }
      
    if ($page && ($this->getRequestParameter('slug') != 'custom'))
    {      
      $slot = $page->setSlot($request->getParameter('id'), $value , $request->getParameter('type'));
    }
    else
    {      
      $slot = Doctrine::getTable('Slot')->findOneByName($request->getParameter('id'));
  
      if (!$slot)
      {
        $slot = new Slot;
        
        $slot->setName($request->getParameter('id'));
        $slot->setValue($value);
        $slot->setType($request->getParameter('type'));
        
        $slot->save();
      }
      else
      {
        $slot->setValue($value);
        
        if ($request->getParameter('type'))
        {
          $slot->setType($request->getParameter('type'));
        }

        $slot->save();
      }
    }   
    
    return $value;
  }

 protected function getValue($name, $slug)
 {
   if ($slug != 'custom')
   {
     $page = Doctrine::getTable('Page')->retrieveBySlug($slug);
    
     $this->forward404Unless($page);
    
     $slot = $page->getSlot($name);
   }
   else
   {
     $slot = Doctrine::getTable('Slot')->findOneByName($name);  
   }
      
   if ($slot)
   {
     $value = $slot->getValue();  
   }
   else
   {
     $value = '';
   }
 
   return $value;
  }

 protected function getWidgets($selected = null, $module = null)
 { 
   
   if (!isset($module)) $module = 'widgets';
   
   $config = sfConfig::get("app_seegnoPages_widgets");
  
   $path = '../apps/frontend/modules/' . $module . '/templates';
   $mask= '*.php';
   
   $selectedKey = '';
   
   $widgets = array();
   
   $widgets['none']['name'] = sfContext::getInstance()->getI18N()->__('None');
  
   static $dir = array();
   
     if ( !isset($dir[$path])) { 
         $dir[$path] = scandir($path); 
     }
     foreach ($dir[$path] as $i=>$entry) {
       if ($entry !='.' && $entry !='..' && fnmatch($mask, $entry)) 
        { 
          
         $entry = str_replace('.php', '', $entry);
         $entry = str_replace('_', '', $entry);
         
         if (isset($config[$entry]['name']))
         {
           $widgets[$entry]['name'] = $config[$entry]['name']; 
         }
         else
         {
           $widgets[$entry]['name'] = $entry;
         }
       
         
         if (isset($selected) && $selected != null && $entry == $selected ) $selectedKey = $entry;
       }
     }
     
     $widgets['selected'] = $selectedKey;     

     if (isset($config['items']))
     {
       foreach ($config['items'] as $key => $item) 
       {
         foreach ($item as $index => $value) 
         {
           $widgets[$key][$index] = $value;
         }
       }
     }

   return $widgets;
 }
 
 private function setupSeegnoContact($request)
 { 
   $seegnoContactConfig = sfYAML::load(sfConfig::get('sf_config_dir') . '/seegno/seegnoContactPlugin.yml');
   
   if (isset($seegnoContactConfig['seegnoPages']) and $seegnoContactConfig['seegnoPages']['enabled'])
   {
     $pageTable = Doctrine::getTable('Page');
     
     $slug = isset($seegnoContactConfig['seegnoPages']['slug']) ? $seegnoContactConfig['seegnoPages']['slug'] : 'contact';

     $page = $pageTable->retrieveBySlug($slug);

     if ($page)
     {
       return;
     }
     else 
     {
       $pageTree = $pageTable->getTree();
       $roots = $pageTree->fetchRoots();

       if (!count($roots))
       {
         throw new Exception("No roots available.", 1);

       }
       else 
       {
         $root = $roots->getFirst();

         $page = new Page;

         $page->setTemplate('Default');
         $page->setIsPublished(1);
         $page->setModule('seegnoContact');
         $page->setAction('index');
         $page->setMenu('main');
         $page->setSlug('contact');
         $page->setTitle('Contact');

         $pageTree->getNode()->insertAsLastChildOf($root);
       } 
     }
   }
 }

 protected function implode_with_key($assoc, $inglue = '=', $outglue = ',')
 {
  $return = '';
  
  foreach ($assoc as $tk => $tv)
  {
      $return .= $outglue . $tk . $inglue . $tv;
  }

  return substr($return,strlen($outglue));
 }

 protected function explode_with_key($str, $inglue = "=", $outglue = ',') 
 {
  $arr = array();
    
  foreach (explode($outglue, $str) as $pair)
  {           
    $k2v = explode($inglue, $pair);           
    $arr[$k2v[0]] = $k2v[1];           
  }
  
  return $arr;     
 }
}
