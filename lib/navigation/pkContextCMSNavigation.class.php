<?php

abstract class pkContextCMSNavigation
{
  protected $user = null;
  protected $page = null;
  protected $type = null;
  protected $livingOnly = true;
  protected $items = array();
  
  abstract protected function buildPageTree(pkContextCMSPage $page, $maxDepth = null);
  
  public function __construct(pkContextCMSPage $page, $options = array())
  {
    $this->user = sfContext::getInstance()->getUser();
    $this->livingOnly = ($this->user->getAttribute('show-archived', false, 'pk-context-cms')) ? false : true;

    $this->setPage($page);
    $this->setOptions($options);

    $this->initialize();
    $this->buildPageTree($page, null);
  }

  public function initialize()
  {
    
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
      'current' => ((pkContextCMSTools::getCurrentPage() === $pageInfo['slug']) ? true : false)
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