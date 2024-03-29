<?php

class pkContextCMSTextForm extends sfForm
{
  protected $id;
  protected $value;
  protected $soptions;
  public function __construct($id, $value, $soptions)
  {
    $this->id = $id;
    $this->value = $value;
    $this->soptions = $soptions;
    parent::__construct();
  }
  public function configure()
  {
    $class = isset($this->soptions['class']) ? ($this->soptions['class'] . ' ') : '';
    $class .= 'pkContextCMSTextSlot';
    if (isset($this->soptions['multiline']) && $this->soptions['multiline'])
    {
      unset($this->soptions['multiline']);
      $class .= ' multi-line';
    }
    else
    {
      $class .= ' single-line';
    }
    $this->soptions['class'] = $class;
    $this->setWidgets(array('value' => new sfWidgetFormTextarea(array('default' => html_entity_decode(strip_tags($this->value))), $this->soptions)));
    $this->setValidators(array('value' => new sfValidatorString(array('required' => false))));
    $this->widgetSchema->setNameFormat('slotform-' . $this->id . '[%s]');    
  }
}