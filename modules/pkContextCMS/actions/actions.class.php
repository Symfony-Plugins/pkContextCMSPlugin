<?php

/**
 * pkContextCMS actions.
 *
 * @package    simplesite
 * @subpackage pkContextCMS
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class pkContextCMSActions extends sfActions
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
    if ($page->archived && (!$page->userHasPrivilege('edit')))
    {
      $this->forward404();
    }
    // Title is pre-escaped as valid HTML
    $this->getResponse()->setTitle($page->getTitle(), false);
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

  private function retrievePageForEditingById($parameter = 'id')
  {
    $page = pkContextCMSPageTable::retrieveByIdWithSlots(
      $this->getRequestParameter($parameter));
    $this->validAndEditable($page);
    return $page;
  }

  private function retrievePageForEditingBySlug($parameter = 'slug')
  {
    $page = pkContextCMSPageTable::retrieveBySlugWithSlots(
      $this->getRequestParameter($parameter));
    $this->validAndEditable($page);
    return $page;
  }

  private function validAndEditable($page)
  {
    $this->forward404Unless($page);
    $this->forward404Unless($page->userHasPrivilege('edit'));
  }

  public function executeSort(sfRequest $request)
  {
    $this->logMessage("Entering executeSort", "info");
    $page = $this->retrievePageForEditingById('page');
    if (!$page)
    {
      $this->logMessage("No page found in executeSort", "info");
    }
    if (!$page->getNode()->hasChildren())
    {
      $page = $page->getNode()->getParent();
      if (!$page)
      {
        $this->logMessage("No parent page found in executeSort", "info");
      }
      $this->forward404Unless($page);
    }
    $order = $this->getRequestParameter('pk-context-cms-navcolumn-page');
    $this->forward404Unless(is_array($order));
    $this->sortBody($page, $order);
    return sfView::NONE;
  }

  private function sortBody($parent, $order)
  {
    foreach ($order as $id)
    {
      $child = Doctrine::getTable('pkContextCMSPage')->find($id);
      if ($child) 
      {
        $child->getNode()->moveAsLastChildOf($parent);
      }
    }
  }

  public function executeRename(sfRequest $request)
  {
    $page = $this->retrievePageForEditingById();
    $this->forward404Unless($page);
    $this->forward404Unless($page->userHasPrivilege('edit'));    
    // Rename never changes the slug, just the title
    $page->setTitle($request->getParameter('title'));
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
    $parent = $this->retrievePageForEditingBySlug('parent');
    
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
      $page->setTitle($title);
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
      $this->logMessage("found preview", "info");
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
    $this->forward404Unless($page->userHasPrivilege('edit'));    
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
    $this->logMessage("XXXX REQUEST " . json_encode($_REQUEST), "info");
    if ($from === 'settings[id]')
    {
      $this->form->bind($request->getParameter("settings"));
      if ($this->form->isValid())
      {
        $this->form->save();
        return 'redirect';
        $this->logMessage("XXXX VALID", "info");
      }
      else
      {
        $this->logMessage("XXXX INVALID", "info");
      }
    }
    // This might make more sense in some kind of read-only form control.
    // TODO: cache the first call that the form makes so this doesn't
    // cause more db traffic.
    list($all, $selected, $inherited, $sufficient) = $this->page->getAccessesById("edit");
    $this->inheritedEditors = array();
    foreach ($inherited as $userId)
    {
      $this->inheritedEditors[] = $all[$userId];
    }
    $this->adminEditors = array();
    foreach ($sufficient as $userId)
    {
      $this->adminEditors[] = $all[$userId];
    }
  }

  public function executeDelete()
  {
    $page = $this->retrievePageForEditingById();
    $this->forward404Unless($page->userHasPrivilege('delete'));
    $parent = $page->getParent();
    // tom@punkave.com: we must delete via the nested set
    // node or we'll corrupt the tree. Nasty detail, that.
    // Note that this implicitly calls $page->delete()
    // (but the reverse was not true and led to problems).
    $page->getNode()->delete(); 
    return $this->redirect($parent->getUrl());
  }
}
