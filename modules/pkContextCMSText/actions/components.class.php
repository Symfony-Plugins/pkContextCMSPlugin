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
  public function executeNormalView()
  {
    $this->setup();
    // We don't recommend doing this at the FCK level,
    // let it happen here instead so what is stored in the
    // db can be clean markup
    $this->value = pkHtml::obfuscateMailto($this->value);
  }
}
