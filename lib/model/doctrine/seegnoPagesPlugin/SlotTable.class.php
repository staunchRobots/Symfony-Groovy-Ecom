<?php


class SlotTable extends PluginSlotTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Slot');
    }
}