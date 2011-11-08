<?php

class Doctrine_Template_Listener_Auditable extends Doctrine_Record_Listener
{
  protected $_options = array();

  public function __construct(array $options)
  {
    $this->_options = $options;
  }

  public function preInsert(Doctrine_Event $event)
  {
    if(!$this->_options['created']['disabled']) {
      $createdName = $this->_options['created']['name'];
      $event->getInvoker()->$createdName = $this->getUserId('created');
    }

    if(!$this->_options['updated']['disabled'] && $this->_options['updated']['onInsert']) {
      $updatedName = $this->_options['updated']['name'];
      $event->getInvoker()->$updatedName = $this->getUserId('updated');
    }
  }

  public function preUpdate(Doctrine_Event $event)
  {
    if (!$this->_options['created']['disabled'])
    {
      $createdName = $this->_options['created']['name'];

      if (!$event->getInvoker()->$createdName)
      {
        $event->getInvoker()->$createdName = $this->getUserId('created');
      }
    }

    if (!$this->_options['updated']['disabled'])
    {
      $updatedName = $this->_options['updated']['name'];
      $event->getInvoker()->$updatedName = $this->getUserId('updated');
    }
  }

  public function getUserId($type)
  {
    $options = $this->_options[$type];

    if ($options['expression'] !== false && is_string($options['expression']))
    {
      return new Doctrine_Expression($options['expression']);
    }
    elseif (!class_exists("pakeApp") and sfContext::hasInstance())
    {
      switch($options['type']) 
      {
        case 'integer':
          if (class_exists('sfGuardUser'))
          {
            return sfContext::getInstance()->getUser()->getAttribute('user_id', null, 'sfGuardSecurityUser');
          }
          else
          {
            return sfContext::getInstance()->getUser()->getId();
          }

          break;

        case 'string':
          return sfContext::getInstance()->getUser()->getUsername();
          break;

        default:
          return 'n/a';
          break;
      }
    }
  }
}