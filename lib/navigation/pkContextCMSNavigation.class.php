<?php

abstract class pkContextCMSNavigation
{
  protected $user = null;
  protected $page = null;
  protected $type = null;
  protected $livingOnly = true;
  protected $items = array();
  
  protected $baseItem;
  
  protected $showPeers = true;
  protected $showAncestors = true;
  protected $showAncestorPeers = true;
  // showDescendants is an offset that determines how many levels below the currentPage to show children for, need to implement same
  // functionality for showAncestors 
  protected $showDescendants = 1;
  protected $showCurrent = true;
  
  protected abstract function unsetItems($items);
  
  public function buildPageTree(pkContextCMSPage $page, $maxDepth=null)
  {
    $tree = $page->getTreeInfo($this->getLivingOnly(), null);

    $this->setItems($this->createObjects($tree, null));
    $this->setParameters($this->getItems(), true, false, 0);
    $this->unsetItems($this->getItems());
  }
  
  private function createObjects($tree, $parent)
  {
    $navItems = array();
    $n = 0;
    foreach($tree as $item)
    {
      $navItem = $this->buildNavigationItem($tree, $item, $n++);
      if($navItem->isCurrent())
      {
        $this->baseItem = $navItem;
      }
      $navItem->setRelativeDepth($item['level'] - $this->page->getLevel() - 1);
      $navItem->setParent($parent);
      //AbsoluteDepth is the same as level in pk_context_cms_page, need to rename to have better consistency
      $navItem->setAbsoluteDepth($item['level']);
      if(isset($item['children']))
      {
        $navItem->setChildren($this->createObjects($item['children'], $navItem));
      }
      $navItems[] = $navItem;
    }
    return $navItems;
  }
  
  
  private function setParameters($tree, $peer, $descendant, $nodeCount)
  {
    foreach($tree as $item)
    {
      $nodeCount++;
      $item->id = $nodeCount;
      //Move check if item is current page to here
      $descendantBit = ($item->isCurrent())? true : $descendant;
      $ancestor = ($item->isAncestor(pkContextCMSTools::getCurrentPage())) ? true : $peer;
      
      if($item->isAncestor(pkContextCMSTools::getCurrentPage()))
      {
        $item->ancestorOfCurrentPage = true;
        foreach ($tree as $ancestorPeer)
        {
          $ancestorPeer->peerOfAncestorOfCurrentPage = true;
        }
      }

      //Check if $item is peer of base page
      if($peer && pkContextCMSTools::getCurrentPage()->getLevel() == $item->getAbsoluteDepth())
      {
        $item->peerOfCurrentPage = true && !$item->isCurrent();
      }
      
      //Item is descendant of base page
      $item->descendantOfBasePage = $descendantBit && !$item->isCurrent();
      
      if($item->hasChildren())
      {
        $this->setParameters($item->getChildren(), $ancestor, $descendantBit, $nodeCount);
      }
    }
  }
  
  public function __construct(pkContextCMSPage $page, $options = array())
  {
    $this->user = sfContext::getInstance()->getUser();
    $this->livingOnly = ($this->user->getAttribute('show-archived', false, 'pk-context-cms')) ? false : true;

    $this->setPage($page);
    $this->setOptions($options);

    $this->buildPageTree($page, null);
  }
  
  /**
   * Builds a navigation object with options for its position in the page tree.
   *
   * @param $pages array The entire page tree.
   * @param $pageInfo array Information about the page we want to create an object from.
   * @param $pos int The pages position inside the current level of the page tree.
   * @return pkContextCMSNavigationItem object
   */
  public function buildNavigationItem($pages, $pageInfo, $pos)
  {
    return new pkContextCMSNavigationItem($pageInfo, pkContextCMSTools::urlForPage($pageInfo['slug']), array(
      'first' => (($pos == 0) ? true : false),
      'last' => (($pos == count($pages) - 1) ? true : false),
      'current' => ((pkContextCMSTools::getCurrentPage()->getSlug() == $pageInfo['slug']) ? true : false)
    ));
  }
  
  protected function setItems($items)
  {
    $this->items = $items;
  }
  
  public function getItems()
  {
    return $this->items;
  }
  
  /**
   * Returns whether or not to display archived pages.
   * Admins can enable the ability to view archived pages in the CMS.
   */
  public function getLivingOnly()
  {
    return $this->livingOnly;
  }

  /**
   * Returns the page that the user is calling the navigation from.
   */
  public function getPage()
  {
    return $this->page;
  }
  
  public function setPage($page)
  {
    $this->page = $page;
  }
  
  public function getOptions()
  {
    return $this->options; 
  }
  
  public function getOption($name, $default = null)
  {
    return (isset($this->options[$name])) ? $this->options[$name] : $default;
  }
  
  public function setOptions($options = array())
  {
    $this->options = $options;
  }
  
  public function setOption($name, $option)
  {
    $this->options[$name] = $option;
  }
  
}