<div class="pk-chad"></div>

<?php use_helper('Url', 'jQuery') ?>

	<?php echo jq_form_remote_tag(
	  array(
	    'update' => "pk-personal-settings",
	    "url" => "pkContextCMS/personalSettings",
			'complete' => '$(".pk-page-overlay").hide();', 
	    "script" => true),
	  array(
	    "name" => "pk-personal-settings-form", 
	    "id" => "pk-personal-settings-form")) ?>

	<h3 id="pk-personal-settings-heading">User Preferences for <span><?php echo $sf_user->getGuardUser()->getUsername() ?></span></h3>

	<?php // We need this to distinguish the original AJAX POST from an ?>
	<?php // actual form submit; we can't use a name attribute on the ?>
	<?php // submit tag because that doesn't work in jq_form_remote_tag ?>
  <input type="hidden" name="submit" value="1" />

	<?php echo $form ?>
	
	<ul id="pk-personal-settings-footer" class="pk-controls pk-personal-settings-form-controls">
		<li>
		  <input type="submit" name="pk-personal-settings-submit" value="Save Changes" id="pk-personal-settings-submit" class="pk-submit" />
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

<script type="text/javascript" charset="utf-8">
	<?php if (0): ?>
	pkMultipleSelect('#pk-personal-settings', { });
	pkRadioSelect('.pk-radio-select', { });
	<?php endif ?>

	$('#pk-personal-settings').show();
</script>