<?php

if (sfConfig::get('app_pkContextCMS_plugin_routes_register', true) && in_array('pkContextCMS', sfConfig::get('sf_enabled_modules', array())))
{
  $this->dispatcher->connect('routing.load_configuration', array('pkContextCMSRouting', 'listenToRoutingLoadConfigurationEvent'));
}

// Allows us to reset static data such as the current CMS page.
// Necessary when writing functional tests that use the restart() method
// of the browser to start a new request - something that never happens in the
// lifetime of the same PHP invocation under normal circumstances
$this->dispatcher->connect('test.simulate_new_request', array('pkContextCMSTools', 'listenToSimulateNewRequestEvent'));

// Register an event so we can add our buttons to the set of global CMS back end admin buttons
// that appear when the apostrophe is clicked. We do it this way as a demonstration of how it
// can be done in other plugins that enhance the CMS
$this->dispatcher->connect('pkContextCMS.getGlobalButtons', array('pkContextCMSTools', 'getGlobalButtonsInternal'));