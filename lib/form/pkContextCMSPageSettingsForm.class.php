<?php

// TODO: move the post-validation cleanup of the slug into the
// validator so that we don't get a user-unfriendly error or
// failure when /Slug Foo fails to be considered a duplicate
// of /slug_foo the first time around

class pkContextCMSPageSettingsForm extends pkContextCMSPageForm
{
  public function configure()
  {
    unset($this['author_id'], $this['deleter_id'], $this['Accesses'],
      $this['created_at'], $this['updated_at'], $this['view_credentials'],
      $this['edit_credentials'], $this['lft'], $this['rgt'], $this['level']);

    $this->setWidget(
      'template', 
      new sfWidgetFormSelect(
        array('choices' => pkContextCMSTools::getTemplates())));
    list($all, $selected, $inherited, $sufficient) = 
      $this->getObject()->getAccessesById('edit');
    foreach ($inherited as $userId)
    {
      unset($all[$userId]);
    }
    foreach ($sufficient as $userId)
    {
      unset($all[$userId]);
    }
    $this->setWidget(
      'archived',
      new sfWidgetFormSelect(
        array(
          'choices' => array(
            false => "active",
            true => "archived"
          )
        ),
        array(
          'class' => 'pk-radio-select'
        )));

    if ($this->getObject()->hasChildren())
    {
      unset($this['archived']);
    }

    $this->setWidget(
      'is_published',
      new sfWidgetFormSelect(
        array(
          'choices' => array(
            false => "unpublished",
            true => "published"
          )
        ),
        array(
          'class' => 'pk-radio-select'
        )));

    $this->setWidget(
      'view_is_secure',
      new sfWidgetFormSelect(
        array(
          'choices' => array(
            false => "Public",
            true => "Login required"
          )
        ),
        array(
          'class' => 'pk-radio-select'
        )));

    $this->setWidget(
      'editors', 
      new sfWidgetFormSelect(
        array(
          // + operator is correct: we don't want renumbering when
          // ids are numeric
          'choices' => 
            array("" => "Choose a User to Add") + $all,
          'multiple' => true,
          'default' => $selected)));

    $this->setValidator(
      'slug',
      new sfValidatorAnd(
        array(
          new sfValidatorRegex(
            array(
              'pattern' => '/^\/[\w\/\-\+]+$/',
              'required' => 'The slug cannot be empty.',
            ),
            array(
              'invalid' => 'The slug must contain only slashes, letters, digits, dashes, plus signs and underscores. Also, you cannot change a slug to conflict with the home page slug.')
        ))));
    $this->setValidator(
      'template',
      new sfValidatorChoice(
        array('required' => true,
          'choices' => array_keys(pkContextCMSTools::getTemplates()))));
    $this->setValidator(
      'editors',
      new sfValidatorChoice(
        array('required' => false, 
          'multiple' => true,
          'choices' => array_keys($all))));

    // The slug of the home page cannot change (chicken and egg problems)
    if ($this->getObject()->getSlug() === '/')
    {
      unset($this['slug']);
    }
    else
    {
      $this->validatorSchema->setPostValidator(
        new sfValidatorDoctrineUnique(
          array('model' => 'pkContextCMSPage',
            'column' => 'slug'),
          array('invalid' => 'There is already a page with that slug.')));
    }
    $this->widgetSchema->setIdFormat('pk_context_cms_settings_%s');
    $this->widgetSchema->setNameFormat('settings[%s]');
    $this->widgetSchema->setFormFormatterName('list');

    $user = sfContext::getInstance()->getUser();
    if (!$user->hasCredential('cms_admin'))
    {
      unset($this->editors);
      unset($this->slug);
    }
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

    // Only true for admins
    if (isset($this['editors']))
    {
      $editorIds = $this->getValue('editors');
      // Happens when the list is empty (sigh)
      if ($editorIds === null)
      {
        $editorIds = array();
      }
      $object->setAccessesById('edit', $editorIds);
    }
  }
}
