<?php

class ContactForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema['name'] = new sfWidgetFormInput();
    $this->widgetSchema['email'] = new sfWidgetFormInput();
    $this->widgetSchema['message'] = new sfWidgetFormTextarea();
    $this->widgetSchema['phone'] = new sfWidgetFormInput();
    
    $this->validatorSchema['name'] = new sfValidatorPass();
    $this->validatorSchema['email'] = new sfValidatorPass();
    $this->validatorSchema['message'] = new sfValidatorPass();
    $this->validatorSchema['phone'] = new sfValidatorPass();
    
    $this->widgetSchema->setNameFormat('contact[%s]');
  }
}