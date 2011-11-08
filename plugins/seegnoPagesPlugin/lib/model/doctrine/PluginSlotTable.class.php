<?php

class PluginSlotTable extends Doctrine_Table
{
  public function getSlotValue($name, $default = false)
  {
    $slot = Doctrine::getTable('Slot')->findOneByName($name);

    return $slot ? $slot->getValue() : $default;
  }
}