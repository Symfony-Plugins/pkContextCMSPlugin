<?php

class pkContextCMSRichTextActions extends pkContextCMSBaseActions
{
  public function executeEdit(sfRequest $request)
  {
    $this->editSetup();
    $rawValue = $this->getRequestParameter('value-' . $this->id);

    // Enforce sane, simple HTML. Our default tag list might not be to 
    // everyone's taste. We reserve h1 and h2 for use at the layout and 
    // template level. You can change it with an option.

    // We don't allow img because we use media slots and pkMediaPlugin.
    // Your preferences may vary.

    // I don't really like accepting div all that much but it needs to at least
    // be permitted as a paragraph equivalent.

    // We do more than call strip_tags here. Specifically, we
    //remove all attributes except href and name on an "a" element. 
    // We also remove any javascript: href. If you enable img, then "src" 
    // will be permitted on that (but again, no javascript: src).
    // See pkToolkitPlugin and its pkHtml class.

    // We allow tables. Your preference may vary. You can pass a
    // different set of allowed_tags as an option to the slot or area.

    if ($this->getOption('simplify', true))
    {
      $this->logMessage('Edited rich text slot on page ' . $this->page->slug, 'info');
      $this->slot->value = pkHtml::simplify(
        $rawValue,
        $this->getOption('allowed_tags', "<h1><h2><h3><h4><h5><h6><blockquote><p><a><ul><ol><nl><li><b><i><strong><em><strike><code><hr><br><div><table><thead><caption><tbody><tr><th><td><pre>"));
    }
    return $this->editSave();
  }
}
