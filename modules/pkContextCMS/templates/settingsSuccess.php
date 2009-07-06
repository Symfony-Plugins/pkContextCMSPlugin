<div class="pk-chad"></div>

<?php use_helper('Form', 'jQuery') ?>

	<?php echo jq_form_remote_tag(
	  array(
	    'update' => "pk-page-settings",
	    "url" => "pkContextCMS/settings",
			'complete' => '$(".pk-page-overlay").hide();', 
	    "script" => true),
	  array(
	    "name" => "pk-page-settings-form", 
	    "id" => "pk-page-settings-form")) ?>

	<?php // We need this to distinguish the original AJAX POST from an ?>
	<?php // actual form submit; we can't use a name attribute on the ?>
	<?php // submit tag because that doesn't work in jq_form_remote_tag ?>
	<h3 id="pk-page-settings-heading">Page Settings</h3>

	<?php echo input_hidden_tag('submit', 1) ?>

	<?php echo $form['id'] ?>

		<div id="pk-page-settings-left">
			<?php if (isset($form['slug'])): ?>
			  <div class="pk-form-row">
			    <label>Page Slug</label>
			    <?php echo $form['slug'] ?>
			    <?php echo $form['slug']->renderError() ?>
			  </div>
			<?php endif ?>
			<div class="pk-form-row">
			  <label>Page Template</label>
			  <?php echo $form['template'] ?>
			  <?php echo $form['template']->renderError() ?>
			</div>
			<div class="pk-form-row">
			  <label>Page Status</label>
			  	<div class="pk-page-settings-status">
						<?php echo $form['view_is_secure'] ?>
						
						<?php if (isset($form['archived'])): ?>
				  		<?php echo $form['archived'] ?>
						<?php else: ?>
							<div id="pk-page-settings-note" class="pk-note">This page has subpages which are turned on (see the side navigation for a list). If you wish to turn it off, you must first turn off its subpages.</div>
						<?php endif ?>
						
					</div>
			</div>
		</div>
	
  <div id="pk-page-settings-right">
	
		<h4>Page Permissions</h4>
	
	  <?php include_partial('pkContextCMS/privileges', 
	    array('form' => $form, 'widget' => 'editors',
	      'label' => 'Editors', 'inherited' => $inherited['edit'],
	      'admin' => $admin['edit'])) ?>
	  <?php include_partial('pkContextCMS/privileges', 
	    array('form' => $form, 'widget' => 'managers',
	      'label' => 'Managers', 'inherited' => $inherited['manage'],
	      'admin' => $admin['manage'])) ?>
  </div>
	
	<ul id="pk-page-settings-footer" class="pk-controls pk-page-settings-form-controls">
		<li>
			<?php echo submit_tag("Save Changes", array("class" => "pk-submit", "id" => "pk-page-settings-submit")) ?>
		</li>
		<li>
			<?php echo jq_link_to_function('Cancel', '
				$("#pk-page-settings").slideUp(); 
				$("#pk-page-settings-button-open").show(); 
				$("#pk-page-settings-button-close").addClass("loading").hide()
				$(".pk-page-overlay").hide();', 
				array(
					'class' => 'pk-btn icon pk-cancel', 
					'title' => 'cancel', 
				)) ?>
		</li>
		<?php if ($page->userHasPrivilege('manage')): ?>
		<li>
			<?php echo link_to("Delete Page", "pkContextCMS/delete?id=" . $page->getId(), array("confirm" => "Are you sure? This operation can not be undone. Consider archiving the page instead.", 'class' => 'pk-btn icon pk-delete', )) ?>
		</li>
		<?php endif ?>
	</ul>

</form>
<?php // Allows a page to be moved up or down in the hierarchy. ?>
<?php // This feature works but the UI currently leaves much to be desired. ?>
<?php if (sfConfig::get('app_pkContextCMS_up_and_down', false)): ?>
  <?php if ($page->userHasPrivilege('move-up') || $page->userHasPrivilege('move-down')): ?>
    <div class="pk-page-settings-move">
      <h4>Move the Page</h4>
      <p>Note: to move a page among its peers, just drag and drop that page in the side nav or tabs.</p>
      <?php if ($page->userHasPrivilege('move-up')): ?>
        <?php echo link_to("Up One Level", "pkContextCMS/moveUp?id=" . $page->id, array("class" => "pk-btn")) ?>
      <?php endif ?>
      <?php if ($page->userHasPrivilege('move-down')): ?>
        <?php $peerOptions = $page->getPeersAsOptionsArray() ?>
        <?php if (count($peerOptions)): ?>
          <?php echo jq_link_to_function("Down One Level", "$('#pk-page-settings-move-down-form').show(); $('#pk-page-settings-move-down-button').hide()", array("id" => "pk-page-settings-move-down-button")) ?>
          <form method="POST" id="pk-page-settings-move-down-form" style="display: none" action="<?php echo url_for('pkContextCMS/moveDown') ?>">
            <input type="hidden" name="id" value="<?php echo $page->id ?>" />
            <label>New Parent:</label>
            <?php echo select_tag('peer', options_for_select($page->getPeersAsOptionsArray())) ?>
            <?php echo submit_tag("Move", array("id" => "pk-page-move-down-submit")) ?>
      			<?php echo jq_link_to_function('Cancel', '
      				$("#pk-page-settings-move-down-form").hide(); 
      				$("#pk-page-settings-move-down-button").show();', 
      				array(
      					'class' => 'pk-btn icon pk-cancel', 
      					'title' => 'cancel', 
      				)) ?>
      		</form>
      	<?php endif ?>
      <?php endif ?>
    </div>
  <?php endif ?>
<?php endif ?>
	<script>
	<?php // you can do this: { remove: 'custom html for remove button' } ?>
	pkMultipleSelect('#pk-page-settings', { });

	<?php // you can do this: { linkTemplate: "<a href='#'>_LABEL_</a>",  ?>
	<?php //                    spanTemplate: "<span>_LINKS_</span>",     ?>
	<?php //                    betweenLinks: " " }                       ?>
	pkRadioSelect('.pk-radio-select', { });
	$('#pk-page-settings').show();
	</script>