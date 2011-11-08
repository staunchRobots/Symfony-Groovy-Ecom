<?php

class sfWidgetFormSchemaFormatterDiv extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<div id=\"%id%\" class=\"form-row%is_error%\">\n  %label%\n  %field%\n  %help%\n%hidden_fields%</div>\n",
    $errorRowFormat  = "%errors%\n",
    $helpFormat      = '<div class="form-help"><div class="top"></div><div class="content"><h4>Help</h4><p>%help%</p></div><div class="bottom"></div></div>',
    $decoratorFormat = "\n  %content%";
  
  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    preg_match('/for="(.+)"/', $label, $matches);

    if (isset($matches[1]))
    {
      $row = strtr(parent::formatRow($label, $field, $errors, $help, $hiddenFields), array('%is_error%' => (count($errors) > 0) ? ' error' : '', '%id%' => 'field_' . $matches[1]));
    }
    else
    {
      $row = strtr(parent::formatRow($label, $field, $errors, $help, $hiddenFields), array('%is_error%' => (count($errors) > 0) ? ' error' : '', '%id%' => ''));
    }

    return $row;
  }
}