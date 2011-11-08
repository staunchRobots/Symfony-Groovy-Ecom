<?php

class seegnoWidgetFormTextareaTinyMCE extends sfWidgetFormTextareaTinyMCE
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $attributes = array_merge($this->attributes, $attributes); 

    return parent::render($name, $value, $attributes, $errors);
  }
}
