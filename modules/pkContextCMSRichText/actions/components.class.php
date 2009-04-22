<?php

class pkContextCMSRichTextComponents extends pkContextCMSBaseComponents
{
  public function executeEditView()
  {
    $this->setup();
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
