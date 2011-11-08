<?php
/**
  * myWidgetFormRichDate is a rich date widget for 1.1+ forms
  *
  * @author Matt Daum matt [at] setfive.com
  */
class seegnoWidgetFormRichDate extends sfWidgetFormDate
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Tag', 'Form'));

    $attributes['rich'] = true;

    return input_date_tag($name,$value, $attributes);
  }
}