<?php

class pkContextCMSSlideshowComponents extends pkContextCMSBaseComponents
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
      $this->items = unserialize($this->slot->value);
      $this->itemIds = array();
      foreach ($this->items as $item)
      {
        $this->itemIds[] = $item->id;
      }
    }
    else
    {
      $this->items = array();
      $this->itemIds = array();
    }
  }
}
