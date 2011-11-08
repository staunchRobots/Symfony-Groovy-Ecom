<?php

class PluginPageTable extends Doctrine_Table
{
  public function retrieveByAction($pair)
  {
    return $this->createQuery()->where('module = ? AND action = ?', explode('/', $pair))->fetchOne();
  }

  public function retrieveTreeOrdered()
  {
    return $this->createQuery()->orderby('lft');
  }
  
  public function retrieveBySlug($slug)
  {
    $pages = $this->createQuery('p')
                  ->where('p.slug LIKE ?', $slug)
                  ->orderBy('p.level DESC')
                  ->fetchOne();

   return $pages;
  }
}