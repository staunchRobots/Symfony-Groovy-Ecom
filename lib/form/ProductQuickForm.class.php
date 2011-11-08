<?php

/**
 * Product form.
 *
 * @package    carpetbeggers
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ProductQuickForm extends BaseProductForm
{
  public function configure()
  {
    $choices = array();
    $keys = array();
    
    $fields = array('name', 'price', 'length', 'width', 'quality', 'status', 'pile', 'floor', 'is_published');
    
    if ($this->getObject()->isFurniture()) unset($fields['length'], $fields['width']);
    
    $this->useFields($fields);
    
    $this->widgetSchema['status']->setDefault($this->getObject()->getStatus());
    
    $parent = CategoryTable::getInstance()->retrieveAvailableCategories($this->getObject()->getId(), $this->getObject()->getCategoryIds());
    
    foreach ($parent as $type => $categories) 
    {
      foreach ($categories as $category) 
      {
        $choices[$type][$category['id']] = $category['name'];
      }
    }
    
    $this->widgetSchema['quality'] = new sfWidgetFormChoice(array('choices' => array(3 => 'Best', 2 => 'Great', 1 => 'Good')));
    
    $this->widgetSchema['categories_options'] = new sfWidgetFormChoice(array('choices' => $choices));
    $this->validatorSchema['categories_options'] = new sfValidatorPass();

    $this->validatorSchema['name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
    
    $this->widgetSchema->setNameFormat('item[%s]');
  }
}