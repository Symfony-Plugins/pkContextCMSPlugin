<?php

require_once(sfConfig::get('sf_lib_dir').'/form/base/BaseFormPropel.class.php');

/**
 * pkContextCMSPage form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 11540 2008-09-14 15:23:55Z fabien $
 */
class BasepkContextCMSPageForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'tree_left'        => new sfWidgetFormInput(),
      'tree_right'       => new sfWidgetFormInput(),
      'slug'             => new sfWidgetFormInput(),
      'template'         => new sfWidgetFormInput(),
      'is_published'     => new sfWidgetFormInputCheckbox(),
      'view_is_secure'   => new sfWidgetFormInputCheckbox(),
      'view_credentials' => new sfWidgetFormInput(),
      'edit_credentials' => new sfWidgetFormInput(),
      'version'          => new sfWidgetFormInputHidden(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorPropelChoice(array('model' => 'pkContextCMSPage', 'column' => 'id', 'required' => false)),
      'tree_left'        => new sfValidatorInteger(),
      'tree_right'       => new sfValidatorInteger(),
      'slug'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'template'         => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'is_published'     => new sfValidatorBoolean(array('required' => false)),
      'view_is_secure'   => new sfValidatorBoolean(array('required' => false)),
      'view_credentials' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'edit_credentials' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'version'          => new sfValidatorPropelChoice(array('model' => 'pkContextCMSPage', 'column' => 'version', 'required' => false)),
      'created_at'       => new sfValidatorDateTime(array('required' => false)),
      'updated_at'       => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pk_context_cms_page[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'pkContextCMSPage';
  }


}
