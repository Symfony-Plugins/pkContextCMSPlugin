<?php

class pkContextCMSNavigationTabs extends pkContextCMSNavigation
{
  /**
   * @return array of pkContextNavigationItem objects
   */
  public function buildPageTree()
  {
    $children = $this->getPage()->getTabsInfo($this->getLivingOnly());
    $items = array();
    $n = 0;
    
    foreach ($children as $pageInfo)
    {
      $items[] = $this->buildNavigationItem($children, $pageInfo, $n++);
    }

    return $items;
  }
}