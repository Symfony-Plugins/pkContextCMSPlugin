<?php use_helper('jQuery') ?>
<?php if ($editable): ?>
  <?php echo jq_form_remote_tag(
      array(
        'update' => "pk-slot-content-$name-$permid",
        'url' => "$type/edit",
				'complete' => 'pkUI("#pk-slot-'.$name.'-'.$permid.'");', 
        'script' => true),
      array(
        "class" => "pk-slot-form",
        "name" => "pk-slot-form-$name-$permid",
        "id" => "pk-slot-form-$name-$permid",
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
	<ul class="pk-controls pk-slot-save-cancel-controls">  
		<?php	// JB Note: I moved the submit button javascript down to the bottom ?>
	  <li><?php echo submit_tag("Save", array("class" => "submit pk-submit", 'id' => 'pk-slot-form-submit-'.$name.'-'.$permid, )) ?></li>
		<?php // JB Note: I moved the cancel javascript down to the bottom  ?>
	  <li><?php echo button_to_function("Cancel", "", array("class" => "pk-submit pk-cancel", 'id' => 'pk-slot-form-cancel-'.$name.'-'.$permid, )) ?></li>
	</ul>
  </form>
<?php endif ?>
<?php if ($editable): ?>
  <div class="pk-slot-content-container <?php echo $outlineEditable ? " pk-context-cms-editable" : "" ?>" id="pk-slot-content-container-<?php echo $name ?>-<?php echo $permid ?>" style="display: <?php echo $showEditor ? "none" : "block"?>">
<?php endif ?>
<?php include_component($normalModule, 
  "normalView", 
  array(
    "name" => $name,
    "type" => $type,
    "permid" => $permid,
    "options" => $options)) ?>
<?php if ($editable): ?>
  </div>
<?php endif ?>

<?php if ($editable): ?>
  <script type="text/javascript">
  $(document).ready(function() {

    var normalView = $('#pk-slot-<?php echo $name ?>-<?php echo $permid ?>');

		// CANCEL
		$('#pk-slot-form-cancel-<?php echo $name ?>-<?php echo $permid ?>').click(function(){
  		$(normalView).children('.pk-slot-content').children('.pk-slot-content-container').fadeIn();
  		$(normalView).children('.pk-slot-content').children('.pk-slot-form').hide();
  		$(this).parents('.pk-slot').find('.pk-slot-controls .edit').removeClass('editing-now');
 			$(this).parents('.pk-area.singleton').find('.pk-area-controls .edit').removeClass('editing-now'); // for singletons
  	});

		// SAVE 
  	$('#pk-slot-form-submit-<?php echo $name ?>-<?php echo $permid ?>').click(function(){
  			$(this).parents('.pk-slot').find('.pk-slot-controls .edit').removeClass('editing-now');
  			$(this).parents('.pk-area.singleton').find('.pk-area-controls .edit').removeClass('editing-now'); // for singletons
  			window.pkContextCMS.callOnSubmit('<?php echo $id ?>');
  			return true;
  	});
  });
  </script>
<?php endif ?>
