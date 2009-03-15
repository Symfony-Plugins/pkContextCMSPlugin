<?php

class pkContextCMSRouting
{
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();
    if (sfConfig::get('app_pkContextCMS_routes_register', true) && in_array('pkContextCMS', sfConfig::get('sf_enabled_modules')))
    {
      // 0.13: By default we'll use /cms for pages to avoid compatibility problems with
      // the default routing of other modules. But see the routing.yml of the cmstest
      // project for a better way to do this so your CMS pages (the point of your site!)
      // don't have to be locked down in a subfolder
      // 0.14: rename this rule pk_context_cms_page and require its use
      $r->prependRoute('pk_context_cms_page', 
        new sfRoute('/cms/:slug', 
          array('module' => 'pkContextCMS', 'action' => 'show'),
          array('slug' => '.*')));
    }
  }
}
