<?php slot("pk-slot-controls-$name-$permid") ?>
	<li class="pk-controls-item edit">
  <?php echo jq_link_to_function("edit", "", 
				array(
					'id' => 'pk-slot-edit-'.$name.'-'.$permid, 
					'class' => 'pk-btn icon pk-edit', 
					'title' => 'Edit', 
	)) ?>
	<script type="text/javascript">
	$(document).ready(function(){
		
		var editBtn = $('#pk-slot-edit-<?php echo $name ?>-<?php echo $permid ?>');
		var editSlot = $('#pk-slot-<?php echo $name ?>-<?php echo $permid ?>');
		
		editBtn.click(function(event){
			$(this).parent().addClass('editing-now');
			$(editSlot).children('.pk-slot-content').children('.pk-slot-content-container').hide(); // Hide content
			$(editSlot).children('.pk-slot-content').children('.pk-slot-form').fadeIn();	// I changed this to fadeIn from show() -- this seemed to help with the stroke re-draw bug we were experiencing.
			init_pk_controls($(this).parents('.pk-slot').attr('id'));
			// $(editSlot).children('.pk-messages').css('visibility','hidden'); // Hide the messages
			return false;
		});
	})
	</script>
	</li>
<?php end_slot() ?>

<?php if (!strlen($value)): ?>
  <?php if ($editable): ?>
    Click edit to add text.
  <?php endif ?>
<?php else: ?>
<?php echo $value ?>
<?php endif ?>

