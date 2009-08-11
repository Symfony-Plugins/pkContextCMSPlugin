<?php

class BasepkContextCMSPersonalSettingsForm extends sfGuardUserProfileForm
{
  public function setup()
  {
    parent::setup();
    // Allowing a user to associate their profile with another
    // user's id does not make sense
    unset($this['user_id']);
    $this->widgetSchema->setNameFormat('settings[%s]');
    $this->widgetSchema->setFormFormatterName('list');
  }
}

?>