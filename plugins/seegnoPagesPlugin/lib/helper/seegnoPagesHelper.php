<?php

if (is_editor())
{
  use_javascript('/seegnoPlugin/ckeditor/ckeditor.js');
  use_javascript('/seegnoPlugin/ckfinder/ckfinder.js');
  use_javascript('/seegnoPlugin/ckeditor/adapters/jquery.js');
  use_javascript('/seegnoPlugin/js/plugins/jquery.eip.js');
  use_javascript('/seegnoPlugin/js/plugins/jquery.spinner.js');
}

if (sfConfig::get('app_seegnoPages_stylesheet') != 'none')
{
  $stylesheet = sfConfig::get('app_seegnoPages_stylesheet') ? sfConfig::get('app_seegnoPages_stylesheet') : '/seegnoPagesPlugin/css/main.css';
  use_stylesheet($stylesheet);
}

use_helper('Text');

function get_nested_set_manager($model, $field)
{
  sfContext::getInstance()->getResponse()->addJavascript('/seegnoPagesPlugin/js/tree/jquery.tree.js');

  return get_component('seegnoPagesAdmin', 'manager', array('model' => $model, 'field' => $field));
}


function is_editor()
{
  if (!sfContext::getInstance()->getRequest()->getParameter('edit'))
    return false;

  if (sfContext::getInstance()->getUser()->isSuperAdmin() || sfContext::getInstance()->getUser()->hasCredential('edit_pages'))
    return true;

  return false;
}


function _parse_params($str, $inglue = "=", $outglue = ',') 
{
  $arr = array();
    
  foreach (explode($outglue, $str) as $pair)
  {           
    $k2v = explode($inglue, $pair);           
    $arr[$k2v[0]] = $k2v[1];           
  }
  
  return $arr;     
}


function seegno_has_simple_slot($name)
{
  $slot = Doctrine::getTable('Slot')->findOneByName($name);

  return $slot ? ((strlen($slot->getValue()) > 0) || is_editor()) : is_editor();
}


function seegno_has_slot($page, $name)
{
  if (!is_object($page))
  {
    $page = Doctrine::getTable('Page')->retrieveByAction($page);
  }

  if ($page instanceof Page)
  {
    $slot = $page->getSlot($name);
    return $slot ? ((strlen($slot->getValue()) > 0) || is_editor()) : is_editor();
  }

  die('N/I');
}


function seegno_slot_value($name, $page = false)
{
  $slot = ($page) ? ((is_object($page)) ? $page->getSlot($name) : Doctrine::getTable('Page')->retrieveByAction($page)->getSlot($name)) : Doctrine::getTable('Slot')->findOneByName($name);
  
  return $slot ? $slot->getValue() : false;
}


function render_widget($slot, $options)
{
  $config = sfConfig::get("app_seegnoPages_widgets");
  
  if (isset($config['module'])) 
  {
   $module = $config['module'];
  }
  else
  {
   $module = 'widgets';
  }
  
  $value = $slot->getValue(ESC_RAW);
  
  $params = array('id' => $slot->getName());

  if (is_editor())
  {    
    $params['class'] = 'eip ' . strtolower($slot->getType());
  }
  
  if (isset($options['class']))
  {
    $params['class'] = isset($params['class']) ? $params['class'] . ' ' . $options['class'] : $options['class'];
  }
  
  if (!$value && !is_editor())
  {
    return;
  }
  else
    {
      if ((!$value && is_editor()) || $value == 'none')
      {
        return render_slot($slot, $options);
      }
    }
    
  if (stripos($value, '='))
  {
    $slotParams = _parse_params($value);
    
    $widgetName = $slotParams['id'];
    
    $widgetParams = $slotParams;
    
    unset($widgetParams['id']);
  }
  else
  {
    $widgetName = $value;   
  }
  
  $context = sfContext::getInstance();

  $controller = $context->getController();

  if (!$controller->componentExists($module, $widgetName))
  {
    throw new sfControllerException(sprintf('Component "%s" does not exist in module "%s".', $widgetName, $module));
  }
  
  return content_tag('div', get_component($module, $widgetName, ((isset($widgetParams) && count($widgetParams)) ? array('params' => $widgetParams) : array())), $params);
}


function seegno_object_slot($object, $field, $type = 'Text', $options)
{
  $value  = $object->get($field, ESC_RAW);
  $name   = get_class($object->getRawValue()) . "_" . $object->getId() . "_" . $field;
  
  if (!isset($options['attr']))
    $options['attr']  = array();
  $options['attr']  = array( 'data-class' => get_class($object->getRawValue()), 'data-key' => $object->getId(), 'data-field' => $field );

  return render_raw($name, $value, $type, $options);
}


function render_slot($slot, $options)
{
  return render_raw($slot->getName(), $slot->getValue(ESC_RAW), $slot->getType(), $options);
}


function render_raw($name, $value, $type, $options)
{
  if (!isset($options['tag']))
  {
    $options['tag'] = 'div';
  }

  if (!is_editor() && isset($options['truncate']))
  {
    use_helper('Text');
    
    $length = 30;
    $delimiter = '...';
    $lastspace = false;
    
    if (isset($options['truncate']['length']) && is_int($options['truncate']['length']))
    {
      $length = $options['truncate']['length'];
    }
    
    if (isset($options['truncate']['delimiter']))
    {
      $delimiter = $options['truncate']['delimiter'];
    }
    
    if (isset($options['truncate']['lastspace']))
    {
     $lastspace = $options['truncate']['lastspace'];
    }
    
    $value = truncate_text($value, $length, $delimiter, $lastspace);
  }

  if ((!$value || ($value == 'none' && $type == 'Widget')) && is_editor())
  {
    $value = '[click to edit]';
  }
  else 
  { 
   if ((!$value || ($value == 'none' && $type == 'Widget')) && !is_editor())
   {
     $value = '';
   }     
  }

  if (!isset($options['attr']))
    $options['attr']  = array();

  $attrs  = $options['attr'];

  $attrs['id'] = $name;

  if (is_editor())
  {    
    $attrs['class'] = 'eip ' . strtolower($type);
    
    if (isset($options['config']))
    {
      $attrs['class'] .= ' ' . $options['config'];
    }

    if (isset($options['modal']) && $options['modal'])
    {
      $attrs['class'] .= ' modal';
    }
    if (isset($options['source']) && $options['source'])
    {
      $attrs['class'] .= ' source';
    }   
    if (isset($options['ckfinder']) && $options['ckfinder'])
    {
      $attrs['class'] .= ' ckfinder';
    }
  }

  if (isset($options['class']))
  {
    $attrs['class'] = isset($attrs['class']) ? $attrs['class'] . ' ' . $options['class'] : $options['class'];
  }

  if (isset($options['link_text']) && !$options['link_text']) 
  {
    return content_tag($options['tag'], $value, $attrs);
  }
  else
  {
    return content_tag($options['tag'], auto_link_text($value, 'urls'), $attrs);
  }
}


function seegno_simple_slot($name, $type, $options = array() )
{  
  $slot = Doctrine::getTable('Slot')->findOneByName($name);
  
  if (!$slot)
  {
    $slot = new Slot;

    $slot->setName($name);
    $slot->setType($type);
  }
  
  if (stripos($type, 'Widget') !== false)
  {
    return render_widget($slot, $options);
  }

  $options['attr'] = array( 'data-type' => $type );

  return render_slot($slot, $options);
}


function seegno_slot($page, $name, $type = 'Text', $options = array() )
{
  if (!is_object($page))
  {
    $page = Doctrine::getTable('Page')->retrieveByAction($page);
  }

  $slot = $page->getSlot($name);

  if (!$slot)
  {
    $slot = new Slot;

    $slot->setName($name);
    $slot->setType($type);
    
    if ($page)
    {
      $slot->setPageId($page->getId());
    }
  }
  else if ($slot->getType() != $type )
  {
    $slot->setType($type);
    $slot->save();
  }
  
  if ($type === 'Widget')
  {
    return render_widget($slot, $options);
  }

  return render_slot($slot, $options);
}

if (is_editor()) {
  $js = '$(document).ready(function() {';

  $configs = sfConfig::get("app_seegnoPages_configs");

  $slug = sfContext::getInstance()->getRequest()->getParameter('slug') ? sfContext::getInstance()->getRequest()->getParameter('slug') : 'custom';

  $data_params  = " submitdata: function() { return { className: $(this).data('class'), key: $(this).data('key'), field: $(this).data('field'), type: $(this).data('type'), } }";

  if (isset($configs))
  {
    foreach ($configs as $class => $path)
    {
      $js .= '$(".eip.richtext.' . $class . '").not(".source").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "richtext", config: "' . rawurlencode($path) . '"}).addClass("processed");';      
      $js .= '$(".eip.richtext.source.' . $class . '").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "richtext", loadurl: "' . url_for_with_path('seegnoPages/source?slug=' . $slug) . '", loadtype: "post", config: "' . rawurlencode($path) . '"}).addClass("processed");';
    }
  }

  $js .= '$(".eip.spinner").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "spinner"});
  $(".eip.widget").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "select", loadurl: "' . url_for_with_path('seegnoPages/widgets?slug=' . $slug) . '"});
  $(".eip.text").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "text"});
  $(".eip.textarea").not(".source").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "textarea"});
  $(".eip.richtext").not(".source").not(".processed").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "richtext"});
  $(".eip.textarea.source").not(".processed").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "textarea", loadurl: "' . url_for_with_path('seegnoPages/source?slug=' . $slug) . '", loadtype: "post"});
  $(".eip.richtext.source").not(".processed").editable("' . url_for_with_path('seegnoPages/update?slug=' . $slug) . '", {' . $data_params . ', type: "richtext", loadurl: "' . url_for_with_path('seegnoPages/source?slug=' . $slug) . '", loadtype: "post"});
});';

  echo javascript_tag($js);
}
