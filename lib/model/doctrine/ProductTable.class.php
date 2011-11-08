<?php


class ProductTable extends Doctrine_Table
{
  public static function getInstance()
  {
      return Doctrine_Core::getTable('Product');
  }
  
  public function retrieveByKeyword($keyword)
  {
    $q = $this->createQuery('p')->select('p.id, p.name, p.slug')->where('p.id LIKE LOWER(?) OR p.name LIKE LOWER(?)', array('%'.$keyword.'%', '%'.$keyword.'%'));
    
    return $this->addFilter($q, 'complete')->fetchArray();
  }
  
  public function countByCategoryId($id)
  {
    return $this->retrieveByCategoryIdQuery($id)->count();
  }

  public function retrieveAllFilteredBy($filter)
  {
    $q = $this->createQuery('p');
    
    return $q = $this->addFilter($q, $filter)->execute();
  }

  public function retrieveByCategoryIdQuery($id)
  {
    return $this->createQuery('p')->innerJoin('p.Categories c')->where('c.id = ?', $id);
  }

  public function retrieveByCategoryId($id)
  {
    return $this->retrieveByCategoryIdQuery($id)->execute();
  }
  
  public function retrieveByCategoryName($name)
  {
    return $this->createQuery('p')->innerJoin('p.Categories c')->where('c.parent = ?', $name)->execute();
  }
  
  public function retrieveByCategoryNameQuery($name)
  {
    return $this->createQuery('p')->innerJoin('p.Categories c')->where('c.parent = ?', $name);
  }

  public function retrieveByCategoryNameFilteredBy($name, $filter)
  {
    $q = $this->retrieveByCategoryNameQuery($name);
    
    $q = $this->addFilter($q, $filter);
    
    return $q->execute();
  }
  
  public function addPriceRange($q, $min, $max)
  {
    return $q->addWhere('p.price >= ? AND p.price <= ?', array($min, $max));
  }

  public function addLengthRange($q, $min, $max)
  {
    return $q->addWhere('p.length >= ? AND p.length <= ?', array($min, $max));
  }
  
  public function addWidthRange($q, $min, $max)
  {
    return $q->addWhere('p.width >= ? AND p.width <= ?', array($min, $max));
  }
  
  public function addFilterByCategories($q, $slugs)
  {
    return $q->whereIn('c.slug', $slugs)->addOrderBy('p.quality DESC');
  }
  
  public function addFilter($q, $filter)
  {
    switch ($filter) {
      case 'published':
        $q->addWhere('p.is_published = ?', true);
        break;

      case 'sold':
        $q->addWhere('p.status = ?', 'sold');
        break;

      case 'incomplete':
        $q->addWhere('p.status = ?', 'incomplete');
        break;

      case 'pending':
        $q->addWhere('p.status = ?', 'sales pending');
        break;
        
      case 'complete':
        $q->addWhere('p.status = ?', 'complete');
        break;

      case 'unpublished':
        $q->addWhere('p.is_published = ?', false);
        break;
            
      default:
        break;
    }
    
    return $q->addOrderBy('p.quality DESC');
  }
}