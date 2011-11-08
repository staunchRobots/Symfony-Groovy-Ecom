<?php

class categoriesActions extends sfActions
{
  public function executeNew(sfWebRequest $request)
  {
    $parent = CategoryTable::getInstance()->findOneById($request->getParameter('parent_id'));
    
    $this->forward404Unless($this->getUser()->isSuperAdmin() && $parent);

    $category = new Category;

    $category->setName($request->getParameter('name'));
    $category->setParent($parent->getSlug());

    $category->save();
    $category->moveToFirst();

    $this->form = new CategorySortForm(null, array('parent' => $parent));

    return $this->renderPartial('products/category_form', array('form' => $this->form));
  }

  public function executeList(sfWebRequest $request)
  {
    $parent = CategoryTable::getInstance()->findOneById($request->getParameter('parent_id'));
    
    $this->forward404Unless($this->getUser()->isSuperAdmin() && $parent);

    $categories = CategoryTable::getInstance()->findAllSortedWithParent($parent->getSlug(), 'parent', 'ASCENDING');

    $this->form = new CategorySortForm(null, array('parent' => $parent));

    return $this->renderPartial('products/category_form', array('form' => $this->form));
  }

  public function executeRemove(sfWebRequest $request)
  {
    $category = CategoryTable::getInstance()->findOneById($request->getParameter('id'));

    $this->forward404Unless($this->getUser()->isSuperAdmin() && $category);

    $parent = CategoryTable::getInstance()->findOneBySlug($category->getParent());

    $category->delete();

    $this->form = new CategorySortForm(null, array('parent' => $parent));

    return $this->renderPartial('products/category_form', array('form' => $this->form));
  }

  public function executeMove(sfWebRequest $request)
  {
    $type = $request->getParameter('type');

    $this->forward404Unless(in_array($type, array('promote', 'demote')) && $this->getUser()->isSuperAdmin());

    $category = CategoryTable::getInstance()->findOneById($request->getParameter('id'));

    $this->forward404Unless($category);

    $parent = CategoryTable::getInstance()->findOneBySlug($category->getParent());
    
    if ($type == 'promote')
    {
      $category->promote();
    }
    elseif ($type == 'demote') 
    {
      $category->demote();
    }
    
    $this->form = new CategorySortForm(array(), array('parent' => $parent));

    return $this->renderPartial('products/category_form', array('form' => $this->form));
  }
  
 public function executeSave(sfWebRequest $request)
 {
   $category = CategoryTable::getInstance()->findOneById($request->getParameter('id'));

   $this->forward404Unless($this->getUser()->isSuperAdmin() && $category);

   $category->setName($request->getParameter('value'));
   $category->save();

   return $this->renderText(json_encode(array('success' => 'true')));
 }
}
