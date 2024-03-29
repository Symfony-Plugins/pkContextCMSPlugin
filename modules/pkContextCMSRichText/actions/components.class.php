<?php

class pkContextCMSRichTextComponents extends pkContextCMSBaseComponents
{
  public function executeEditView()
  {
    $this->setup();
    // Careful, don't clobber a form object provided to us with validation errors
    // from an earlier pass
    if (!isset($this->form))
    {
      $this->form = new pkContextCMSRichTextForm($this->id, $this->options);
      $this->form->setDefault('value', $this->slot->value);
    }
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
