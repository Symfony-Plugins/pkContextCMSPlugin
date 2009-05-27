<?php

if (sfConfig::get('app_pk_context_cms_plugin_routes_register', true) && in_array('pkContextCMS', sfConfig::get('sf_enabled_modules', array())))
{
  $this->dispatcher->connect('routing.load_configuration', array('pkContextCMSRouting', 'listenToRoutingLoadConfigurationEvent'));
}

// You too can do this in a plugin dependent on pkContextCMS, see the provided stylesheet 
// for how to correctly specify an icon to go with your button. Note that if you are using
// a pluginnamePluginConfiguration class you'll need to call from there rather than
// from config.php, which won't be loaded if you have such a class.
pkContextCMSTools::addGlobalButton('Settings', 'pkContextCMS/globalSettings', 'pk-settings');
pkContextCMSTools::addGlobalButton('Users', 'sfGuardUser/index', 'pk-users');
