<?php

/**
 * Product filter form base class.
 *
 * @package    carpetbeggers
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseProductFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'            => new sfWidgetFormFilterInput(),
      'price'           => new sfWidgetFormFilterInput(),
      'length'          => new sfWidgetFormFilterInput(),
      'width'           => new sfWidgetFormFilterInput(),
      'quality'         => new sfWidgetFormChoice(array('choices' => array('' => '', 3 => '3', 2 => '2', 1 => '1'))),
      'status'          => new sfWidgetFormChoice(array('choices' => array('' => '', 'sold' => 'sold', 'sale pending' => 'sale pending', 'incomplete' => 'incomplete', 'complete' => 'complete'))),
      'pile'            => new sfWidgetFormFilterInput(),
      'floor'           => new sfWidgetFormFilterInput(),
      'notes'           => new sfWidgetFormFilterInput(),
      'is_published'    => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'flickr_id'       => new sfWidgetFormFilterInput(),
      'is_on_sale'      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'slug'            => new sfWidgetFormFilterInput(),
      'photo'           => new sfWidgetFormFilterInput(),
      'photo_x1'        => new sfWidgetFormFilterInput(),
      'photo_y1'        => new sfWidgetFormFilterInput(),
      'photo_x2'        => new sfWidgetFormFilterInput(),
      'photo_y2'        => new sfWidgetFormFilterInput(),
      'categories_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Category')),
    ));

    $this->setValidators(array(
      'name'            => new sfValidatorPass(array('required' => false)),
      'price'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'length'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'width'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'quality'         => new sfValidatorChoice(array('required' => false, 'choices' => array(3 => '3', 2 => '2', 1 => '1'))),
      'status'          => new sfValidatorChoice(array('required' => false, 'choices' => array('sold' => 'sold', 'sale pending' => 'sale pending', 'incomplete' => 'incomplete', 'complete' => 'complete'))),
      'pile'            => new sfValidatorPass(array('required' => false)),
      'floor'           => new sfValidatorPass(array('required' => false)),
      'notes'           => new sfValidatorPass(array('required' => false)),
      'is_published'    => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'flickr_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_on_sale'      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'slug'            => new sfValidatorPass(array('required' => false)),
      'photo'           => new sfValidatorPass(array('required' => false)),
      'photo_x1'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'photo_y1'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'photo_x2'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'photo_y2'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'categories_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Category', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addCategoriesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.ProductCategory ProductCategory')
          ->andWhereIn('ProductCategory.category_id', $values);
  }

  public function getModelName()
  {
    return 'Product';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'name'            => 'Text',
      'price'           => 'Number',
      'length'          => 'Number',
      'width'           => 'Number',
      'quality'         => 'Enum',
      'status'          => 'Enum',
      'pile'            => 'Text',
      'floor'           => 'Text',
      'notes'           => 'Text',
      'is_published'    => 'Boolean',
      'flickr_id'       => 'Number',
      'is_on_sale'      => 'Boolean',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
      'slug'            => 'Text',
      'photo'           => 'Text',
      'photo_x1'        => 'Number',
      'photo_y1'        => 'Number',
      'photo_x2'        => 'Number',
      'photo_y2'        => 'Number',
      'categories_list' => 'ManyKey',
    );
  }
}
