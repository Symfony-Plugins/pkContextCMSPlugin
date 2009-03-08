<?php

class pkContextCMSSlideshowActions extends pkContextCMSBaseActions
{
  public function executeEdit(sfRequest $request)
  {
    $this->logMessage("====== in pkContextCMSSlideshowActions::executeEdit", "info");
    $this->editSetup();
    $items = pkMediaAPI::getSelectedItems($request, "app_pkContextCMS_media");
    if ($items === false)
    {
      // Cancellation or error
      return $this->redirect($this->page->getUrl());
    } 
    $nitems = array();
    foreach ($items as $item)
    {
      if ($item->type !== 'image')
      {
        // An attempt to stuff inappropriate media in
      }
      else
      {
        $nitems[] = $item;
      }
    }
    $this->slot->value = serialize($nitems);
    $this->editSave();
  }
}
