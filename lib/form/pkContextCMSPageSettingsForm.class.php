<?php

// TODO: move the post-validation cleanup of the slug into the
// validator so that we don't get a user-unfriendly error or
// failure when /Slug Foo fails to be considered a duplicate
// of /slug_foo the first time around

class pkContextCMSPageSettingsForm extends pkContextCMSPageForm
{
  public function configure()
  {
    unset(
      $this['author_id'],
      $this['deleter_id'],
      $this['Accesses'],
      $this['created_at'],
      $this['updated_at'],
      $this['view_credentials'],
      $this['edit_credentials'],
      $this['lft'],
      $this['rgt'],
      $this['level']
    );

    $this->setWidget('template', new sfWidgetFormSelect(array('choices' => pkContextCMSTools::getTemplates())));
     
    $this->setWidget('engine', new sfWidgetFormSelect(array('choices' => pkContextCMSTools::getEngines())));

    // On vs. off makes more sense to end users, but when we first
    // designed this feature we had an 'archived vs. unarchived'
    // approach in mind
    $this->setWidget('archived', new sfWidgetFormChoice(array(
      'expanded' => true,
      'choices' => array(false => "Published", true => "Unpublished"),
      'default' => false
    )));

    if ($this->getObject()->hasChildren())
    {
      unset($this['archived']);
    }

    $this->setWidget('view_is_secure', new sfWidgetFormChoice(array(
      'expanded' => true,
      'choices' => array(
        false => "Public",
        true => "Login Required"
      ),
      'default' => false
    )));

    $this->addPrivilegeWidget('edit', 'editors');
    $this->addPrivilegeWidget('manage', 'managers');

    $this->setValidator('slug', new sfValidatorAnd(array(
      new sfValidatorRegex(array(
        'pattern' => '/^\/[\w\/\-\+]+$/',
        'required' => 'The slug cannot be empty.',
      ), array(
        'invalid' => 'The slug must contain only slashes, letters, digits, dashes, plus signs and underscores. Also, you cannot change a slug to conflict with the home page slug.'
      ))
    )));

    $this->setValidator('template', new sfValidatorChoice(array(
      'required' => true,
      'choices' => array_keys(pkContextCMSTools::getTemplates())
    )));

    // Making the empty string one of the choices doesn't seem to be good enough
    // unless we expressly clear 'required'
    $this->setValidator('engine', new sfValidatorChoice(array(
      'required' => false,
      'choices' => array_keys(pkContextCMSTools::getEngines())
    )));   

    // The slug of the home page cannot change (chicken and egg problems)
    if ($this->getObject()->getSlug() === '/')
    {
      unset($this['slug']);
    }
    else
    {
      $this->validatorSchema->setPostValidator(new sfValidatorDoctrineUnique(array(
        'model' => 'pkContextCMSPage',
        'column' => 'slug'
      ), array('invalid' => 'There is already a page with that slug.')));
    }
    
    $this->widgetSchema->setIdFormat('pk_context_cms_settings_%s');
    $this->widgetSchema->setNameFormat('settings[%s]');
    $this->widgetSchema->setFormFormatterName('list');

    $user = sfContext::getInstance()->getUser();
    if (!$user->hasCredential('cms_admin'))
    {
      unset($this['editors']);
      unset($this['managers']);
      unset($this['slug']);
    } 
  }
  
  protected function addPrivilegeWidget($privilege, $widgetName)
  {
    list($all, $selected, $inherited, $sufficient) = $this->getObject()->getAccessesById($privilege);

    foreach ($inherited as $userId)
    {
      unset($all[$userId]);
    }

    foreach ($sufficient as $userId)
    {
      unset($all[$userId]);
    }

    $this->setWidget($widgetName, new sfWidgetFormSelect(array(
      // + operator is correct: we don't want renumbering when
      // ids are numeric
      'choices' => 
        array("" => "Choose a User to Add") + $all,
      'multiple' => true,
      'default' => $selected
    )));

    $this->setValidator($widgetName, new sfValidatorChoice(array(
      'required' => false, 
      'multiple' => true,
      'choices' => array_keys($all)
    )));
  }

  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);
    
    // This part isn't validation, it's just normalization.
    $slug = $object->slug;
    $slug = trim($slug);
    $slug = preg_replace("/\/+/", "/", $slug);
    $slug = preg_match("/^(\/.*?)\/*$/", $slug, $matches);
    $object->slug = $matches[1];
    if (isset($object->engine) && (!strlen($object->engine)))
    {
      // Store it as null for plain ol' executeShow page templating
      $object->engine = null;
    }
    $this->savePrivileges($object, 'edit', 'editors');
    $this->savePrivileges($object, 'manage', 'managers');
    
    // Has to be done on shutdown so it comes after the in-memory cache of
    // sfFileCache copies itself back to disk, which otherwise overwrites
    // our attempt to invalidate the routing cache [groan]
    register_shutdown_function(array($this, 'invalidateRoutingCache'));
  }
  
  public function invalidateRoutingCache()
  {
    // Clear the routing cache on page settings changes. TODO:
    // finesse this to happen only when the engine is changed,
    // and then perhaps further to clear only cache entries
    // relating to this page
    $routing = sfContext::getInstance()->getRouting();
    if ($routing)
    {
      $cache = $routing->getCache();
      if ($cache)
      {
        sfContext::getInstance()->getLogger()->info("QZ got cache");
        $cache->clean();
        sfContext::getInstance()->getLogger()->info("QZ cleared cache");
      }
      else
      {
        sfContext::getInstance()->getLogger()->info("QZ no cache");
      }
    }
    else
    {
      sfContext::getInstance()->getLogger()->info("QZ no routing");
    }
  }
  
  protected function savePrivileges($object, $privilege, $widgetName)
  {
    if (isset($this[$widgetName]))
    {
      $editorIds = $this->getValue($widgetName);
      // Happens when the list is empty (sigh)
      if ($editorIds === null)
      {
        $editorIds = array();
      }
      
      $object->setAccessesById($privilege, $editorIds);
    }
  }
}
