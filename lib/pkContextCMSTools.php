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
  static public function urlForPage($slug)
  {
    // sfSimpleCMS found a nice workaround for this
    // By using @pk_context_cms_page we can skip to a shorter URL form
    // and not get tripped up by the default routing rule which could
    // match first if we wrote pkContextCMS/show 
    $routed_url = sfContext::getInstance()->getController()->genUrl('@pk_context_cms_page?slug=-PLACEHOLDER-', true);
    $routed_url = str_replace('-PLACEHOLDER-', $slug, $routed_url);
    // We tend to get double slashes because slugs begin with slashes
    // and the routing engine wants to helpfully add one too. Fix that,
    // but don't break http://
    $routed_url = preg_replace('/([^:])\/\//', '$1/', $routed_url);
    return $routed_url;
  }
  // We need a separate flag so that even a non-CMS page can
  // restore its state (i.e. set the page back to null)
  static private $global = false;
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
      $global = pkContextCMSPageTable::retrieveBySlugWithSlots('global');
      if (!$global)
      {
        $global = new pkContextCMSPage();
        $global->slug = 'global';
        $global->save();
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

    pkContextCMSTools::addGlobalButtons(array(
      new pkContextCMSGlobalButton('Settings', 'pkContextCMS/globalSettings', 'pk-settings'),
      new pkContextCMSGlobalButton('Users', 'sfGuardUser/index', 'pk-users')));
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
}
