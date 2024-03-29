<?php

class seegnoWidgetFormRichEditor extends sfWidgetFormTextarea {
  
  protected $_editor;
  protected $_finder;
  
  protected function configure($options = array(), $attributes = array())
  {
    $editorClass = 'CKEditor';
    $editorSettings = sfConfig::get('app_seegnoPages_ckeditor');
    $finderSettings = sfConfig::get('app_seegnoPages_ckfinder');
    
    if (!class_exists($editorClass))
    {
       throw new sfConfigurationException(sprintf('CKEditor class not found'));
    }
    
    $this->_editor = new $editorClass();
        
    if (!isset($editorSettings))
    {
       throw new sfConfigurationException(sprintf('CKEditor settings not found'));
    }
    
    $this->_editor->basePath = $editorSettings['path'];
        
    $this->_editor->returnOutput = true;
    
    if (isset($finderSettings))
    {
       if ($finderSettings['active'] == true)
       {
        $finderClass = 'CKFinder';
        if (!class_exists($finderClass))
        {
          throw new sfConfigurationException(sprintf('CKFinder class not found'));    
        }      
        $this->_finder = new $finderClass();
        $this->_finder->BasePath = $finderSettings['path'];
        $this->_finder->SetupCKEditorObject($this->_editor);  
       }
    }
    
    if (isset($options['jsoptions']))
    {
      $this->addOption('jsoptions', $options['jsoptions']);
    }

    if (isset($options['parameters']))
    {
      $this->addOption('parameters', $options['parameters']);
    }

    parent::configure($options, $attributes);
  }
  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $this->setEditorOptions('jsoptions', $value, $attributes, $errors);
    $this->setEditorOptions('parameters', $value, $attributes, $errors);

    return parent::render($name, $value, $attributes, $errors).$this->_editor->replace($name);
  }

  protected function setEditorOptions($name, $value = null, $attributes = array(), $errors = array())
  {
    $options = $this->getOption($name);
    
    if ($options)
    {
      foreach($options as $k => $v)
      {
        $this->_editor->config[$k] = $v;
      }
    }
  }

  public function getEditor()
  {
    return $this->_editor;
  }  
  public function getFinder()
  {
    return $this->_finder;
  }  
}
