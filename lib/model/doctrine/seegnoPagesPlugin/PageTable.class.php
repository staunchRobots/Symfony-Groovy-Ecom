<?php


class PageTable extends PluginPageTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Page');
    }
}