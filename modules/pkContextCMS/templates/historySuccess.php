<?php use_helper('Form', 'jQuery') ?>
<?php echo jq_form_remote_tag(
  array(
    'update' => "pk-context-cms-contents-$name",
    'url' => 'pkContextCMS/revert',
    'script' => true),
  array(
    "name" => "pk-context-cms-vc-form-$name", 
    "id" => "pk-context-cms-vc-form-$name")) ?>
<?php echo input_hidden_tag('id', $id)?>
<?php echo input_hidden_tag('name', $name)?>
<?php echo input_hidden_tag('subaction', '', array("id" => "pk-context-cms-vc-subaction-$name"))?>
<?php echo select_tag('version',
  options_for_select(
    $versions, $version), array("id" => "pk-context-cms-vc-$name-version")) ?>
<?php echo submit_tag("Preview", array(
  "name" => "preview", "class" => "submit", "id" => "pk-context-cms-preview-$name", "onClick" => "$('#pk-context-cms-vc-subaction-$name').val('preview'); return true")) ?>
<?php echo submit_tag("Revert", array(
  "name" => "revert", "class" => "submit", "id" => "pk-context-cms-revert-$name", "onClick" => "$('#pk-context-cms-vc-subaction-$name').val('revert'); return true")) ?>
<?php echo submit_tag("Cancel", array(
  "name" => "cancel", "class" => "submit", "id" => "pk-context-cms-cancel-$name", "onClick" => "$('#pk-context-cms-vc-subaction-$name').val('cancel'); return true")) ?>
</form>
