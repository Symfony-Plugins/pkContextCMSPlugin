<?php

class pkContextCMSNavigationBreadcrumb extends pkContextCMSNavigation
{
  public function unsetItems($items)
  {
    foreach($items as $key => $item)
    {
      if(!$item->isAncestor($this->activePage) && !$item->isCurrent())
      {
        if(!is_null($item->getParent()))
        {
          $peers = $item->getParent()->getChildren();
          unset($peers[$key]);
          $item->getParent()->setChildren($peers);
        }
        else
        {
          unset($this->items[$key]);
        }
      }
      else
      {
        if($item->hasChildren())
        {
          $this->unsetItems($item->getChildren());
        }
      }
    }
  }
  
  public function buildNavigation($items)
  {
    $this->unsetItems($items);
  }
  
}