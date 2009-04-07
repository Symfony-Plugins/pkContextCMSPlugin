<?php use_helper('Form', 'jQuery') ?>

<div class="caution-padding">

	<h3 id="pk-context-cms-settings-heading">Page Settings</h3>

	<?php echo jq_form_remote_tag(
	  array(
	    'update' => "pk-context-cms-settings",
	    "url" => "pkContextCMS/settings",
	    "script" => true),
	  array(
	    "name" => "pk-context-cms-settings-form", 
	    "id" => "pk-context-cms-settings-form")) ?>

	<?php // We need this to distinguish the original AJAX POST from an ?>
	<?php // actual form submit; we can't use a name attribute on the ?>
	<?php // submit tag because that doesn't work in jq_form_remote_tag ?>

	<?php echo input_hidden_tag('submit', 1) ?>

	<?php echo $form['id'] ?>

		<div id="pk-context-cms-settings-left">
			<?php if (isset($form['slug'])): ?>
			  <div class="pk-context-cms-form-row">
			    <label>Page Slug</label>
			    <?php echo $form['slug'] ?>
			    <?php echo $form['slug']->renderError() ?>
			  </div>
			<?php endif ?>
			<div class="pk-context-cms-form-row">
			  <label>Page Template</label>
			  <?php echo $form['template'] ?>
			  <?php echo $form['template']->renderError() ?>
			</div>
			<div class="pk-context-cms-form-row">
			  <label>Page Status</label>
			  	<p><?php echo $form['view_is_secure'] ?></p>
					<?php if (isset($form['archived'])): ?>
			  	<p><?php echo $form['archived'] ?></p>
					<?php else: ?>
					<?php //edit by Rick 2.17.09 put the unarchived note in this else statement ?>
					<p id="pk-context-cms-settings-note" class="pk-note">This page has subpages which are turned on (see the side navigation for a list). If you wish to turn it off, you must first turn off its subpages.</p>
					<?php endif ?>
			</div>
		</div>
	
  <?php include_partial('pkContextCMS/privileges', 
    array('form' => $form, 'widget' => 'editors',
      'label' => 'Privilege:<br /> Page Editing', 'inherited' => $inherited['edit'],
      'admin' => $admin['edit'])) ?>
  <?php include_partial('pkContextCMS/privileges', 
    array('form' => $form, 'widget' => 'managers',
      'label' => 'Privilege:<br /> Add and Delete Pages', 'inherited' => $inherited['manage'],
      'admin' => $admin['manage'])) ?>
	<br class="c"/>
	
	<div id="pk-context-cms-settings-footer">
	<?php echo submit_tag("Save Changes", array("class" => "submit", "id" => "pk-context-cms-settings-submit")) ?>
	<span class="or">or</span>
	<?php echo jq_link_to_function('cancel', '$("#pk-context-cms-settings").slideUp(); $("#pk-context-cms-settings-button-open").show(); $("#pk-context-cms-settings-button-close").addClass("loading").hide()', array('class' => 'cancel', 'title' => 'cancel', )) ?>
	<?php if ($page->userHasPrivilege('manage')): ?>
    <?php # TBB: delete class made the delete button invisible! ?>
	  <?php echo link_to("Delete Page<span></span>", 
	    "pkContextCMS/delete?id=" . $page->getId(), 
	    array("confirm" => "Are you sure? This operation can not be undone. Consider archiving the page instead.", 'class' => 'pk-btn icon delete', )) ?>
	<?php endif ?>
	</div>

</form>

	<script>
	<?php // you can do this: { remove: 'custom html for remove button' } ?>
	pkMultipleSelect('#pk-context-cms-settings', { });

	<?php // you can do this: { linkTemplate: "<a href='#'>_LABEL_</a>",  ?>
	<?php //                    spanTemplate: "<span>_LINKS_</span>",     ?>
	<?php //                    betweenLinks: " " }                       ?>
	pkRadioSelect('.pk-radio-select', { });
	$('#pk-context-cms-settings').show();

	init_shadows();
	</script>

</div>
