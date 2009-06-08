<?php

class pkContextCMSPluginPkContextCMSExtension
{
  // You too can do this in a plugin dependent on pkContextCMS, see the provided stylesheet 
  // for how to correctly specify an icon to go with your button. Note that if you are using
  // a pluginnamePluginConfiguration class you'll need to call from there rather than
  // from config.php, which won't be loaded if you have such a class.
  static public function getGlobalButtons()
  {
    return array(
      new pkContextCMSGlobalButton('Settings', 'pkContextCMS/globalSettings', 'pk-settings'),
      new pkContextCMSGlobalButton('Users', 'sfGuardUser/index', 'pk-users'));
  }
}
