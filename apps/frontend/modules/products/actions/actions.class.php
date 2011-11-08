<?php

class productsActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $type = $request->getParameter('type');
    
    $filter = $request->getParameter('filter');
    
    if ($filter != 'published') $this->forward404Unless($this->getUser()->isSuperAdmin());
    
    $this->products = ($type == 'all') ? ProductTable::getInstance()->retrieveAllFilteredBy($filter)
                                       : ProductTable::getInstance()->retrieveByCategoryNameFilteredBy($type, $filter);

    if (!$this->products->count() > 0)
    {
     return 'Error';
    }
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->isSuperAdmin() && $product = ProductTable::getInstance()->findOneById($request->getParameter('id')));
    
    $product->delete();
    
    return $this->renderText(json_encode(array('url' => $request->getReferer())));
  }

  public function executeShowcase(sfWebRequest $request)
  { 
    $this->forward404Unless($this->product = ProductTable::getInstance()->findOneById($request->getParameter('id')));
    
    return $this->renderPartial('products/showcase');
  }

  public function executeSearch(sfWebRequest $request)
  {
    if ($request->hasParameter('q'))
    {
      return $this->renderText(json_encode(ProductTable::getInstance()->getInstance()->retrieveByKeyword($request->getParameter('q'))));
    }
    
    $lengths = $request->getParameter('lr');
    $prices = $request->getParameter('pr');
    $widths = $request->getParameter('wr');
    $parent = $request->getParameter('pt');
    
    $categories = explode(',', $request->getParameter('category'));
    
    $q = ProductTable::getInstance()->retrieveByCategoryNameQuery($parent);
    
    if (isset($lengths) && !empty($lengths['min']) && !empty($lengths['max']))
    {
      $q = ProductTable::getInstance()->addLengthRange($q, $lengths['min'], $lengths['max']);
    }

    if (isset($prices) && !empty($prices['min']) && !empty($prices['max']))
    {
      $q = ProductTable::getInstance()->addPriceRange($q, $prices['min'], $prices['max']);
    }
    
    if (isset($widths) && !empty($widths['min']) && !empty($widths['max']))
    {
      $q = ProductTable::getInstance()->addWidthRange($q, $widths['min'], $widths['max']);
    }
    
    if (isset($categories))
    {
      $q = ProductTable::getInstance()->addFilterByCategories($q, $categories);
    }
    
    $q = ProductTable::getInstance()->addFilter($q, 'published');
    
    $this->products = $q->execute();
    
    if (!$this->products->count() > 0)
    {
     $this->setTemplate('index');
     
     return 'Error';
    }

    $this->setTemplate('index');
  }

  public function executeUpdateNotes(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->isSuperAdmin() && $product = ProductTable::getInstance()->findOneById($request->getParameter('id')));
    
    $this->form = new NotesForm($product);
    
    if ($request->isMethod('post'))
    {
       $this->form->bind($request->getParameter('notes'));

       if ($this->form->isValid())
       {
         $updatedItem = $this->form->save();
         
         $this->getUser()->setFlash('notice', sfContext::getInstance()->getI18N()->__('Notes edited with success!'));
         
         $this->form = new NotesForm($updatedItem);
       }
     }
     
     return $this->renderPartial('products/notes', array('form' => $this->form));
  }
  
  public function executeUpdateItem(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->isSuperAdmin() && $product = ProductTable::getInstance()->findOneById($request->getParameter('id')));
    
    $this->form = new ProductQuickForm($product);
    
    $data = $request->getParameter('item');
    
    unset($data['categories_options']);

    if ($request->isMethod('post'))
    {
       $this->form->bind($data);

       if ($this->form->isValid())
       {
         $updatedItem = $this->form->save();
         
         $this->getUser()->setFlash('notice', sfContext::getInstance()->getI18N()->__('Item edited with success!'));
         
         $this->form = new ProductQuickForm($updatedItem);
       }
     }
     
     return $this->renderPartial('products/item', array('form' => $this->form));
  }
  
  public function executeImport(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->isSuperAdmin());

    $this->form = new ImportForm;

    if ($request->isMethod('post'))
    {
       $this->form->bind($request->getParameter('import'));
       
       if ($this->form->isValid())
       {
         $data = $this->form->getValues();

         preg_match("/flickr.com\/photos\/(.*)\/sets\/(.*)\//", $data['url'], $matches);
         
         if (count($matches) < 3)
         {
           $this->getUser()->setFlash('notice', sfContext::getInstance()->getI18N()->__('Please enter a valid Flickr Set URL!'));
         }
         else 
         {
           if (isset($matches[1]) && isset($matches[2])) 
           {
             $id = sha1(uniqid());
             
             $exec = sfConfig::get('sf_root_dir') . '/symfony carpetbeggers:flickr-import '. $matches[1] . ' ' . $matches[2] . ' ' . $id
                   . ' ' . $data['category'] . " >> " . sfConfig::get('sf_root_dir') . '/log/flickr/' . $id . '.log';

             $exec = $exec . ' &';

             system($exec);

             $this->getUser()->setAttribute('id', $id, 'flickr');

             $this->redirect('@homepage');
           }
         }
       }
     }
     
     return $this->renderPartial('products/import', array('form' => $this->form));
  }
  
  public function executeCheckImportStatus(sfWebRequest $request)
  {
     $this->forward404Unless($this->getUser()->isSuperAdmin());
    
     $id = $this->getUser()->getAttribute('id', null, 'flickr');
     
     if (isset($id) && file_exists(sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $id . '.lock'))
     {
       return $this->renderText(sfContext::getInstance()->getI18N()->__('Your request is currently being processed.'));
     }
     else 
     {
       $this->getUser()->setAttribute('id', null, 'flickr');
       
       return $this->renderPartial('core/status', array('url' => '@homepage'));
     }
  }
  
  public function executeToggleTag(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->isSuperAdmin());
    
    $productId = $request->getParameter('product_id');
    $categoryId = $request->getParameter('category_id');
    
    $this->forward404Unless($this->product = ProductTable::getInstance()->findOneById($productId));
    $this->forward404Unless($this->category = CategoryTable::getInstance()->findOneById($categoryId));

    $productCategory = ProductCategoryTable::getInstance()->find(array($productId, $categoryId));
    
    if ($productCategory)
    {
      $productCategory->delete();
      
      $this->getUser()->setFlash('notice', sfContext::getInstance()->getI18N()->__('Category removed with success!'));
    }
    else 
    {
      $productCategory = new ProductCategory;

      $productCategory->setProductId($this->product->getId());
      $productCategory->setCategoryId($this->category->getId());
      $productCategory->save();

      $this->getUser()->setFlash('notice', sfContext::getInstance()->getI18N()->__('Category added with success!'));
    }
    
    $this->form = new ProductQuickForm($this->product);

    return $this->renderPartial('products/item_tags', array('form' => $this->form));
  }
}
?>