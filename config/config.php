<?php

if (sfConfig::get('app_pk_context_cms_plugin_routes_register', true) && in_array('pkContextCMS', sfConfig::get('sf_enabled_modules', array())))
{
  $this->dispatcher->connect('routing.load_configuration', array('pkContextCMSRouting', 'listenToRoutingLoadConfigurationEvent'));
}


