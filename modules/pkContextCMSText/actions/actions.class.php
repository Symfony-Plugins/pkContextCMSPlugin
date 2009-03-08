<?php

class pkContextCMSTextActions extends pkContextCMSBaseActions
{
  public function executeEdit(sfRequest $request)
  {
    $this->editSetup();
    $value = $this->getRequestParameter('value');
    if (!$this->getOption('multiline'))
    {
      $value = preg_replace("/\s/", " ", $value);
    }
    $value = htmlspecialchars($value);
    $value = preg_replace("(\r\n|\n|\r)", "<br>\n", $value);
    $maxlength = $this->getOption('maxlength');
    if ($maxlength !== false)
    {
      $value = substr(0, $maxlength);
    }
    $this->slot->value = $value;
    return $this->editSave();
  }
}
