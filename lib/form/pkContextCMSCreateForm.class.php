<?php

class pkContextCMSCreateForm extends sfForm
{
  protected $page;
  public function __construct($page)
  {
    $this->page = $page;
    parent::__construct();
  }
  
  public function configure()
  {
    $this->setWidget('parent', new sfWidgetFormInputHidden(array('default' => $this->page->getSlug())));
    // It's not sfFormWidgetInput anymore in 1.4
    $this->setWidget('title', new sfWidgetFormInputText(array(), array('class' => 'pk-breadcrumb-create-childpage-title pk-breadcrumb-input')));
  }
}

