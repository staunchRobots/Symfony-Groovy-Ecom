<?php

class coreComponents extends sfComponents
{
  public function executeCheckImportStatus(sfWebRequest $request)
  {
    $this->poll = ($this->getUser()->hasAttribute('id', 'flickr')) ? true : false;
  }
}