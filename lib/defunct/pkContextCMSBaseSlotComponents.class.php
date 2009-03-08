<?php

abstract class pkContextBaseSlotComponents extends sfComponents
{
  protected $page;
  public function executeInsert()
  {
    $this->setup();
    // Template inserts the edit and show sub-components
  }
  protected function setup()
  {
    $this->page = pkContextCMSTools::getCurrentPage();
    $this->slot = $this->page->getSlot($this->name);
  }
  abstract public function executeEdit();
  abstract public function executeShow();
}
