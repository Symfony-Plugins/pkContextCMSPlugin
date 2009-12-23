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
    pkContextCMSTools::validatePageAccess($this, $page);
    pkContextCMSTools::setPageEnvironment($this, $page);
    $this->page = $page;
    $this->setTemplate($page->template);

    return 'Template';
  }
  
  protected function retrievePageForEditingByIdParameter($parameter = 'id', $privilege = 'edit|manage')
  {
    return $this->retrievePageForEditingById($this->getRequestParameter($parameter));
  }
  
  protected function retrievePageForEditingById($id, $privilege = 'edit|manage')
  {
    $page = pkContextCMSPageTable::retrieveByIdWithSlots($id);
    $this->validAndEditable($page, $privilege);
    return $page;
  }

  protected function retrievePageForEditingBySlugParameter($parameter = 'slug', $privilege = 'edit|manage')
  {
    return $this->retrievePageForEditingBySlug($this->getRequestParameter($parameter));
  }

  protected function retrievePageForEditingBySlug($slug, $privilege = 'edit|manage')
  {
    $page = pkContextCMSPageTable::retrieveBySlugWithSlots($slug);
    $this->validAndEditable($page, $privilege);
    return $page;
  }

  protected function validAndEditable($page, $privilege = 'edit|manage')
  {
    $this->flunkUnless($page);
    $this->flunkUnless($page->userHasPrivilege($privilege));
  }

  public function executeSort(sfRequest $request)
  {
    return $this->sortBodyWrapper('pk-navcolumn');
  }
  
  public function executeSortTree(sfRequest $request)
  {
    return $this->sortBodyWrapper('pk-navcolumn');
  }
  
  public function executeSortTabs(sfRequest $request)
  {
    return $this->sortBodyWrapper('pk-tab-nav-item', '/');
  }
  
  public function executeSortNav(sfRequest $request)
  {
    return $this->sortNavWrapper('pk-tab-nav-item');
  }
  
  protected function sortNavWrapper($parameter)
  {
    $request = $this->getRequest();
    $page = $this->retrievePageForEditingByIdParameter('page');
    $page = $page->getNode()->getParent();
    $this->validAndEditable($page, 'edit');
    $this->flunkUnless($page);
    $order = $this->getRequestParameter($parameter);
    $this->flunkUnless(is_array($order));
    $this->sortBody($page, $order);
    return sfView::NONE;
  }

  protected function sortBodyWrapper($parameter, $slug = false)
  {
    $request = $this->getRequest();
    $this->logMessage("ZZ sortBodyWrapper");
    if ($slug !== false)
    {
      $page = pkContextCMSPageTable::retrieveBySlugWithSlots($slug);
      $this->logMessage("ZZ got slug by slots");
      $this->validAndEditable($page, 'edit');
      $this->logMessage("ZZ is valid and editable");
    } 
    else
    {
      $page = $this->retrievePageForEditingByIdParameter('page');
      $this->logMessage("ZZ got page for editing by id");      
    }
    $this->logMessage("ZZ Page is " . $page->id, "info");
    $this->flunkUnless($page);
    if (!$page->getNode()->hasChildren())
    {
      $page = $page->getNode()->getParent();
      $this->logMessage("ZZ bumping up to parent");
      $this->flunkUnless($page);
    }
    $order = $this->getRequestParameter($parameter);
    ob_start();
    var_dump($_REQUEST);
    $this->logMessage("ZZ request is " . ob_get_clean());
    $this->logMessage("ZZ is_array order: " . is_array($order));
    $this->flunkUnless(is_array($order));
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
    $page = $this->retrievePageForEditingByIdParameter();
    $this->flunkUnless($page);
    $this->flunkUnless($page->userHasPrivilege('edit|manage'));    
    // Rename never changes the slug, just the title
    $page->setTitle(htmlspecialchars($request->getParameter('title')));
    return $this->redirect($page->getUrl());
  }

  public function executeShowArchived(sfRequest $request)
  {
    $page = $this->retrievePageForEditingByIdParameter();
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
    $this->flunkUnless($this->getRequest()->getMethod() == sfRequest::POST);
    $parent = $this->retrievePageForEditingBySlugParameter('parent', 'manage');
    
    $title = trim($this->getRequestParameter('title'));

    $this->flunkUnless(strlen($title));

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
      // Must save the page BEFORE we call setTitle, which has the side effect of
      // refreshing the page object
      $page->save();
      $page->setTitle(htmlspecialchars($title));
      return $this->redirect($page->getUrl());
    }
  }

  public function executeHistory()
  {
    // Careful: if we don't build the query our way,
    // we'll get *all* slots as soon as we peek at ->slots,
    // including slots that are not current etc.
    $page = $this->retrievePageForEditingByIdParameter();
    $name = $this->getRequestParameter('name');
    $all = $this->getRequestParameter('all');
    $this->versions = $page->getAreaVersions($name, false, isset($all)? null : 10);
    $this->id = $page->id;
    $this->version = $page->getAreaCurrentVersion($name);
    $this->name = $name;
    $this->all = $all;
  }
  
  public function executeAddSlot(sfRequest $request)
  {
    $page = $this->retrievePageForEditingByIdParameter();
    pkContextCMSTools::setCurrentPage($page);
    $this->name = $this->getRequestParameter('name');
    $this->type = $this->getRequestParameter('type');
    $options = pkContextCMSTools::getAreaOptions($page->id, $this->name);

    // TODO: validate the user's choice of slot type against the
    // allowed slots for this area, not just the allowed slots globally.
    // There is very little harm in this as slots rarely have
    // security implications, but custom slots someday might.
    if (!in_array($this->type, array_keys(pkContextCMSTools::getSlotTypeOptions($options))))
    {
      $this->forward404();
    }
  }

  public function executeMoveSlot(sfRequest $request)
  {
    $page = $this->retrievePageForEditingByIdParameter();
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
        $this->flunkUnless($page);
        pkContextCMSTools::setCurrentPage($page);
      }
    }
  }

  public function executeDeleteSlot(sfRequest $request)
  {
    $page = $this->retrievePageForEditingByIdParameter();
    pkContextCMSTools::setCurrentPage($page);
    $this->name = $this->getRequestParameter('name');
    $page->newAreaVersion($this->name, 'delete', 
      array('permid' => $this->getRequestParameter('permid')));
    $page = pkContextCMSPageTable::retrieveByIdWithSlots(
      $request->getParameter('id'));
    $this->flunkUnless($page);
    pkContextCMSTools::setCurrentPage($page);
  }

//  Part of an AJAX implementation of slot saving which we
//  don't seem to be able to trust yet due to FCK issues
//  public function executeAfterEdit(sfRequest $request)
//  {
//    $page = pkContextCMSPageTable::retrieveByIdWithSlots(
//      $request->getParameter('id'));
//    $this->flunkUnless($page);
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
    $this->flunkUnless($page);
    $this->flunkUnless($page->userHasPrivilege('edit|manage'));    
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
    if ($request->hasParameter('settings'))
    {
      $settings = $request->getParameter('settings');
      $this->page = $this->retrievePageForEditingById($settings['id']);
    }
    else
    {
      $this->page = $this->retrievePageForEditingByIdParameter();
    }
    
    $this->form = new pkContextCMSPageSettingsForm($this->page);
    if ($request->hasParameter('settings'))
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
    $page = $this->retrievePageForEditingByIdParameter('id', 'manage');
    $parent = $page->getParent();
    if (!$parent)
    {
      // You can't delete the home page, I don't care who you are; creates a chicken and egg problem
      return $this->redirect('@homepage');
    }
    // tom@punkave.com: we must delete via the nested set
    // node or we'll corrupt the tree. Nasty detail, that.
    // Note that this implicitly calls $page->delete()
    // (but the reverse was not true and led to problems).
    $page->getNode()->delete(); 
    return $this->redirect($parent->getUrl());
  }
  
  public function executeSearch(sfRequest $request)
  {
    // create the array of pages matching the query
    $q = $request->getParameter('q');
    
    if ($request->hasParameter('x'))
    {
      // We like to use input type="image" for presentation reasons, but it generates
      // ugly x and y parameters with click coordinates. Get rid of those and come back.
      return $this->redirect(sfContext::getInstance()->getController()->genUrl('pkContextCMS/search', true) . '?' .
    http_build_query(array("q" => $q)));
    }
    
    $key = strtolower(trim($q));
    $key = preg_replace('/\s+/', ' ', $key);
    $replacements = sfConfig::get('app_pkContextCMS_search_refinements', array());
    if (isset($replacements[$key]))
    {
      $q = $replacements[$key];
    }

    $values = pkZendSearch::searchLuceneWithValues(Doctrine::getTable('pkContextCMSPage'), $q, pkContextCMSTools::getUserCulture());

    $nvalues = array();

    foreach ($values as $value)
    {
      if (!sfContext::getInstance()->getUser()->isAuthenticated())
      {
        if (isset($value->view_is_secure) && $value->view_is_secure)
        {
          continue;
        }
      }
      $nvalue = $value;      
      $nvalue->url = pkContextCMSTools::urlForPage($nvalue->slug, true);
      $nvalue->class = 'pkContextCMSPage';
      $nvalues[] = $nvalue;
    }
    $values = $nvalues;

    if ($this->searchAddResults($values, $q))
    {
      usort($values, "pkContextCMSActions::compareScores");
    }
    $this->pager = new pkArrayPager(null, sfConfig::get('app_pkContextCMS_search_results_per_page', 10));    
    $this->pager->setResultArray($values);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
    $this->pagerUrl = "pkContextCMS/search?" .http_build_query(array("q" => $q));
    // setTitle takes care of escaping things
    $this->getResponse()->setTitle(pkContextCMSTools::getOptionI18n('title_prefix') . 'Search for ' . $q);
    $this->results = $this->pager->getResults();
  }
  
  protected function searchAddResults(&$values, $q)
  {
    // $values is the set of results so far, passed by reference so you can append more.
    // $q is the Zend query the user typed.
    //
    // Override me! Add more items to the $values array here (note that it was passed by reference).
    // Example: $values[] = array('title' => 'Hi there', 'summary' => 'I like my blog', 
    // 'link' => 'http://thissite/wherever', 'class' => 'blog_post', 'score' => 0.8)
    //
    // 'class' is used to set a CSS class (see searchSuccess.php) to distinguish result types.
    //
    // Best when used with results from a pkZendSearch::searchLuceneWithValues call.
    //
    // IF YOU CHANGE THE ARRAY you must return true, otherwise it will not be sorted by score.
    return false;
  }
  
  static public function compareScores($i1, $i2)
  {
    // You can't just use - when comparing non-integers. Oops.
    if ($i2->score < $i1->score)
    {
      return -1;
    } 
    elseif ($i2->score > $i1->score)
    {
      return 1;
    }
    else
    {
      return 0;
    }
  }
  
  public function executeReorganize(sfRequest $request)
  {
    
    // Reorganizing the tree = escaping your page-specific security limitations.
    // So only full CMS admins can do it.
    $this->flunkUnless($this->getUser()->hasCredential('cms_admin'));
    
    $root = pkContextCMSPageTable::retrieveBySlug('/');
    $this->forward404Unless($root);
    
    $this->treeData = $root->getTreeJSONReady(false);
    // setTitle takes care of escaping things
    $this->getResponse()->setTitle(pkContextCMSTools::getOptionI18n('title_prefix') . 'Reorganize');
  }

  public function executeTreeMove($request)
  {
    $page = $this->retrievePageForEditingByIdParameter('id', 'manage');
    $refPage = $this->retrievePageForEditingByIdParameter('refId', 'manage');
    $type = $request->getParameter('type');
    if ($refPage->slug === '/')
    {
      // Root must not have peers
      if ($type !== 'inside')
      {
        $this->forward404();
      }
    }
    $this->logMessage("TREEMOVE page slug: " . $page->slug . " ref page slug: " . $refPage->slug . " type: " . $type, "info");
    // Refuse to move a page relative to one of its own descendants.
    // Doctrine's NestedSet implementation produces an
    // inconsistent tree in the 'inside' case and we're not too sure about
    // the peer cases either. The javascript tree component we are using does not allow it
    // anyway, but it can be fooled if you have two reorg tabs open
    // or another user is using it at the same time etc. -Tom and Dan
    // http://www.doctrine-project.org/jira/browse/DC-384
    $ancestorsInfo = $refPage->getAncestorsInfo();
    foreach ($ancestorsInfo as $info)
    {
      if ($info['id'] === $page->id)
      {
        $this->logMessage("TREEMOVE balked because page is an ancestor of ref page", "info");
        $this->forward404();
      }
    }
    if ($type === 'after')
    {
      $page->getNode()->moveAsNextSiblingOf($refPage);
    }
    elseif ($type === 'before')
    {
      $page->getNode()->moveAsPrevSiblingOf($refPage);
    }
    elseif ($type === 'inside')
    {
      $page->getNode()->moveAsLastChildOf($refPage);
    }
    else
    {
      $this->forward404();
    }
    echo("ok");
    return sfView::NONE;
  }
  
  public function executeMoveUp($request)
  {
    $page = $this->retrievePageForEditingByIdParameter('id', 'manage');
    $this->forward404Unless($page->userHasPrivilege('move-up'));
    $parent = $page->getParent();
    $this->forward404Unless($parent);
    $grandparent = $parent->getParent();
    $this->forward404Unless($grandparent);
    $page->getNode()->moveAsLastChildOf($grandparent);
    return $this->redirect($page->getUrl());
  }

  public function executeMoveDown($request)
  {
    $page = $this->retrievePageForEditingByIdParameter('id', 'manage');
    $peer = $this->retrievePageForEditingByIdParameter('peer', 'manage');
    $this->forward404Unless($page->userHasPrivilege('move-down'));
    // Make sure they have the same parent, no monkey business to escape privs checks
    if ($page->getParent()->id !== $peer->getParent()->id)
    {
      $this->forward404();
    }
    $page->getNode()->moveAsLastChildOf($peer);
    return $this->redirect($page->getUrl());
  }
  
  protected function getParentClasses($parents)
  {
    $result = '';
    foreach ($parents as $p)
    {
      $result .= " descendantof-$p";
    }
    if (count($parents))
    {
      $lastParent = pkArray::last($parents);
      $result .= " childof-$lastParent";
    }
    if (count($parents) < 2)
    {
      $result .= " toplevel";
    }
    return $result;
  }
  
  protected function generateAfterPageInfo($lastPage, $parents, $minusLevels)
  {
    $pageInfo = array();
    $pageInfo['id'] = 'after-' . $lastPage->getId();
    $pageInfo['title'] = 'after';
    $pageInfo['level'] = $lastPage->getLevel() - $minusLevels;
    $pageInfo['class'] = 'pagetree-after ' . $this->getParentClasses($parents);
    return $pageInfo;
  }
  
  protected function flunkUnless($condition)
  {
    if ($condition)
    {
      return;
    }
    $this->logMessage("ZZ flunked", "info");
    $this->forward('pkContextCMS', 'cleanSignin');
  }
  
  // Do NOT use these as the default signin actions. They are special-purpose
  // ajax/iframe breakers for use in forcing the user back to the login page
  // when they try to do an ajax action after timing out.
  
  public function executeCleanSignin(sfRequest $request)
  {
    // Template is a frame/ajax breaker, redirects to phase 2
  }
  
  public function executeCleanSigninPhase2(sfRequest $request)
  {
    $this->getRequest()->isXmlHttpRequest();
    $cookies = array_keys($_COOKIE);
    foreach ($cookies as $cookie)
    {
      // Leave the sfGuardPlugin remember me cookie alone
      if ($cookie === sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'sfRemember'))
      {
        continue;
      }
      // ACHTUNG: only works if we specify the domain ('/' in most cases).
      // This lives in factory.yml... where we can't access it. So unfortunately
      // a redundant setting is needed
      setcookie($cookie, "", time() - 3600, sfConfig::get('app_pkToolkit_cleanLogin_cookie_domain', '/'));
    }
    // Push the user back to the home page rather than the login prompt. Otherwise
    // we can find ourselves in an infinite loop if the login prompt helpfully
    // sends them back to an action they are not allowed to carry out
    $url = sfContext::getInstance()->getController()->genUrl('@homepage');
    header("Location: $url");
    exit(0);
  }
  
  public function executePersonalSettings(sfRequest $request)
  {
    $this->forward404Unless(sfConfig::get('app_pkContextCMS_personal_settings_enabled', false));
    $this->logMessage("ZZ hello", "info");
    $this->forward404Unless($this->getUser()->isAuthenticated());
    $this->logMessage("ZZ after auth", "info");
    $profile = $this->getUser()->getProfile();
    $this->logMessage("ZZ after fetch profile", "info");
    $this->forward404Unless($profile);
    $this->logMessage("ZZ after profile", "info");
    $this->form = new pkContextCMSPersonalSettingsForm($profile);
    if ($request->getParameter('submit'))
    {
      $this->form->bind($request->getParameter('settings'));
      if ($this->form->isValid())
      {
        $this->form->save();
        return 'Redirect';
      }
    }
  }
}

