<?php

class pkContextCMSNavigationTree extends pkContextCMSNavigation
{    
  public function unsetItems($items)
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
          $this->unsetItems($item->getChildren());
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