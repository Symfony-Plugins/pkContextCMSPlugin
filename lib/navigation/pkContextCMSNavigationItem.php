<?php

class pkContextCMSNavigationItem
{
  protected $name = '';
  protected $url = '';
  protected $first = false;
  protected $last = false;
  protected $current = false;
  protected $options = array();
  protected $children = array();
  protected $absoluteDepth;
  protected $relativeDepth;
  public $ancestorOfCurrentPage = false;
  public $peerOfAncestorOfCurrentPage = false;
  public $peerOfCurrentPage = false;

  public function __construct($pageInfo, $url, $options = array(), $children = array())
  {
    $this->name = $pageInfo['title'];
    $this->url = $url;
    $this->lft = $pageInfo['lft'];
    $this->rgt = $pageInfo['rgt'];
    
    $this->options = $options;
    $this->first = isset($this->options['first']) ? $this->options['first'] : '';
    $this->last = isset($this->options['last']) ? $this->options['last'] : '';
    $this->current = isset($this->options['current']) ? $this->options['current'] : '';
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function getUrl()
  {
    return $this->url;
  }
  
  public function isLast()
  {
    return $this->last;
  }
  
  public function isFirst()
  {
    return $this->first;
  }
  
  public function isCurrent()
  {
    return $this->current;
  }

  public function setChildren($items = array())
  {
    $this->children = $items;
  }
  
  public function getChildren()
  {
    return $this->children;
  }
  
  public function hasChildren()
  {
    return count($this->children) > 0;
  }
  
  public function setRelativeDepth($relativeDepth)
  {
    $this->relativeDepth = $relativeDepth;
  }
  
  public function getRelativeDepth()
  {
    return $this->relativeDepth;
  }
  
  public function setAbsoluteDepth($absoluteDepth)
  {
    $this->absoluteDepth = $absoluteDepth;
  }
  
  public function getAbsoluteDepth()
  {
    return $this->absoluteDepth;
  }
  
  public function isAncestor(pkContextCMSPage $page)
  {
    return ($page->lft > $this->lft && $page->rgt < $this->rgt)? true : false;
  }
  
  public function isDescendant(pkContextCMSPage $page, $offset=null)
  {
    if($this->lft > $page->lft && $this->rgt < $page->rgt)
    {
      if(isset($offset))
      {
        return $page->getLevel() + $offset >= $this->getAbsoluteDepth(); 
      }
      return true;
    }
    return false;
  }
  
  public function isAncestorOfCurrentPage()
  {
    return $ancestorOfCurrentPage;
  }
  
  public function isAncestorPeerOfCurrentPage()
  {
    return $peerOfAncestorOfCurrentPage; 
  }
  
}

?>