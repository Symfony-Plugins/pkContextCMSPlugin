<?php

class pkContextCMSTextComponents extends pkContextCMSBaseComponents
{
  public function executeEditView()
  {
    $this->setup();
    $this->multiline = $this->getOption('multiline');
    // The rest of the options array is passed as HTML
    // options to the helper function, but this
    // should not be
    unset($this->options['multiline']);
  }
}
