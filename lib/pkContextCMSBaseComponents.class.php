<?php

class pkContextCMSBaseComponents extends sfComponents
{
  protected function setup()
  {
    $this->page = pkContextCMSTools::getCurrentPage();
    $this->slug = $this->page->slug;
    $this->realSlug = pkContextCMSTools::getRealPage()->slug;
    $this->slot = $this->page->getSlot(
          $this->name, $this->permid);
    if ((!$this->slot) || ($this->slot->type !== $this->type))
    {
      $this->slot = $this->page->createSlot($this->type);
    }
    $this->editable = $this->page->userHasPrivilege('edit');
    if ($this->getOption('preview'))
    {
      $this->editable = false;
    }
    if ($this->editable)
    {
      $user = $this->getUser();
      $id = $this->page->getId();
      $name = $this->name;
      $permid = $this->permid;
      // Make sure the options passed to pk_context_cms_slot 
      // can be found again at save time
      $user->setAttribute("slot-options-$id-$name-$permid", 
        $this->options, "pkContextCMS");
    }
    $this->id = $this->name . "-" . $this->permid;
    // The basic slot types, and some custom slot types, are
    // simplified by having this field ready to go
    $this->value = $this->slot->value;
    // Not everyone wants the default 'double click the outline to
    // start editing' behavior 
    $this->outlineEditable =
      $this->editable && $this->getOption('outline_editable', 
        $this->slot->isOutlineEditable());
    // Useful if you're reimplementing that via a button etc
    $id = $this->id;
    $this->showEditorJS = 
      "$('#content-$id').hide(); $('#form-$id').show();";
    if (isset($this->validationData['form']))
    {
      // Make Symfony 1.2 form validation extra-convenient
      $this->form = $this->validationData['form'];
    }
  }
  
  public function executeSlot()
  {
    // Sadly components have no preExecute method
    $this->setup();
  }
  protected function getOption($name, $default = false)
  {
    if (isset($this->options[$name]))
    {
      return $this->options[$name];
    }
    else
    {
      return $default;
    }
  }
  protected function getValidationData($name, $default = false)
  {
    if (isset($this->validationData[$name]))
    {
      return $this->validationData[$name];
    }
    else
    {
      return $default;
    }
  }
}
