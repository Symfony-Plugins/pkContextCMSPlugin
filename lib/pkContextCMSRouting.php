<?php

class pkContextCMSRouting
{
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();
    if (sfConfig::get('app_pkContextCMS_routes_register', true) && in_array('pkContextCMS', sfConfig::get('sf_enabled_modules')))
    {
      // TBB: this is a big change to simplify the routing of actions
      // that are virtually all AJAXed anyway. If you don't go this route,
      // you'll need routes for the individual actions in the CMS
      $r->prependRoute('pk_context_cms_action',
        new sfRoute('/cms/:module/:action',
          array('module' => '\w+', 'action' => '\w+')));
      $r->appendRoute('pk_context_cms_show', 
        new sfRoute('/:slug', 
          array('module' => 'pkContextCMS', 'action' => 'show'),
          array('slug' => '.*')));
    }
  }
}
