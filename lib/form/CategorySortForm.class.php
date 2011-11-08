<?php

class CategorySortForm extends BaseForm
{
  public function configure()
  {    
    $parent = $this->getOption('parent');
    
    $sortedCategories = CategoryTable::getInstance()->findAllSortedWithParent($parent->getSlug(), 'parent', 'ASCENDING');
    
    $mainCategories = CategoryTable::getInstance()->retrieveMainCategories();
    
    $this->widgetSchema['parent'] = new sfWidgetFormSelect(array('choices' => $mainCategories));
    
    $this->widgetSchema['parent']->setDefault($parent->getId());
    
    $this->widgetSchema['categories'] = new seegnoWidgetSortable(array(
                                            'model'       => 'Category',
                                            'choices'     => $sortedCategories,
                                            'promote_url' => '@categories?action=move&type=promote', 
                                            'demote_url'  => '@categories?action=move%type=demote',
                                            'remove_url'  => 'categories/remove', 
                                            'save_url'    => 'categories/save', 
                                            'update'      => 'category_edit_form'));
    
    $this->widgetSchema['new'] = new sfWidgetFormInput(array('label' => 'Add new'));
    
    $this->validatorSchema['new'] = new sfValidatorString(array('required' => false, 'min_length' => 2));
    
    $this->widgetSchema->setNameFormat('category[%s]');
  }
}
