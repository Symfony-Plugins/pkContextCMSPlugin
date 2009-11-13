<?php

class pkContextCMSNavigationTree extends pkContextCMSNavigation
{  
  
  public $nodeCount = 0;
  /* All of the show variables correspond to the currentPage, i.e. show peers of currentPage, the page passed to the constructor
   * specifies the page to start building the navigation tree at.
   * 
   * The navigation tree needed for kimberton can be setting the following values
   * showPeers = false, showAncestors = true, showAncestorPeers = false, showDescendants = 1, showCurrent = true
   * 
   * Breadcrumb navigation can be created by setting the following values, however getTree info does not currently return the 
   * homepage in the tree structure so this will not be included in the breadcrumb
   * showPeers = false, showAncestors = true, showAncestorPeers = false, showDescendants = false, showCurrent = true
   * 
   * The standard tabs that are in most pages of pkContextCMS can be created with the following values, making sure to pass the
   * current page to the constructor and not the root page
   * showPeers = true, showAncestors = false, showAncestorPeers = false, showDescendants = false, showCurrent = true
   * 
   * Setting showAnscestors to false will break the NavigationClass unless the page passed to the constructor is the currentPage
   * 
   * The constructor also does not accept these values yet, which effectively makes that class a bit unusable at the moment, a small
   * change that will come
   * 
   * This class could be refactored into the parent class as it can perform the task of its sibling classes, with proper specification
   * of parameters
   * 
   * Because the currentPage will not exist in the navigation tree structure, there may be problems that arrise when the navigation is
   * used on non CMS pages, should be explored, a likely fix would be to remove references to pkContextCMSTools::getCurrentPage() and 
   * instead use the root page as the current page could be done by having the constructor check to see if the currentPage is valid, 
   * and if not setting it to the root page.
   * 
   */
  protected $showPeers = true;
  protected $showAncestors = true;
  protected $showAncestorPeers = true;
  // showDescendants is an offset that determines how many levels below the currentPage to show children for, need to implement same
  // functionality for showAncestors 
  protected $showDescendants = 1;
  protected $showCurrent = true;
  
  
  public function initialize()
  {
    $this->setOption('depth', isset($this->options['depth']) ? $this->options['depth'] : 2);
        
  }
  
  public function buildPageTree(pkContextCMSPage $page, $maxDepth=null)
  {
    $tree = $page->getTreeInfo($this->getLivingOnly(), null);
    $this->setItems($this->recurseTree($tree, 0));
    
  }
  
  private function recurseTree($tree, $relativeDepth)
  {
    $items = array();
    $shownItems = array();
    $n = 0;
    $ancestorFlag = false;
    $peerFlag = false;
    foreach ($tree as $pageInfo)
    { 
      $this->nodeCount++;     
      $item = $this->buildNavigationItem($tree, $pageInfo, $n++);
      
      $item->setRelativeDepth($relativeDepth);
      //AbsoluteDepth is the same as level in pk_context_cms_page, need to rename to have better consistency
      $item->setAbsoluteDepth($pageInfo['level']);
      $item->id = $this->nodeCount;
      
      //If a node is the current page than the other members of its array are its peers, set peerFlag
      $peerFlag = $item->getName() == pkContextCMSTools::getCurrentPage() ? true : $peerFlag;

      //If a node is an ancestor of the current page than other members of its array are ancestorPeers, set ancestorFlag
      $ancestorFlag = $item->isAncestor(pkContextCMSTools::getCurrentPage())? true : $ancestorFlag;
      $item->ancestorOfCurrentPage = $ancestorFlag;
      
      
      $items[] = $item;
      if (isset($pageInfo['children']))
      {
        $item->setChildren($this->recurseTree($pageInfo['children'], $relativeDepth+1));
      }
    }
    
    /*The code below needs to be cleaned up some
     * Because the tree is traversed in preorder fashion it is difficult to set the peer bit and ancestorPeer bit during
     * the traversal so they are set after all items in a leaf level are traversed.
     * 
     * The logic here is a bit screwy for the time being, the goal is to set a couple variables in the item and remove items that
     * shouldn't be in the tree because they are of a type that is turned off from display.
     */
    foreach($items as $key=>$item)
    {
      
      if ($item->isCurrent() && $this->showCurrent)
      {
        echo '';
      }
      elseif ($peerFlag == true && $this->showPeers && !$item->isCurrent())
      {
        $item->peerOfCurrentPage = true;
      }
      elseif ($ancestorFlag == true && $this->showAncestors && $item->isAncestor(pkContextCMSTools::getCurrentPage()))
      {
        $item->ancestorOfCurrentPage = true;
        
      }
      elseif ($ancestorFlag == true && $this->showAncestorPeers)
      {
        $item->peerOfAncestorOfCurrentPage = $item->ancestorOfCurrentPage;
        
      }
      elseif ($item->isDescendant(pkContextCMSTools::getCurrentPage(), $this->showDescendants) && $this->showDescendants)
      {
        
      }
      else
      {
        unset($items[$key]);
      }
    }
    
    return $items;
  }
  
  public function getDepth()
  {
    return $this->getOption('depth');
  }
}