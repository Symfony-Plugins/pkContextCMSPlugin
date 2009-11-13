<?php

class pkContextCMSNavigationTree extends pkContextCMSNavigation
{  
  
  public function initialize()
  {
    $this->setOption('depth', isset($this->options['depth']) ? $this->options['depth'] : 2);
  }
  
  public function buildPageTree(pkContextCMSPage $page, $maxDepth=null)
  {
    $tree = $page->getTreeInfo($this->getLivingOnly(), $maxDepth);
    $this->setItems($this->recurseTree($tree, 0));
  }
  
  private function recurseTree($tree, $relativeDepth)
  {
    $items = array();
    $n = 0;
    foreach ($tree as $pageInfo)
    {      
      $item = $this->buildNavigationItem($tree, $pageInfo, $n++);
      $item->setRelativeDepth($relativeDepth);
      $item->setAbsoluteDepth($pageInfo['level']);
      $item->lft = $pageInfo['lft'];
      $item->rgt = $pageInfo['rgt'];
      $items[] = $item;
      if (isset($pageInfo['children']))
      {
        $item->setChildren($this->recurseTree($pageInfo['children'], $relativeDepth+1));
      }
    }
    return $items;
  }
  
  public function getDepth()
  {
    return $this->getOption('depth');
  }
}