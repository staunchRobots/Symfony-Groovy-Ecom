<?php

class productsComponents extends sfComponents
{
  public function executeAdminTabs(sfWebRequest $request)
  {
    if ($this->getUser()->isSuperAdmin())
    {
      $type = $request->getParameter('type');
      
      $parent = (!isset($type) || $request->getParameter('type') == 'all') ? CategoryTable::getInstance()->findOneBySlug('rugs') 
                                                                           : CategoryTable::getInstance()->findOneBySlug($type); 

      $this->forms = array('item' => new ProductQuickForm($this->product),
                           'notes' => new NotesForm($this->product),
                           'category' => new CategorySortForm(null, array('parent' => $parent)),
                           'import' => new ImportForm);
    }
  }
  
  public function executeSidebar(sfWebRequest $request)
  {
    $this->categories = CategoryTable::getInstance()->retrieveCategoriesByType();

    $this->limits = CategoryTable::getInstance()->retrieveLimits();
  }
}