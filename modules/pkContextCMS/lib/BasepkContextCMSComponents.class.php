<?php

/**
 * pkContextCMS components.
 *
 * @package    simplesite
 * @subpackage pkContextCMS
 * @author     P'unk Ave
 */
class BasepkContextCMSComponents extends pkContextCMSBaseComponents
{
  public function executeBreadcrumb(sfRequest $request)
  {
    // Use our caching proxy implementation of getAncestors
    $this->page = pkContextCMSTools::getCurrentPage();
    $this->pages = $this->page->getAncestors();
  }
  public function executeSubnav(sfRequest $request)
  {
    // This really should be a well-written component,
    // but for the moment it just invokes specialized helpers
    // from the template
  }
  public function executeTabs(sfRequest $request)
  {
    $this->page = pkContextCMSTools::getCurrentPage();
    if (!$this->page)
    {
      // Tabs on non-CMS pages are relative to the home page
      $this->page = pkContextCMSPageTable::retrieveBySlug('/');
    }
    $ancestors = $this->page->getAncestors();
    if (!$ancestors)
    {
      $ancestors = array($this->page);
    }
    $home = $ancestors[0];
    $this->tabs = $home->getChildren(!$this->getUser()->getAttribute('show-archived', false, 'pk-context-cms'));
    $ids = pkArray::getIds($this->tabs);
    $this->logMessage("ZZ resulting list is " . implode(",", $ids));
    if (sfConfig::get('app_pkContextCMS_home_as_tab', true))
    {
      array_unshift($this->tabs, $home);
    }
    $this->draggable = $this->page->userHasPrivilege('edit');
  }
  public function executeSlot()
  {
    $this->setup();
    $controller = $this->getController();
    if ($controller->componentExists($this->type, "normalView"))
    {
      $this->normalModule = $this->type;
    }
    else
    {
      $this->normalModule = "pkContextCMS";
    }
    if ($controller->componentExists($this->type, "editView"))
    {
      $this->editModule = $this->type;
    }
    else
    {
      $this->editModule = "pkContextCMS";
    }
  }

  public function executeEditView()
  {
    $this->setup();
  }

  public function executeNormalView()
  {
    $this->setup();
  }

  public function executeArea()
  {
    $this->page = pkContextCMSTools::getCurrentPage();
    $this->slots = $this->page->getArea($this->name, $this->addSlot, true);
    $this->editable = $this->page->userHasPrivilege('edit');
    $user = $this->getUser();
    // Clean this up for nicer templates
    $this->refresh = (isset($this->refresh) && $this->refresh);
    $this->preview = (isset($this->preview) && $this->preview);
    $id = $this->page->id;
    $name = $this->name;
    if ($this->refresh)
    {
      if ($user->hasAttribute("area-options-$id-$name", "pkContextCMS"))
      {
        $this->options = $user->getAttribute("area-options-$id-$name", array(), "pkContextCMS");
      }
      else
      {
        // BZZT: probably a hack attempt
        throw new sfException("executeArea without options");
      }
    }
    else
    {
      $user->setAttribute("area-options-$id-$name", $this->options, "pkContextCMS");
    }
    $this->infinite = $this->getOption('infinite');
    if (!$this->infinite)
    {
      // Watch out for existing slots of the wrong type, which might contain data
      // that is incompatible with the singleton slot's type. That can happen if you
      // switch slot types in the template, or change from an area to a singleton slot.
      // Also ignore anything after the first slot (again, that can happen if you
      // switch from an area to a singleton slot)
      if (count($this->slots) > 1)
      {
        // Get the first one without being tripped up by the fact that it's a hash
        foreach ($this->slots as $key => $slot)
        {
          break;
        }
        $this->slots = array($key => $slot);
      }
      if (count($this->slots))
      {
        // Get the first one without being tripped up by the fact that it's a hash
        foreach ($this->slots as $key => $slot)
        {
          break;
        }
        if ($slot->type !== $this->options['type'])
        {
          $this->slots = array();
        }
      }
      if (!count($this->slots))
      {
        if (!isset($this->options['type']))
        {
          throw new sfException('Must specify type when embedding a singleton slot');
        }
        $this->slots[1] = $this->page->createSlot($this->options['type']);
      }
    }
  }
}
