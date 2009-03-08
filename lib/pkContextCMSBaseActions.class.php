<?php

class pkContextCMSBaseActions extends sfActions
{
  protected $validationData = array();
  protected function editSetup()
  {
    return $this->setup(true);
  }
  protected function setup($editing = false)
  {
    $this->reopen = false;
    $this->slug = $this->getRequestParameter('slug');
    $this->name = $this->getRequestParameter('slot');
    $this->value = $this->getRequestParameter("value");
    $this->permid = $this->getRequestParameter("permid");
    $this->page = pkContextCMSPageTable::retrieveBySlugWithSlots($this->slug);
    $this->forward404Unless($this->page);
    $this->user = sfContext::getInstance()->getUser();
    $this->pageid = $this->page->getId();
    // Used to name value parameters, among other things
    $this->id = $this->name . "-" . $this->permid;
    if ($editing)
    {
      if (!$this->page->userHasPrivilege('edit'))
      {
        return $this->redirect(
          sfConfig::get('secure_module') . '/' .
          sfConfig::get('secure_action'));
      }
    } 
    else
    {
      if (!$this->page->userHasPrivilege('view'))
      {
        return $this->redirect(
          sfConfig::get('login_module') . '/' .
          sfConfig::get('login_action'));
      }
    }
    // This was stored when the slot's editing view was rendered. If it
    // isn't present we must refuse to play for security reasons.
    $user = $this->getUser();
    $pageid = $this->pageid;
    $name = $this->name;
    $permid = $this->permid;
    $lookingFor = "slot-options-$pageid-$name-$permid";
    if ($user->hasAttribute($lookingFor, 'pkContextCMS'))
    {
      $this->options = $user->getAttribute(
        $lookingFor, false, "pkContextCMS");
    }
    $this->forward404Unless($this->options !== false);
    // Clever no?
    $this->type = str_replace("Actions", "", get_class($this));
    $slot = $this->page->getSlot(
      $this->name, $this->permid);
    // Copy the slot- we'll be making a new version of it,
    // if we do decide to save that is. 
    if ($slot)
    {
      $this->slot = $slot->copy();
    }
    else
    {
      $this->slot = $this->page->createSlot($this->type);
    }
  }

  protected function editSave()
  {
    $this->slot->save();
    $this->page->newAreaVersion(
      $this->name, 
      'update', 
      array('permid' => $this->permid, 'slot' => $this->slot));
    if  ($this->getRequestParameter('noajax'))
    {
      return $this->redirect($this->page->getUrl());
    }
    else
    {
      return $this->editAjax(false);
    }
  }

  protected function editRetry()
  {
    if (isset($this->form))
    {
      $this->validationData['form'] = $this->form;
    }
    return $this->editAjax(true);
  }

  protected function editAjax($editorOpen)
  {
    // Refetch the page to reflect these changes before we
    // rerender the slot
    pkContextCMSTools::setCurrentPage(
      pkContextCMSPageTable::retrieveByIdWithSlots($this->page->id));

    // Symfony 1.2 can return partials rather than templates...
    // which gets us out of the "we need a template from some other
    // module" bind
    return $this->renderPartial("pkContextCMS/ajaxUpdateSlot",
      array("name" => $this->name, 
        "type" => $this->type, 
        "permid" => $this->permid, 
        "options" => $this->options,
        "editorOpen" => $editorOpen,
        "validationData" => $this->validationData));
  }

  public function executeEdit(sfRequest $request)
  {
    // When writing your own custom slot classes, you override this
    // to store information in different database fields, look at different
    // request fields, validate the value more critically etc. Call
    // $this->editSetup() to get $this->slot prepopulated for you with
    // a slot of the appropriate type. Always return the result of 
    // $this->editSave() when you are done! 
    $this->editSetup();
    $this->slot->value = $this->getRequestParameter('value-' . $this->id);
    return $this->editSave();
  }
  protected function getOption($option, $default = false)
  {
    if (isset($this->options[$option]))
    {
      return $this->options[$option];
    }
    else
    {
      return $default;
    }
  }
  protected function setValidationData($key, $val)
  {
    $this->validationData[$key] = $val;
  }
}
