<?php

class ImportForm extends BaseForm
{
  public function configure()
  {
    $choices = CategoryTable::getInstance()->retrieveGroupedCategories();

    $this->widgetSchema['category'] = new sfWidgetFormSelect(array('choices' => $choices));
    $this->widgetSchema['url'] = new sfWidgetFormInputText();
    
    $this->validatorSchema['category'] = new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'Category', 'required' => true));
    $this->validatorSchema['url'] = new sfValidatorUrl(array('required' => true));

    $this->widgetSchema->setNameFormat('import[%s]');
  }
}
