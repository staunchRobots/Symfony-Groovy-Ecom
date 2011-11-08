<?php

abstract class PluginPageForm extends BasePageForm
{
  public function setup()
  {
    parent::setup();

    $this->useFields(array('template', 'slug', 'is_published', 'menu'));

    if (!class_exists('seegnoI18N'))
    {
      throw new Exception("You must download seegnoI18N");
    }

    // Embedding I18N Forms
    foreach (sfContext::getInstance()->getUser()->getLanguages() as $culture => $params)
    {
      $this->embedI18N(array($culture));
      $this->widgetSchema[$culture]->setLabel(' ');
    }

    // Menu
    $choices = self::getMenus();

    $this->widgetSchema['menu'] = new sfWidgetFormChoice(array('expanded' => false, 'multiple' => false, 'choices' => $choices));

    if (!$this->isNew())
    {
      $menu = $this->getObject()->getMenu();

      if (isset($choices['Available Menus']) && array_key_exists($menu, $choices['Available Menus']))
      {
        // Default menu is ok, skip it
      } 
      elseif ($menu == 'none')
      {
        $this->getObject()->setMenu('none');
      }
    }
    
    // Template
    if (!$templates = sfConfig::get('app_seegnoPages_templates'))
    {
      $templates = array('default' => 'Default Template');
    }
    
    $this->widgetSchema['slug']->setLabel('Slug');
    $this->widgetSchema['template'] = new sfWidgetFormChoice(array('choices' => $templates));

    // Parent
    $this->widgetSchema['parent'] = new sfWidgetFormDoctrineChoice(array('model' => 'Page', 'add_empty' => true, 'method' => 'getTitleWithLevel', 'order_by' => array('root_id, lft, level', 'DESC')));
    $this->validatorSchema['parent'] = new sfValidatorPass;

    if ($this->getObject()->getNode()->getParent())
    {
      $this->widgetSchema['parent']->setDefault($this->getObject()->getNode()->getParent()->getId());
    }

    $this->validatorSchema['url'] = new sfValidatorPass;

    // Field Order
    $this->widgetSchema->moveField('parent', sfWidgetFormSchema::AFTER, 'template');

    $path = explode('/', $this->getObject()->getSlug());

    $this->getObject()->setSlug(end($path));
  }

  public function doSave($con = null)
  {
    parent::doSave($con);

    $values = $this->getValues();

    // Menus
    if ($values['menu'] == null) 
    {
      $this->getObject()->setMenu('none');
    }
    elseif ($values['menu'] == 'parent')
    {
      if ($this->getObject()->getNode()->hasParent())
      {
        $this->getObject()->setMenu($this->getObject()->getNode()->getParent()->getSlug());
      }
    }

    $this->getObject()->save();
  }

  private function getMenus()
  {
    $items = sfYaml::load(sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . 'app.yml');

    $choices = array();

    if (isset($items['all']['seegnoMenu']))
    {
      $choices['Default']['none'] = 'None';

      // Populate menu entries with seegnoMenu indexes
      foreach ($items['all']['seegnoMenu'] as $key => $item) 
      {
        $choices['Available Menus'][$key] = ucfirst($key);
      }
    }
    
    return $choices;
  }
}