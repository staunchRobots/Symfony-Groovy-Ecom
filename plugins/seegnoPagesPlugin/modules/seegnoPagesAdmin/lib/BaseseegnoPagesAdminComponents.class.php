<?php

class BaseseegnoPagesAdminComponents extends sfComponents
{  
  public function executeManager()
  {
    $request = $this->getRequest();
    
    $this->records = $this->getTree();
  }

  private function getTree()
  {
    $tree = Doctrine::getTable('Page')->getTree();

    return $tree->fetchTree();
  }
}
