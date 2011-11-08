<?php
class seegnoWidgetFormDisplay extends sfWidgetForm
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $tag  = 'span';
    if (isset($attributes['tag']))
    {
      $tag  = $attributes['tag'];
      unset($attributes['tag']);
    }

    $suffix = '';
    if (isset($attributes['suffix']))
    {
      $suffix  = $attributes['suffix'];
      unset($attributes['suffix']);
    }

    $hidden = $this->renderContentTag('input', '', array('type' => 'hidden', 'name' => $name, 'value' => $value));
    return $hidden . $this->renderContentTag($tag, $value . $suffix, $attributes);
  }
}
