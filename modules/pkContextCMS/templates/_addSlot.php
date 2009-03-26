<?php use_helper('Form', 'JavascriptBase', 'jQuery') ?>
<?php echo jq_form_remote_tag(
  array(
    'update' => "pk-context-cms-contents-$name",
    "url" => "pkContextCMS/addSlot?" . http_build_query(array('name' => $name, 'id' => $id)),
    "script" => true),
  array('id' => "pk-context-cms-add-slot-form-$name",
    'name' => "pk-context-cms-add-slot-form-$name",
		'class' => 'pk-context-cms-add-slot-form', 
    'style' => "display: none")) ?>
<?php echo select_tag('type',
  options_for_select(
    pkContextCMSTools::getSlotTypeOptions($options),
    pkContextCMSTools::getOption($options, 'default-type', 
      'pkContextCMSRichText'))) ?>
<?php echo submit_tag('Add', array('class' => 'submit', 'onmouseup'=>"$(this).parent().hide(); $(this).parent().parent().children('.pk-btn').show()"))?>
<span class="or">or</span>
<?php echo jq_link_to_function('cancel',"$('#pk-context-cms-add-slot-form-$name').hide(); $(this).parent().parent().children('.pk-btn').show()", array("class"=>"cancel")) ?>
</form>
   
  
