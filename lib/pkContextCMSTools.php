<?php

class pkContextCMSTools
{
  static public function cultureOrDefault($culture = false)
  {
    if ($culture)
    {
      return $culture;
    }
    return self::getUserCulture();
  }
  static public function getUserCulture($user = false)
  {
    if ($user == false)
    {
      $culture = false;
      try
      {
        $context = sfContext::getInstance();
      } catch (Exception $e)
      {
        // Not present in tasks
        $context = false;
      }
      if ($context)
      {
        $user = sfContext::getInstance()->getUser();
      }
    }
    if ($user)
    {
      $culture = $user->getCulture();
    }
    if (!$culture)
    {
      $culture = sfConfig::get('sf_default_culture', 'en');
    }
    return $culture;
  }
  static public function urlForPage($slug, $absolute = true)
  {
    // sfSimpleCMS found a nice workaround for this
    // By using @pk_context_cms_page we can skip to a shorter URL form
    // and not get tripped up by the default routing rule which could
    // match first if we wrote pkContextCMS/show 
    $routed_url = sfContext::getInstance()->getController()->genUrl('@pk_context_cms_page?slug=-PLACEHOLDER-', $absolute);
    $routed_url = str_replace('-PLACEHOLDER-', $slug, $routed_url);
    // We tend to get double slashes because slugs begin with slashes
    // and the routing engine wants to helpfully add one too. Fix that,
    // but don't break http://
    $matches = array();
    // This is good both for dev controllers and for absolute URLs
    $routed_url = preg_replace('/([^:])\/\//', '$1/', $routed_url);
    // For non-absolute URLs without a controller
    if (!$absolute) 
    {
      $routed_url = preg_replace('/^\/\//', '/', $routed_url);
    }
    return $routed_url;
  }
  // We need a separate flag so that even a non-CMS page can
  // restore its state (i.e. set the page back to null)
  static private $global = false;
  static private $globalCache = false;
  static private $currentPage = null;
  static private $savedCurrentPage = null;
  static public function setCurrentPage($page)
  {
    self::$currentPage = $page;
  }
  static public function getCurrentPage()
  {
    return self::$currentPage;
  }

  static public function globalSetup($options)
  {
    if (isset($options['global']) && $options['global'])
    {
      self::$savedCurrentPage = self::getCurrentPage();
      // Caching the global page speeds up pages with two or more global slots
      if (self::$globalCache !== false)
      {
        $global = self::$globalCache;
      }
      else
      {        
        $global = pkContextCMSPageTable::retrieveBySlugWithSlots('global');
        if (!$global)
        {
          $global = new pkContextCMSPage();
          $global->slug = 'global';
          $global->save();
        }
        self::$globalCache = $global;
      }
      self::setCurrentPage($global);
      self::$global = true;
    }
  }

  static public function globalShutdown()
  {
    if (self::$global)
    {
      self::setCurrentPage(self::$savedCurrentPage);
      // Set to null not false
      self::$savedCurrentPage = null;
      self::$global = false;
    }
  }

  static public function getSlotOptionsGroup($groupName)
  {
    $optionGroups = sfConfig::get('app_pkContextCMS_slot_option_groups', 
      array());
    if (isset($optionGroups[$groupName]))
    {
      return $optionGroups[$groupName];
    }
    throw new sfException("Option group $groupName is not defined in app.yml");
  }
  static public function getSlotTypeOptions($options)
  {
    $slotTypes = array_merge(
      array(
        'pkContextCMSText' => 'Plain Text',
        'pkContextCMSRichText' => 'Rich Text'),
      sfConfig::get('app_pkContextCMS_slot_types', array()));
    if (isset($options['allowed_types']))
    {
      $newSlotTypes = array();
      foreach($options['allowed_types'] as $type)
      {
        if (isset($slotTypes[$type]))
        {
          $newSlotTypes[$type] = $slotTypes[$type];
        }
      }
      $slotTypes = $newSlotTypes;
    }
    return $slotTypes; 
  }
  static public function getOption($array, $name, $default)
  {
    if (isset($array[$name]))
    {
      return $array[$name];
    }
    return $default;
  }
  static public function getRealPage()
  {
    if (self::$savedCurrentPage)
    {
      return self::$savedCurrentPage;
    }
    elseif (self::$currentPage)
    {
      return self::$currentPage;
    }
    else
    {
      return false;
    }
  }
  // Fetch options array saved in session
  static public function getAreaOptions($pageid, $name)
  {
    $lookingFor = "area-options-$pageid-$name";
    $options = array();
    $user = sfContext::getInstance()->getUser();
    if ($user->hasAttribute($lookingFor, 'pkContextCMS'))
    {
      $options = $user->getAttribute(
        $lookingFor, false, "pkContextCMS");
    }
    return $options;
  }
  
  static public function getTemplates()
  {
    if (sfConfig::get('app_pkContextCMS_get_templates_method'))
    {
      $method = sfConfig::get('app_pkContextCMS_get_templates_method');

      return call_user_func($method);
    }
    return sfConfig::get('app_pkContextCMS_templates', array(
      'default' => 'Default Page',
      'home' => 'Home Page'));
  }
  
  static public function getEngines()
  {
    if (sfConfig::get('app_pkContextCMS_get_engines_method'))
    {
      $method = sfConfig::get('app_pkContextCMS_get_engines_method');

      return call_user_func($method);
    }
    return sfConfig::get('app_pkContextCMS_engines', array(
      '' => 'Template-Based'));
  }
  
  // Fetch an internationalized option from app.yml. Example:
  // all:
  //   pkContextCMS:
  
  static public function getOptionI18n($option, $default = false, $culture = false)
  {
    $culture = self::cultureOrDefault($culture);
    $values = sfConfig::get("app_pkContextCMS_$option", array());
    if (!is_array($values))
    {
      // Convenience for single-language sites
      return $values;
    }
    if (isset($values[$culture]))
    {
      return $values[$culture];  
    } 
    return $default; 
  }
  
  static public function getGlobalButtonsInternal(sfEvent $event)
  {
    // If we needed a context object we could get it from $event->getSubject(),
    // but this is a simple static thing
    
    // Add the users button only if the user has the admin credential.
    // This is typically only given to admins and superadmins.
    // TODO: there is also the cms_admin credential, should I differentiate here?
    $user = sfContext::getInstance()->getUser();
    if ($user->hasCredential('admin'))
    {
      $extraAdminButtons = sfConfig::get('app_pkContextCMS_extra_admin_buttons', 
        array(
          array('label' => 'Users', 'action' => 'pkUserAdmin/index', 'class' => 'pk-users'),
          array('label' => 'Reorganize', 'action' => 'pkContextCMS/reorganize', 'class' => 'pk-reorganize')        
        ));
      // Eventually this one too. Reorganize will probably get moved into it
      // ('Settings', 'pkContextCMS/globalSettings', 'pk-settings')

      if (is_array($extraAdminButtons))
      {
        foreach ($extraAdminButtons as $data)
        {
          pkContextCMSTools::addGlobalButtons(array(new pkContextCMSGlobalButton(
            $data['label'], $data['action'], isset($data['class']) ? $data['class'] : '')));
        }
      }
    }
  }
  
  static protected $globalButtons = false;

  // To be called only in response to a pkContextCMS.getGlobalButtons event 
  static public function addGlobalButtons($array)
  {
    self::$globalButtons = array_merge(self::$globalButtons, $array);
  }
  
  static public function getGlobalButtons()
  {
    if (self::$globalButtons !== false)
    {
      return self::$globalButtons;
    }
    $buttonsOrder = sfConfig::get('app_pkContextCMS_global_button_order', false);
    self::$globalButtons = array();
    // We could pass parameters here but it's a simple static thing in this case 
    // so the recipients just call back to addGlobalButtons
    sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent(null, 'pkContextCMS.getGlobalButtons', array()));
    
    $buttonsByLabel = array();
    foreach (self::$globalButtons as $button)
    {
      $buttonsByLabel[$button->getLabel()] = $button;
    }
    if ($buttonsOrder === false)
    {
      ksort($buttonsByLabel);
      $orderedButtons = array_values($buttonsByLabel);
    }
    else
    {
      $orderedButtons = array();
      foreach ($buttonsOrder as $label)
      {
        if (isset($buttonsByLabel[$label]))
        {
          $orderedButtons[] = $buttonsByLabel[$label];
        }
      }
    }
    
    self::$globalButtons = $orderedButtons;
    return $orderedButtons;
  }
  
  static public function globalToolsPrivilege()
  {
    // if you can edit the page, there are tools for you in the apostrophe
    if (self::getCurrentPage() && self::getCurrentPage()->userHasPrivilege('edit'))
    {
      return true;
    }
    // if you are the site admin, there are ALWAYS tools for you in the apostrophe
    $user = sfContext::getInstance()->getUser();
    return $user->hasCredential('cms_admin');
  }
  
  // These methods allow slot editing to be turned off even for people with
  // full and appropriate privileges.
  
  // Most of the time being able to edit a global slot on a non-CMS page is a
  // good thing, especially if that's the only place the global slot appears.
  // But sometimes, as in the case where you're editing other types of data,
  // it's just a source of confusion to have those buttons displayed. 
  
  // (Suppressing editing of slots on normal CMS pages is of course a bad idea,
  // because how else would you ever edit them?)
  
  static private $allowSlotEditing = true;
  static public function setAllowSlotEditing($value)
  {
    self::$allowSlotEditing = $value;
  }
  static public function getAllowSlotEditing()
  {
    return self::$allowSlotEditing;
  }
  
  // Kick the user out to appropriate places if they don't have the proper 
  // privileges to be here. pkContextCMS::executeShow and pkContextCMSEngineActions::preExecute
  // both use this 
  
  static public function validatePageAccess(sfAction $action, $page)
  {
    $action->forward404Unless($page);
    if (!$page->userHasPrivilege('view'))
    {
      // forward rather than login because referrers don't always
      // work. Hopefully the login action will capture the original
      // URI to bring the user back here afterwards.

      if ($action->getUser()->isAuthenticated())
      {
        return $action->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
      }
      else
      {
        return $action->forward(sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action'));

      }
    }
    if ($page->archived && (!$page->userHasPrivilege('edit|manage')))
    {
      $action->forward404();
    }    
  }

  // Establish the page title, set the layout, and add the javascripts that are
  // necessary to manage pages. pkContextCMS::executeShow and pkContextCMSEngineActions::preExecute
  // both use this. TODO: is this redundant now that pkContextCMSHelper does it?
  
  static public function setPageEnvironment(sfAction $action, pkContextCMSPage $page)
  {
    // Title is pre-escaped as valid HTML
    $prefix = pkContextCMSTools::getOptionI18n('title_prefix');
    $action->getResponse()->setTitle($prefix . $page->getTitle(), false);
    // Necessary to allow the use of
    // pkContextCMSTools::getCurrentPage() in the layout.
    // In Symfony 1.1+, you can't see $action->page from
    // the layout.
    pkContextCMSTools::setCurrentPage($page);
    // Borrowed from sfSimpleCMS
    if(sfConfig::get('app_pkContextCMS_use_bundled_layout', true))
    {
      $action->setLayout(sfContext::getInstance()->getConfiguration()->getTemplateDir('pkContextCMS', 'layout.php').'/layout');
    }

    // Loading the pkContextCMS helper at this point guarantees not only
    // helper functions but also necessary JavaScript and CSS
    sfContext::getInstance()->getConfiguration()->loadHelpers('pkContextCMS');     
  }
  
  static public function pageIsDescendantOfInfo($page, $info)
  {
    return ($page->lft > $info['lft']) && ($page->rgt < $info['rgt']);
  }
}
