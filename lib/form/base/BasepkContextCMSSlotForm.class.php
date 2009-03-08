<?php

require_once(sfConfig::get('sf_lib_dir').'/form/base/BaseFormPropel.class.php');

/**
 * pkContextCMSSlot form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 11540 2008-09-14 15:23:55Z fabien $
 */
class BasepkContextCMSSlotForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'page_id'    => new sfWidgetFormInputHidden(),
      'author_id'  => new sfWidgetFormPropelChoice(array('model' => 'sfGuardUser', 'add_empty' => true)),
      'archived'    => new sfWidgetFormInputCheckbox(),
      'version'    => new sfWidgetFormInputHidden(),
      'culture'    => new sfWidgetFormInputHidden(),
      'name'       => new sfWidgetFormInputHidden(),
      'value'      => new sfWidgetFormTextarea(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'page_id'    => new sfValidatorPropelChoice(array('model' => 'pkContextCMSPage', 'column' => 'id', 'required' => false)),
      'author_id'  => new sfValidatorPropelChoice(array('model' => 'sfGuardUser', 'column' => 'id', 'required' => false)),
      'archived'    => new sfValidatorBoolean(array('required' => false)),
      'version'    => new sfValidatorPropelChoice(array('model' => 'pkContextCMSSlot', 'column' => 'version', 'required' => false)),
      'culture'    => new sfValidatorPropelChoice(array('model' => 'pkContextCMSSlot', 'column' => 'culture', 'required' => false)),
      'name'       => new sfValidatorPropelChoice(array('model' => 'pkContextCMSSlot', 'column' => 'name', 'required' => false)),
      'value'      => new sfValidatorString(array('required' => false)),
      'created_at' => new sfValidatorDateTime(array('required' => false)),
      'updated_at' => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pk_context_cms_slot[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'pkContextCMSSlot';
  }


}
