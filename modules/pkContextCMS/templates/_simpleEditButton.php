<?php if (!isset($controlsSlot)): ?>
  <?php $controlsSlot = true ?>
<?php endif ?>
<?php if ($controlsSlot): ?>
<?php slot("pk-slot-controls-$name-$permid") ?>
<?php endif ?>
	<li class="pk-controls-item edit">
  <?php echo jq_link_to_function(isset($label) ? $label : "edit", "", 
				array(
					'id' => 'pk-slot-edit-'.$name.'-'.$permid, 
					'class' => isset($class) ? $class : 'pk-btn icon pk-edit', 
					'title' => isset($title) ? $title : 'Edit', 
	)) ?>
	<script type="text/javascript">
	$(document).ready(function(){
		var editBtn = $('#pk-slot-edit-<?php echo $name ?>-<?php echo $permid ?>');
		var editSlot = $('#pk-slot-<?php echo $name ?>-<?php echo $permid ?>');
		editBtn.click(function(event){
			$(this).parent().addClass('editing-now');
			$(editSlot).children('.pk-slot-content').children('.pk-slot-content-container').hide(); // Hide content
			$(editSlot).children('.pk-slot-content').children('.pk-slot-form').fadeIn();							// Show form
			pkUI($(this).parents('.pk-slot').attr('id'));
			return false;
		});
	})
	</script>
	</li>
<?php if ($controlsSlot): ?>
<?php end_slot() ?>
<?php endif ?>
  
