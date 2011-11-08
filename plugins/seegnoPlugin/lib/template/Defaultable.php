<?php

class Doctrine_Template_Defaultable extends Doctrine_Template
{
  protected $_options = array(
      'unique'        =>  true,
      'uniqueBy'      =>  array()
  );
  
  public function setTableDefinition()
  {
    $this->hasColumn('is_default', 'boolean', null, array('type' => 'boolean', 'default' => false));

    $this->addListener(new Doctrine_Template_Listener_Defaultable($this->_options));
  }
}