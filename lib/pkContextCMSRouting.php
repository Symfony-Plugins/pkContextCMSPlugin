<?php

class pkContextCMSRouting
{
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();
    if (sfConfig::get('app_pkContextCMS_routes_register', true) && in_array('pkContextCMS', sfConfig::get('sf_enabled_modules')))
    {
      // Everything that doesn't specifically match an action (see next rule)
      $r->prependRoute('pk_context_cms_show', 
        new sfRoute('/:slug', 
          array('module' => 'pkContextCMS', 'action' => 'show'),
          array('slug' => '.*')));
      // TBB: this is a big change to simplify the routing of actions
      // that are mostly AJAXed and not the primary point of the site.
      // If you don't go this route, you'll need routes for the individual 
      // actions in the CMS as well as your own non-CMS modules
      $r->prependRoute('pk_context_cms_action',
        new sfRoute('/cms/:module/:action',
          array('module' => '\w+', 'action' => '\w+')));
    }
  }
}
