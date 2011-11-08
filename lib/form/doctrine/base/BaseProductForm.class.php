<?php

/**
 * Product form base class.
 *
 * @method Product getObject() Returns the current form's model object
 *
 * @package    carpetbeggers
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseProductForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'name'            => new sfWidgetFormInputText(),
      'price'           => new sfWidgetFormInputText(),
      'length'          => new sfWidgetFormInputText(),
      'width'           => new sfWidgetFormInputText(),
      'quality'         => new sfWidgetFormChoice(array('choices' => array(3 => '3', 2 => '2', 1 => '1'))),
      'status'          => new sfWidgetFormChoice(array('choices' => array('sold' => 'sold', 'sale pending' => 'sale pending', 'incomplete' => 'incomplete', 'complete' => 'complete'))),
      'pile'            => new sfWidgetFormInputText(),
      'floor'           => new sfWidgetFormInputText(),
      'notes'           => new sfWidgetFormTextarea(),
      'is_published'    => new sfWidgetFormInputCheckbox(),
      'flickr_id'       => new sfWidgetFormInputText(),
      'is_on_sale'      => new sfWidgetFormInputCheckbox(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'slug'            => new sfWidgetFormInputText(),
      'photo'           => new sfWidgetFormInputText(),
      'photo_x1'        => new sfWidgetFormInputText(),
      'photo_y1'        => new sfWidgetFormInputText(),
      'photo_x2'        => new sfWidgetFormInputText(),
      'photo_y2'        => new sfWidgetFormInputText(),
      'categories_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Category')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'name'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'price'           => new sfValidatorNumber(array('required' => false)),
      'length'          => new sfValidatorNumber(array('required' => false)),
      'width'           => new sfValidatorNumber(array('required' => false)),
      'quality'         => new sfValidatorChoice(array('choices' => array(0 => '3', 1 => '2', 2 => '1'), 'required' => false)),
      'status'          => new sfValidatorChoice(array('choices' => array(0 => 'sold', 1 => 'sale pending', 2 => 'incomplete', 3 => 'complete'), 'required' => false)),
      'pile'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'floor'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'notes'           => new sfValidatorString(array('required' => false)),
      'is_published'    => new sfValidatorBoolean(array('required' => false)),
      'flickr_id'       => new sfValidatorInteger(array('required' => false)),
      'is_on_sale'      => new sfValidatorBoolean(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'slug'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'photo'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'photo_x1'        => new sfValidatorInteger(array('required' => false)),
      'photo_y1'        => new sfValidatorInteger(array('required' => false)),
      'photo_x2'        => new sfValidatorInteger(array('required' => false)),
      'photo_y2'        => new sfValidatorInteger(array('required' => false)),
      'categories_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Category', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Product', 'column' => array('slug')))
    );

    $this->widgetSchema->setNameFormat('product[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Product';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['categories_list']))
    {
      $this->setDefault('categories_list', $this->object->Categories->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveCategoriesList($con);

    parent::doSave($con);
  }

  public function saveCategoriesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['categories_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Categories->getPrimaryKeys();
    $values = $this->getValue('categories_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Categories', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Categories', array_values($link));
    }
  }

}
