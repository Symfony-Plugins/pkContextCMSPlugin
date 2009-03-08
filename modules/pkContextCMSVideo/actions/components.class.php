<?php

class pkContextCMSVideoComponents extends pkContextCMSBaseComponents
{
  public function executeEditView()
  {
    // Just a stub, we don't really utilize this for this slot type,
    // we have an external editor instead
    $this->setup();
  }
  public function executeNormalView()
  {
    $this->setup();
    $this->width = $this->getOption('width', 320);
    $this->height = $this->getOption('height', 240);
    $this->resizeType = $this->getOption('resizeType', 's');
    $this->flexHeight = $this->getOption('flexHeight');
    // Behave well if it's not set yet!
    if (strlen($this->slot->value))
    {
      $this->item = unserialize($this->slot->value);
      $this->itemId = $this->item->id;
    }
    else
    {
      $this->item = false;
      $this->itemId = false;
    }
  }
}
