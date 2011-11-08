<?php

class Doctrine_Template_Listener_Defaultable extends Doctrine_Record_Listener
{
  protected $_options = array();

  public function __construct(array $options)
  {
      $this->_options = $options;
  }
  
  public function postSave(Doctrine_Event $event)
  {
    $record = $event->getInvoker();

    if (is_array($record->getTable()->getIdentifier()))
    {
      foreach ($record->getTable()->getIdentifier() as $name)
      {
        if (isset($this->_options['uniqueBy']) && in_array($name, array_values($this->_options['uniqueBy'])))
        {
          continue;
        }

        $whereString[] = $name . ' <> ?';
        $whereParams[] = $record->get($name);
      }

      $whereString = implode(' AND ', $whereString);
    }
    else
    {
      $whereString = "id <> ?";
      $whereParams = array($record->getId());
    }

    foreach ($this->_options['uniqueBy'] as $uniqueBy) 
    {
      if (is_null($record->$uniqueBy)) 
      {
        $whereString .= ' AND '.$uniqueBy.' IS NULL';
      } 
      else
      {
        $whereString .= ' AND '.$uniqueBy.' = ?';
        
        $value = $record->$uniqueBy;
        
        if ($value instanceof Doctrine_Record) 
        {
          $value = current((array) $value->identifier());
        }

        $whereParams[] =  $value;
      }
    }

    if ($record->getIsDefault())
    {
      $record->getTable()->createQuery()->update(get_class($record))->set('is_default', 'false')->where($whereString , $whereParams)->execute();
    }
  }
}