<?php

class pkContextCMSNavigationTree extends pkContextCMSNavigation
{ 
  
  public function buildNavigation($rootDepth = 2)
  {
    $tree = $this->rootPage->getTreeInfo($this->getLivingOnly(), $this->getOption('rootDepth'));
    $this->setItems($this->createObjects($tree, null));
    $this->buildTree($this->getItems());
  }
  
  private function createObjects($tree, $parent)
  {
    $navItems = array();
    $n = 0;
    $peerBit = false;
    $ancestorPeerBit = false;
    foreach($tree as $item)
    {
      $navItem = $this->buildNavigationItem($tree, $item, $n++);
      if($navItem->isCurrent())
      {
        $this->baseItem = $navItem;
        $peerBit = true;
      }
      elseif($navItem->isAncestor($this->activePage))
      {
        $ancestorPeerBit = false;
        $navItem->ancestorOfCurrentPage = true;
      }
      $navItem->setRelativeDepth($item['level'] - $this->rootPage->getLevel() - 1);
      $navItem->setParent($parent);
      //AbsoluteDepth is the same as level in pk_context_cms_page, need to rename to have better consistency
      $navItem->setAbsoluteDepth($item['level']);
      if(isset($item['children']))
      {
        $navItem->setChildren($this->createObjects($item['children'], $navItem));
      }
      $navItems[] = $navItem;
    }
    foreach($navItems as $navItem)
    {
      if($peerBit)
      {
        $navItem->peerOfCurrentPage = true;
      }
      elseif($ancestorPeerBit)
      {
        $navItem->peerOfAncestorOfCurrentPage = true;
      }
      $navItem->setPeers($navItems);
    } 
    return $navItems;
  }
  
  public function buildTree($items)
  {
    foreach($items as $item)
    {
      if(!$this->displayChildren($item))
      {
        $item->setChildren(array());
      }
      else
      {
        if($item->hasChildren())
        {
          $this->buildTree($item->getChildren());
        }
      }
    }
  }
  public function displayChildren($item)
  {
    if($item->ancestorOfCurrentPage || $item->isCurrent())
    {
      return true;
    }
    return false;
  }
}