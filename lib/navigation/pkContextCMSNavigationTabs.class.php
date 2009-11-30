<?php

class pkContextCMSNavigationTabs extends pkContextCMSNavigation
{
  /**
   * @return array of pkContextNavigationItem objects
   */
  public function buildNavigation()
  {
    $children = $this->activePage->getTabsInfo($this->getLivingOnly());
    $items = array();
    $n = 0;
    
    foreach ($children as $pageInfo)
    {
      $items[] = $this->buildNavigationItem($children, $pageInfo, $n++);
    }

    $this->setItems($items);
  }
}