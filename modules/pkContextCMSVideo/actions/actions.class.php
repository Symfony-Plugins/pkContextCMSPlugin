<?php

class pkContextCMSVideoActions extends pkContextCMSBaseActions
{
  public function executeEdit(sfRequest $request)
  {
    $this->logMessage("====== in pkContextCMSVideoActions::executeEdit", "info");
    $this->editSetup();
    $item = pkMediaAPI::getSelectedItem($request, "app_pkContextCMS_media");
    if ($item === false)
    {
      // Cancellation or error
      return $this->redirect($this->page->getUrl());
    } 
    if ($item->type !== 'video')
    {
      // Attempt to stuff in inappropriate media type
      return $this->redirect($this->page->getUrl());
    }
    $this->slot->value = serialize($item);
    $this->editSave();
  }
}
