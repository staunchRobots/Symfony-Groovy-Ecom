<?php

abstract class PluginPage extends BasePage
{
  protected $slots = array();

  public function __toString()
  {
    return $this->getTitle();
  }

  public function getRoute()
  {
    return '@page?slug=' . $this->getSlug();
  }

  public function delete(Doctrine_Connection $conn = null)
  {
    Doctrine::getTable('Slot')->createQuery()->delete()->where('page_id = ?', $this->getId())->execute();

    foreach ($this->getSlots() as $slot)
    {
      $slot->delete();
    }

    return parent::delete($conn);
  }

  public function save(Doctrine_Connection $conn = null )
  {
    $parent = $this->getNode()->getParent();

    $original = $this->getModified(true);
    $update = $this->isModified('slug');
    $isNew = !$this->_get('slug');
  
    if ($this->getTemplate() == null || $this->getTemplate() == "")
    {
      $this->setTemplate('default');
    }

    $return = parent::save($conn);

    if ($parent)
    {
      $old_slug = (isset($original['slug'])) ? $original['slug'] : false;

      $new_slug = ($parent && $parent->_get('slug') && ($this->_get('slug') != $parent->_get('slug'))) ? $parent->_get('slug') . "/" . $this->_get('slug') : $this->_get('slug');

      $sql = "UPDATE page SET slug=? WHERE id=?";
      $pdo = Doctrine_Manager::connection()->getDbh();
      $q = $pdo->prepare($sql);

      $q->execute(array($new_slug, $this->getId()));

      if (!$isNew && $old_slug)
      {
        // Updating Children
        $sql = "UPDATE page SET slug=replace(slug, ?, ?) WHERE lft >= ? and rgt <= ?";
        $q = $pdo->prepare($sql);

        $new_slug = explode('/', $new_slug);
        $q->execute(array($old_slug . '/', end($new_slug) . '/', $this->getLft(), $this->getRgt()));

        // Updating Same Slug
        $sql = "UPDATE page SET slug=? WHERE slug = ? AND lft >= ? and rgt <= ?";
        $q = $pdo->prepare($sql);

        $q->execute(array(end($new_slug), $old_slug, $this->getLft(), $this->getRgt()));
      }
    }

    return $return;
  }

  public function updateSlug()
  {
    $slug = explode('/', $this->getSlug());

    $this->setSlug(end($slug));
    $this->save();
  }

  public function postInsert($event)
  {
    $this->setTitles();

    return parent::postInsert($event);
  }

  public function getSlots()
  {
    if (!count($this->slots))
    {
      $objects = Doctrine::getTable('Slot')->createQuery('s')
                 ->innerJoin('s.Translation t')
                 ->where('s.page_id = ? AND t.lang = ?', array($this->getId(), $this->getLang()))->execute();

      foreach($objects as $slot)
      {
        $this->slots[$slot->getName()] = $slot;
      }
    }

    return $this->slots;
  }

  public function hasSlots()
  {
    return count($this->getSlots());
  }

  public function getSlot($name)
  {
    $slots = $this->getSlots();

    return isset($slots[$name]) ? $slots[$name] : null;
  }

  public function getSlotValue($name)
  {
    $slot = $this->getSlot($name);

    return $slot ? $slot->value : null;
  }

  public function setSlot($name, $value, $type)
  {
    $slot = $this->getSlot($name);

    if (!$slot)
    {
      $slot = new Slot();

      $slot->setName($name);
      $slot->setPageId($this->getId());
    }

    $slot->setType($type);
    $slot->setValue($value);
    $slot->save();
    
    $this->slots[$name] = $slot;
    
    return $slot; 
  }

  public function getSlugWithLevel()
  {
    return str_repeat(' - ', $this->getLevel()) . $this->getSlug();
  }

  public function getTitleWithLevel()
  {
    return str_repeat(' - ', $this->getLevel()) . $this->getTitle();
  }

  public function getBreadcrumb()
  {
    $breadcrumb = array();

    foreach ($this->getNode()->getAncestors() as $node)
    {
      if ($node->getLevel() > 0)
      {
        $breadcrumb[] = array('name' => $node->getName(), 'link' => '@page?slug=' . $node->getSlug());
      }
    }

    $breadcrumb[] = array('name' => $this->getName(), 'link' => '@page?slug=' . $this->getSlug());

    return $breadcrumb;
  }

  public function getRoot()
  {
    return Doctrine::getTable('Page')->getTree()->fetchRoot($this->getRootId());
  }
  
  public function setTitles()
  {
    $titles = array();
    $cultures = array();

    $config = sfYAML::load(sfConfig::get('sf_config_dir') . '/seegno/seegnoI18NPlugin.yml');
    $languages = $config['languages'];

    foreach ($languages as $culture => $params)
    {
      $this->Translation[$culture]->title = $this->getTitle();
    }

    return true;
  }
}
