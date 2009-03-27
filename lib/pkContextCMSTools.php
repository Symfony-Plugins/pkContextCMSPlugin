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
    }
  }

  static public function globalShutdown()
  {
    if (self::$savedCurrentPage)
    {
      self::setCurrentPage(self::$savedCurrentPage);
      self::$savedCurrentPage = false;
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
    return sfConfig::get('app_pkContextCMS_templates', array(
      'default' => 'Default Page',
      'home' => 'Home Page'));
  }
}
