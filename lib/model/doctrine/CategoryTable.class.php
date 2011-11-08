<?php


class CategoryTable extends Doctrine_Table
{    
  public static function getInstance()
  {
    return Doctrine_Core::getTable('Category');
  }
  
  public function retrieveMainCategories()
  {
    $categories = $this->createQuery()->select('id,name')->where('parent IS NULL')->execute();
    
    $choices = array();
    
    foreach ($categories as $category) 
    {
      $choices[$category->getId()] = $category->getName();
    }
    
    return $choices;
  }

  public function retrieveByParentName($name)
  {
    return $this->retrieveByParentNameQuery($name)->execute();
  }
  
  public function retrieveByParentNameQuery($name)
  {
    return $this->createQuery('c')->addWhere('c.parent = ?', $name)->addOrderBy('c.position');
  }
  
  public function retrieveAllCategoriesIds()
  {
    $ids = array();
    
    $categories = $this->createQuery('c')->select('c.id')->where('c.parent IS NOT NULL')->fetchArray();
    
    foreach ($categories as $category) 
    {
      $ids[] = $category['id'];
    }
    
    return $ids;
  }
  
  public function retrieveAllCategories()
  {
    $rugs = $this->retrieveByParentName('rugs');
    $categories = $this->retrieveByParentName('furniture');

    return array('rugs' => $rugs, 'furniture' => $categories); 
  }

  public function retrieveCategoriesQuery()
  {
    $rugsQuery = $this->retrieveByParentNameQuery('rugs');
    
    $furnitureQuery = $this->retrieveByParentNameQuery('furniture');

    return array('rugsQuery' => $rugsQuery, 'furnitureQuery' => $furnitureQuery);    
  }
  
  public function retrieveCategoriesByType()
  {
    $categories = $this->retrieveCategories();
    
    return array('rugs' => seegno::indexObjects($categories['rugs'], array('sort', 'id'), array('array' => false)), 'furniture' => seegno::indexObjects($categories['furniture'], array('sort', 'id'), array('array' => false)));
  }

  public function retrieveCategories()
  {
    $categories = $this->retrieveCategoriesQuery();
    
    $rugs = $categories['rugsQuery']->execute();

    $furniture = $categories['furnitureQuery']->execute();

    return array('rugs' => $rugs, 'furniture' => $furniture);
  }
  
  public function retrieveGroupedCategories()
  {
    $all = $this->retrieveCategories();
    
    $choices = array();
    
    foreach ($all as $type => $categories) 
    {
      foreach ($categories as $category) 
      {
        $choices[$type][$category->getId()] = $category->getName();
      }
    }
    
    return $choices;
  }
  
  public function retrieveAvailableCategories($productId, $ids)
  {
    $categories = $this->retrieveCategoriesQuery();

    $categories['rugsQuery'] = $this->addAssignedCategoriesQueryFor($categories['rugsQuery'], $productId);
    $categories['furnitureQuery'] = $this->addAssignedCategoriesQueryFor($categories['furnitureQuery'], $productId);

    $rugs = $categories['rugsQuery']->execute();
    $furniture = $categories['furnitureQuery']->execute();
    
    return array('rugs' => $rugs, 'furniture' => $furniture);
  }
  
  public function addAssignedCategoriesQueryFor($q, $productId)
  {
    return $q->addWhere('c.id NOT IN (SELECT pc.category_id FROM ProductCategory pc WHERE pc.product_id = ?)', $productId)->orderBy('c.name');
  }

  public function retrieveLimits()
  {
    $limits = $this->createQuery('c')->select('c.*, MIN(p.price) AS min_price, MAX(p.price) AS max_price, MIN(p.width) AS min_width, MAX(p.width) AS max_width, MIN(p.length) AS min_length, MAX(p.length) AS max_length')->leftJoin('c.Products p')->where('c.parent = ? OR c.parent = ?', array('rugs', 'furniture'))->groupBy('c.parent')->execute();
    
    foreach ($limits as $index => $values) 
    {
      if ($values['parent'] == 'furniture')
      {
       $limits['furniture'] = $limits[$index];
       unset($limits[$index]);
      }
      
      else if ($values['parent'] == 'rugs'){
        $limits['rugs'] = $limits[$index];
        unset($limits[$index]);
      }
    }
      
    return $limits;
  }
}