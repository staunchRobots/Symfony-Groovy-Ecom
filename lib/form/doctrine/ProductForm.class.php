<?php

/**
 * Product form.
 *
 * @package    carpetbeggers
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ProductForm extends BaseProductForm
{
  public function configure()
  {
    $this->useFields(array('name', 'price', 'length', 'width', 'quality', 'status', 'pile', 'floor', 'is_published', 'photo', 'categories_list',  'photo_x1', 'photo_x2', 'photo_y1', 'photo_y2'));
    
    $this->validatorSchema['name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
    
    $this->getObject()->configureJCropWidgets($this);
    $this->getObject()->configureJCropValidators($this);
  }
}
