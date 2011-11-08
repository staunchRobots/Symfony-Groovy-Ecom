<?php

/**
 * BaseSlot
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $page_id
 * @property string $name
 * @property clob $value
 * @property string $type
 * @property Page $Page
 * 
 * @method integer getPageId()  Returns the current record's "page_id" value
 * @method string  getName()    Returns the current record's "name" value
 * @method clob    getValue()   Returns the current record's "value" value
 * @method string  getType()    Returns the current record's "type" value
 * @method Page    getPage()    Returns the current record's "Page" value
 * @method Slot    setPageId()  Sets the current record's "page_id" value
 * @method Slot    setName()    Sets the current record's "name" value
 * @method Slot    setValue()   Sets the current record's "value" value
 * @method Slot    setType()    Sets the current record's "type" value
 * @method Slot    setPage()    Sets the current record's "Page" value
 * 
 * @package    carpetbeggers
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7294 2010-03-02 17:59:20Z jwage $
 */
abstract class BaseSlot extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('slot');
        $this->hasColumn('page_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('value', 'clob', null, array(
             'type' => 'clob',
             ));
        $this->hasColumn('type', 'string', 255, array(
             'type' => 'string',
             'default' => 'Text',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Page', array(
             'local' => 'page_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $i18n0 = new Doctrine_Template_I18n(array(
             'fields' => 
             array(
              0 => 'value',
             ),
             ));
        $this->actAs($timestampable0);
        $this->actAs($i18n0);
    }
}