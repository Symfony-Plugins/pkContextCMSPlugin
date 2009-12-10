<?php

class pkContextCMSRawHTMLForm extends sfForm
{
  protected $id;
  public function __construct($id)
  {
    $this->id = $id;
    parent::__construct();
  }
  public function configure()
  {
    $this->setWidgets(array('value' => new sfWidgetFormTextarea(array(), array('class' => 'pkContextCMSRawHTMLSlotTextarea'))));
    // Raw HTML slot, so anything goes, including an empty response 
    $this->setValidators(array('value' => new sfValidatorString(array('required' => false))));
    $this->widgetSchema->setNameFormat('slotform-' . $this->id . '[%s]');
  }
}