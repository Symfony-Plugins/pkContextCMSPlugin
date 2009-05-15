<?php

/**
 * pkContextCMS actions.
 *
 * @package    simplesite
 * @subpackage pkContextCMS
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class BasepkContextCMSActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }
  public function executeShow(sfWebRequest $request)
  {
    $slug = $this->getRequestParameter('slug');
    if (substr($slug, 0, 1) !== '/')
    {
      $slug = "/$slug";
    }
    $page = pkContextCMSPageTable::retrieveBySlugWithSlots($slug);
    $this->forward404Unless($page);
    if (!$page->userHasPrivilege('view'))
    {
      // forward rather than login because referrers don't always
      // work. Hopefully the login action will capture the original
      // URI to bring the user back here afterwards.

      if ($this->getUser()->isAuthenticated())
      {
        return $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
      }
      else
      {
        return $this->forward(sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action'));

      }
    }
    if ($page->archived && (!$page->userHasPrivilege('edit|manage')))
    {
      $this->forward404();
    }
    // Title is pre-escaped as valid HTML
    $prefix = pkContextCMSTools::getOptionI18n('title_prefix');
    $this->getResponse()->setTitle($prefix . $page->getTitle(), false);
    $this->page = $page;
    // Necessary to allow the use of
    // pkContextCMSTools::getCurrentPage() in the layout.
    // In Symfony 1.1+, you can't see $this->page from
    // the layout.
    pkContextCMSTools::setCurrentPage($page);
    $this->setTemplate($page->template);
    // Borrowed from sfSimpleCMS
    if(sfConfig::get('app_pkContextCMS_use_bundled_layout', true))
    {
      $this->setLayout(sfContext::getInstance()->getConfiguration()->getTemplateDir('pkContextCMS', 'layout.php').'/layout');
    }
    if (sfConfig::get('app_pkContextCMS_use_bundled_stylesheet', true))
    {
      $this->getResponse()->addStylesheet('/pkContextCMSPlugin/css/pkContextCMS.css', 'last');
      $this->getResponse()->addStylesheet('/pkContextCMSPlugin/css/pkContextCMSButtons.css', 'last');
    }
    return 'Template';
  }

  // Note that these fetch based on the id or slug found in the
  // named parameter of the request

  protected function retrievePageForEditingById($parameter = 'id', $privilege = 'edit|manage')
  {
    $page = pkContextCMSPageTable::retrieveByIdWithSlots(
      $this->getRequestParameter($parameter));
    $this->validAndEditable($page, $privilege);
    return $page;
  }

  protected function retrievePageForEditingBySlug($parameter = 'slug', $privilege = 'edit|manage')
  {
    $page = pkContextCMSPageTable::retrieveBySlugWithSlots(
      $this->getRequestParameter($parameter));
    $this->validAndEditable($page, $privilege);
    return $page;
  }

  protected function validAndEditable($page, $privilege = 'edit|manage')
  {
    $this->forward404Unless($page);
    $this->forward404Unless($page->userHasPrivilege($privilege));
  }

  public function executeSort(sfRequest $request)
  {
    return $this->sortBodyWrapper('pk-context-cms-navcolumn-page');
  }
  
  public function executeSortTabs(sfRequest $request)
  {
    return $this->sortBodyWrapper('pk-context-cms-site-navigation', '/');
  }

  protected function sortBodyWrapper($parameter, $slug = false)
  {
    $request = $this->getRequest();
    if ($slug !== false)
    {
      $page = pkContextCMSPageTable::retrieveBySlugWithSlots($slug);
      $this->validAndEditable($page, 'edit');
    } 
    else
    {
      $page = $this->retrievePageForEditingBySlugId('page');
    }
    $this->forward404Unless($page);
    if (!$page->getNode()->hasChildren())
    {
      $page = $page->getNode()->getParent();
      $this->forward404Unless($page);
    }
    $order = $this->getRequestParameter($parameter);
    $this->forward404Unless(is_array($order));
    $this->sortBody($page, $order);
    return sfView::NONE;
  }

  protected function sortBody($parent, $order)
  {
    $this->logMessage("ZZ PARENT IS " . $parent->slug);
    // ACHTUNG: I've made attempts to rewrite this more efficiently. They resulted in
    // corrupted nested sets. Corrupted nested sets equal corrupted site page hierarchies
    // equal VERY BAD. I suggest leaving this rarely invoked function the way it is.
    foreach ($order as $id)
    {
      $child = Doctrine::getTable('pkContextCMSPage')->find($id);
      if (!$child)
      {
        $this->logMessage("ZZ skipping non-page");
        continue;
      }
      if ($child->getNode()->getParent() != $parent)
      {
        $this->logMessage("ZZ skipping non-child");
        continue;
      }
      $this->logMessage("ZZ MOVING $id");
      $child->getNode()->moveAsLastChildOf($parent);
    }
    // Now: did that work consistently?
    $children = $parent->getNode()->getChildren();
    $this->logMessage("ZZ resulting order is " . implode(",", pkArray::getIds($children)));
  }

  public function executeRename(sfRequest $request)
  {
    $page = $this->retrievePageForEditingById();
    $this->forward404Unless($page);
    $this->forward404Unless($page->userHasPrivilege('edit|manage'));    
    // Rename never changes the slug, just the title
    $page->setTitle(htmlspecialchars($request->getParameter('title')));
    $page->save();
    return $this->redirect($page->getUrl());
  }

  public function executeShowArchived(sfRequest $request)
  {
    $page = $this->retrievePageForEditingById();
    $this->state = $request->getParameter('state');
    $this->getUser()->setAttribute(
      'show-archived', $this->state, 'pk-context-cms');
    if (!$this->state)
    {
      while ($page->getArchived())
      {
        $page = $page->getNode()->getParent();
      }
    }
    return $this->redirect($page->getUrl());
  }

  public function executeCreate()
  {
    $this->forward404Unless($this->getRequest()->getMethod() == sfRequest::POST);
    $parent = $this->retrievePageForEditingBySlug('parent', 'manage');
    
    $title = trim($this->getRequestParameter('title'));

    $this->forward404Unless(strlen($title));

    $pathComponent = $title;
    $pathComponent = strtolower(preg_replace("/[\W]+/", "-", $title));

    $base = $parent->getSlug();
    if ($base === '/')
    {
      $base = '';
    }
    $slug = "$base/$pathComponent";

    $page = new pkContextCMSPage();
    $page->setArchived(!sfConfig::get('app_pkContextCMS_default_on', true));

    $page->setSlug($slug);
    $existingPage = pkContextCMSPageTable::retrieveBySlug($slug);
    if ($existingPage) {
      // TODO: an error in addition to displaying the existing page?
      return $this->redirect($existingPage->getUrl());
    } else { 
      $page->getNode()->insertAsFirstChildOf($parent);

      // Figure out what template this new page should use based on
      // the template rules. 
      //
      // The default rule assigns default to everything.

      $rule = pkRules::select(
        sfConfig::get('app_pkContextCMS_template_rules', 
        array(
          array('rule' => '*',
            'template' => 'default'))), $slug);
      if (!$rule)
      {
        $template = 'default';
      }
      else
      {
        $template = $rule['template'];
      }
      $page->template = $template;
      // Unpublished pages don't show up in the breadcrumb trail,
      // and we don't have a UI for it yet anyway.
      $page->is_published = true;
      $page->setTitle(htmlspecialchars($title));
      $page->save();
      return $this->redirect($page->getUrl());
    }
  }

  public function executeHistory()
  {
    // Careful: if we don't build the query our way,
    // we'll get *all* slots as soon as we peek at ->slots,
    // including slots that are not current etc.
    $page = $this->retrievePageForEditingById();
    $name = $this->getRequestParameter('name');
    $this->versions = $page->getAreaVersions($name);
    $this->id = $page->id;
    $this->version = $page->getAreaCurrentVersion($name);
    $this->name = $name;
  }
  
  public function executeAddSlot(sfRequest $request)
  {
    $page = $this->retrievePageForEditingById();
    pkContextCMSTools::setCurrentPage($page);
    $this->name = $this->getRequestParameter('name');
    $this->type = $this->getRequestParameter('type');
    $options = pkContextCMSTools::getAreaOptions($page->id, $this->name);

    // TODO: validate the user's choice of slot type against the
    // allowed slots for this area, not just the allowed slots globally.
    // The problem is that 
    // There is very little harm in this as slots rarely have
    // security implications, but custom slots someday might.
    if (!in_array($this->type, array_keys(pkContextCMSTools::getSlotTypeOptions($options))))
    {
      $this->forward404();
    }
  }

  public function executeMoveSlot(sfRequest $request)
  {
    $page = $this->retrievePageForEditingById();
    pkContextCMSTools::setCurrentPage($page);
    $this->name = $this->getRequestParameter('name');
    $slots = $page->getArea($this->name);
    $permid = $this->getRequestParameter('permid');
    if (count($slots))
    {
      $permids = array_keys($slots);
      $index = array_search($permid, $permids);
      if ($request->getParameter('up'))
      {
        $limit = 0;
        $difference = -1;
      }
      else
      {
        $limit = count($slots) - 1;
        $difference = 1;
      }
      if (($index !== false) && ($index != $limit))
      {
        $t = $permids[$index + $difference];
        $permids[$index + $difference] = $permid;
        $permids[$index] = $t;
        $page->newAreaVersion($this->name, 'sort', 
          array('permids' => $permids));
        $page = pkContextCMSPageTable::retrieveByIdWithSlots(
          $request->getParameter('id'));
        $this->forward404Unless($page);
        pkContextCMSTools::setCurrentPage($page);
      }
    }
  }

  public function executeDeleteSlot(sfRequest $request)
  {
    $page = $this->retrievePageForEditingById();
    pkContextCMSTools::setCurrentPage($page);
    $this->name = $this->getRequestParameter('name');
    $page->newAreaVersion($this->name, 'delete', 
      array('permid' => $this->getRequestParameter('permid')));
    $page = pkContextCMSPageTable::retrieveByIdWithSlots(
      $request->getParameter('id'));
    $this->forward404Unless($page);
    pkContextCMSTools::setCurrentPage($page);
  }

//  Part of an AJAX implementation of slot saving which we
//  don't seem to be able to trust yet due to FCK issues
//  public function executeAfterEdit(sfRequest $request)
//  {
//    $page = pkContextCMSPageTable::retrieveByIdWithSlots(
//      $request->getParameter('id'));
//    $this->forward404Unless($page);
//    pkContextCMSTools::setCurrentPage($page);
//    $this->name = $this->getRequestParameter('name');
//  }

  public function executeRevert(sfRequest $request)
  {
    $version = false;
    $subaction = $request->getParameter('subaction');
    $this->preview = false;
    if ($subaction == 'preview')
    {
      $version = $request->getParameter('version');
      $this->preview = true;
    }
    elseif ($subaction == 'revert')
    {
      $version = $request->getParameter('version');
    }
    $id = $request->getParameter('id');
    $page = pkContextCMSPageTable::retrieveByIdWithSlotsForVersion($id, $version);
    $this->forward404Unless($page);
    $this->forward404Unless($page->userHasPrivilege('edit|manage'));    
    $this->name = $this->getRequestParameter('name');
    if ($subaction == 'revert')
    {
      $page->newAreaVersion($this->name, 'revert');
      $page = pkContextCMSPageTable::retrieveByIdWithSlots($id);
    }
    pkContextCMSTools::setCurrentPage($page);
    $this->cancel = ($subaction == 'cancel');
    $this->revert = ($subaction == 'revert');
  }

  public function executeSettings(sfRequest $request)
  {
    $from = 'id';
    if ($request->hasParameter('settings[id]'))
    {
      $from = 'settings[id]';
    }
    $this->page = $this->retrievePageForEditingById($from);
    $this->form = new pkContextCMSPageSettingsForm($this->page);
    if ($from === 'settings[id]')
    {
      $this->form->bind($request->getParameter("settings"));
      if ($this->form->isValid())
      {
        $this->logMessage("YY settings is valid");
        $this->form->save();
        // Oops must be case correct in production
        return 'Redirect';
      }
    }
    // This might make more sense in some kind of read-only form control.
    // TODO: cache the first call that the form makes so this doesn't
    // cause more db traffic.
    $this->inherited = array();
    $this->admin = array();
    $this->addPrivilegeLists('edit');
    $this->addPrivilegeLists('manage');
  }

  protected function addPrivilegeLists($privilege)
  {
    list($all, $selected, $inherited, $sufficient) = $this->page->getAccessesById($privilege);
    $this->inherited[$privilege] = array();
    foreach ($inherited as $userId)
    {
      $this->inherited[$privilege][] = $all[$userId];
    }
    $this->admin[$privilege] = array();
    foreach ($sufficient as $userId)
    {
      $this->admin[$privilege][] = $all[$userId];
    }
  }

  public function executeDelete()
  {
    $page = $this->retrievePageForEditingById('id', 'manage');
    $parent = $page->getParent();
    // tom@punkave.com: we must delete via the nested set
    // node or we'll corrupt the tree. Nasty detail, that.
    // Note that this implicitly calls $page->delete()
    // (but the reverse was not true and led to problems).
    $page->getNode()->delete(); 
    return $this->redirect($parent->getUrl());
  }
  
  public function executeSearch(sfRequest $request)
  {
    $q = $request->getParameter('q');
    $query = pkContextCMSPageTable::queryWithSlots();
    $query = Doctrine::getTable('pkContextCMSPage')->addSearchQuery($query, $q);
    $user = $this->getUser();
    if (!$user->isAuthenticated())
    {
      // I should have made this a non-nullable field but it's too late now
      $query->andWhere($query->getRootAlias() . ".view_is_secure is null or " .
        $query->getRootAlias() . ".view_is_secure = false");
    }
    $this->pager = new sfDoctrinePager(
      'pkContextCMSPage',
      sfConfig::get('app_pkContextCMS_search_results_per_page', 10));
    $this->results = $query->execute();
    $this->pager->setQuery($query);
    $page = $request->getParameter('page', 1);
    $this->pager->setPage($page);
    $this->pager->init();
    $this->results = $this->pager->getResults();
    $this->pagerUrl = "pkContextCMS/search?" .
            http_build_query(array("q" => $q));
  }
}
