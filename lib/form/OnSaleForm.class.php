<?php

class OnSaleForm extends BaseForm
{
  public function configure()
  {    
    $this->widgetSchema['email'] = new sfWidgetFormInput();

    $this->validatorSchema['email'] = new sfValidatorEmail(array('required' => true));
    
    $this->widgetSchema->setNameFormat('on_sale[%s]');
  }
}
