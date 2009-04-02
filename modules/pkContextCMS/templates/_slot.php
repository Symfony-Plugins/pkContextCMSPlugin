<?php use_helper('jQuery') ?>
<?php if ($editable): ?>
  <?php echo jq_form_remote_tag(
      array(
        'update' => "pk-context-cms-contents-$name-$permid",
        'url' => "$type/edit",
        'script' => true),
      array(
        "class" => "pk-context-cms-edit",
        "name" => "form-$id",
        "id" => "form-$id",
        "style" => "display: " . ($showEditor ? "block" : "none")
      )
    ); ?>
  <?php echo input_hidden_tag("slot", $name) ?>
  <?php echo input_hidden_tag("permid", $permid) ?>
  <?php echo input_hidden_tag("slug", $slug) ?>
  <?php # Necessary to redirect correctly after editing a global slot ?>
  <?php echo input_hidden_tag("real-slug", $realSlug) ?>
  <?php include_component($editModule, 
    "editView", 
    array(
      "name" => $name,
      "type" => $type,
      "permid" => $permid,
      "options" => $options,
      "validationData" => $validationData)) ?>

	<div class="form-row">  <?php // I HOPE YOU GUYS DON'T MIND I ADDED THIS FORM-ROW BECAUSE IT'S TOTALLY AWESOME ` rick ?>
	  <?php echo submit_tag("Save", array("onClick" => "window.pkContextCMS.callOnSubmit('$id'); return true", "class" => "submit")) ?>
	  <?php echo button_to_function("Cancel", "$('#form-$id').hide(); $('#content-$id').show()", array("class" => "submit")) ?>
	</div>

  </form>
<?php endif ?>
<div class="pk-context-cms-content<?php echo $outlineEditable ? " pk-context-cms-editable" : "" ?>" id="content-<?php echo $id ?>" style="display: <?php echo $showEditor ? "none" : "block"?>"
  <?php if ($outlineEditable): ?>
    onDblClick="<?php echo $showEditorJS ?>"
  <?php endif ?>
>
<?php include_component($normalModule, 
  "normalView", 
  array(
    "name" => $name,
    "type" => $type,
    "permid" => $permid,
    "options" => $options)) ?>
</div>
