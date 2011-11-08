<?php

class seegnoGuardSecurityUser extends sfGuardSecurityUser
{
  private $breadcrumb = array();

  public function getId()
  {
    if ($this->getGuardUser())
    {
      return $this->getGuardUser()->getId();
    }

    return false;
  }

  public function resetBreadcrumb()
  {
    $this->breadcrumb = array();

    return $this->breadcrumb;
  }

  public function addBreadcrumb($entry)
  {
    $this->breadcrumb[] = $entry;
  }

  public function getBreadcrumb()
  {
    return $this->breadcrumb;
  }
}