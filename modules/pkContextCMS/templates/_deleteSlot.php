<?php use_helper('Form', 'JavascriptBase', 'jQuery') ?>
<?php echo jq_form_remote_tag(
  array(
    'update' => "pk-context-cms-contents-$name",
    "url" => "pkContextCMS/addSlot?" . http_build_query(array('name' => $name, 'id' => $id)),
    "script" => true),
  array('id' => "pk-context-cms-add-slot-form-$name",
    'name' => "pk-context-cms-add-slot-form-$name",
    'style' => "display: none")) ?>
<?php echo select_tag('type',
  options_for_select(
    pkContextCMSTools::getSlotTypeOptions(),
    pkContextCMSTools::getOption($options, 'default-type', 
      'pkContextCMSRichText'))) ?>
<?php echo submit_tag('Add', array("class" => "submit")) ?>
<?php echo button_to_function('Cancel',
  "$('#pk-context-cms-add-slot-form-$name').hide()") ?>
</form>  
