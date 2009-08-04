<div class="pk-chad"></div>

<?php use_helper('Form', 'jQuery') ?>

	<?php echo jq_form_remote_tag(
	  array(
	    'update' => "pk-personal-settings",
	    "url" => "pkContextCMS/personalSettings",
			'complete' => '$(".pk-page-overlay").hide();', 
	    "script" => true),
	  array(
	    "name" => "pk-personal-settings-form", 
	    "id" => "pk-personal-settings-form")) ?>

	<?php // We need this to distinguish the original AJAX POST from an ?>
	<?php // actual form submit; we can't use a name attribute on the ?>
	<?php // submit tag because that doesn't work in jq_form_remote_tag ?>
	<h3 id="pk-personal-settings-heading">Personal Settings</h3>

	<?php echo input_hidden_tag('submit', 1) ?>

	<?php echo $form ?>
	
	<ul id="pk-personal-settings-footer" class="pk-controls pk-personal-settings-form-controls">
		<li>
			<?php echo submit_tag("Save Changes", array("class" => "pk-submit", "id" => "pk-personal-settings-submit")) ?>
		</li>
		<li>
			<?php echo jq_link_to_function('Cancel', '
				$("#pk-personal-settings").slideUp(); 
				$("#pk-personal-settings-button-open").show(); 
				$("#pk-personal-settings-button-close").addClass("loading").hide()
				$(".pk-page-overlay").hide();', 
				array(
					'class' => 'pk-btn icon pk-cancel', 
					'title' => 'cancel', 
				)) ?>
		</li>
	</ul>

</form>

<script>
<?php if (0): ?>
pkMultipleSelect('#pk-personal-settings', { });
pkRadioSelect('.pk-radio-select', { });
<?php endif ?>
$('#pk-personal-settings').show();
</script>