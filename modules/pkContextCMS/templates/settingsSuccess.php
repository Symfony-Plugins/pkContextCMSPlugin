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
			  <p>
			  <?php echo $form['is_published'] ?>
			  </p>
			  <p>
			  <?php echo $form['view_is_secure'] ?>
			  </p>
			<?php if (isset($form['archived'])): ?>
			  <p>
			  <?php echo $form['archived'] ?>
			  </p>
			<?php else: ?>
			<?php //edit by Rick 2.17.09 put the unarchived note in this else statement ?>
				<p id="pk-context-cms-settings-note" class="pk-note">
				This page has unarchived children. If you wish to archive it, you must first archive its children.
				</p>
			<?php endif ?>
			</div>
		</div>
	<?php if (isset($form['editors'])): ?>
	  <div id="pk-context-cms-settings-right">
	  	<div class="pk-context-cms-form-row">
		    <label>Page Editors</label>
				<div class="pk-context-cms-local-editors">
			    <h4>Local Editors</h4>
			    <?php echo $form['editors'] ?>
				</div>

				<?php if (count($inheritedEditors) > 0): ?>
				<div class="pk-context-cms-inherited-editors">
		    	<h4>Inherited Editors</h4>
			    <ul>
			    <?php foreach($inheritedEditors as $editorName): ?>
			      <li><?php echo htmlspecialchars($editorName) ?></li>
			    <?php endforeach ?>
			    </ul>
					<?php if(0): ?>
				    <h4>Admins</h4>
				    <ul>
				    <?php foreach($adminEditors as $editorName): ?>
				      <li><?php echo htmlspecialchars($editorName) ?></li>
				    <?php endforeach ?>
				    </ul>
					<?php endif ?>
				</div>
				<?php endif ?>
			
		  </div>
	  </div>
	<?php endif ?>

	<div id="pk-context-cms-settings-footer">
	<?php echo submit_tag("Save Changes", 
	  array("id" => "pk-context-cms-settings-submit")) ?>
	<span>or</span>
	<?php echo jq_link_to_function("cancel", 
	  "$('#pk-context-cms-settings').hide()", array("class"=>"cancel")) ?>
	<?php if ($page->userHasPrivilege('delete')): ?>
	  <?php echo link_to("Delete Page<span></span>", 
	    "pkContextCMS/delete?id=" . $page->getId(), 
	    array("confirm" => "Are you sure? This operation can not be undone. Consider archiving the page instead.", 'class' => 'pk-btn delete', )) ?>
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