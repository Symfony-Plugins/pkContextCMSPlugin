<?php

class pkContextCMSRichTextForm extends sfForm
{
  protected $id;
  protected $soptions;
  public function __construct($id, $soptions = null)
  {
    $this->id = $id;
    $this->soptions = $soptions;
    $this->allowedTags = $this->getSlotOption('allowed-tags');
    $this->allowedAttributes = $this->getSlotOption('allowed-attributes');
    $this->allowedStyles = $this->getSlotOption('allowed-styles');
    parent::__construct();
  }
  protected function getSlotOption($s)
  {
    if (isset($this->soptions[$s]))
    {
      return $this->soptions[$s];
    }
    else
    {
      return null;
    }
  }
  public function configure()
  {
    $class = isset($this->soptions['class']) ? ($this->soptions['class'] . ' ') : '';
    $class .= 'pkContextCMSRawHTMLSlotTextarea';
    $this->soptions['class'] = $class;
    $this->setWidgets(array('value' => new sfWidgetFormRichTextarea(array(), array('class' => 'pkContextCMSRawHTMLSlotTextarea'))));
    $this->setValidators(array('value' => new sfValidatorHtml(array('required' => false, 'allowed_tags' => $this->allowedTags, 'allowed_attributes' => $this->allowedAttributes, 'allowed_styles' => $this->allowedStyles))));
    // There are problems with AJAX plus FCK plus Symfony forms. FCK insists on making the name and ID
    // the same and brackets are not valid in IDs. Work around this by not attempting to use brackets here
    $this->widgetSchema->setNameFormat('slotform-' . $this->id . '-%s');
  }
}