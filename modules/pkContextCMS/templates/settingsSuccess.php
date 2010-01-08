<div class="pk-chad"></div>

<?php use_helper('Url', 'jQuery') ?>

	<?php echo jq_form_remote_tag(
	  array(
	    'update' => "pk-page-settings",
	    "url" => "pkContextCMS/settings",
			'complete' => '$(".pk-page-overlay").hide();', 
	    "script" => true),
	  array(
	    "name" => "pk-page-settings-form", 
	    "id" => "pk-page-settings-form")) ?>

	<h3 id="pk-page-settings-heading">Page Settings</h3>

	<?php // We need this to distinguish the original AJAX POST from an ?>
	<?php // actual form submit; we can't use a name attribute on the ?>
	<?php // submit tag because that doesn't work in jq_form_remote_tag ?>
  <input type="hidden" name="submit" value="1" />

	<?php echo $form['id'] ?>

		<div id="pk-page-settings-left">
			<?php if (isset($form['slug'])): ?>
			  <div class="pk-form-row slug">
			    <label>Page Slug</label>
			    <?php echo $form['slug'] ?>
			    <?php echo $form['slug']->renderError() ?>
			  </div>
			<?php endif ?>
			<div class="pk-form-row engine">
			  <label>Page Engine</label>
			  <?php echo $form['engine']->render(array('onClick' => 'pkUpdateEngineAndTemplate()')) ?>
			  <?php echo $form['engine']->renderError() ?>
			</div>
			<div class="pk-form-row template" id="pk-page-template">
			  <label>Page Template</label>
			  <?php echo $form['template'] ?>
			  <?php echo $form['template']->renderError() ?>
			</div>
			<div class="pk-form-row status">
			  <label>Page Status</label>
			  	<div class="pk-page-settings-status">
				    <?php echo $form['archived'] ?>
            <?php if(isset($form['cascade_archived'])): ?>
              <?php echo $form['cascade_archived'] ?> Cascade publish changes to children
            <?php endif ?> 
					</div>
			</div>			
			<div class="pk-form-row privacy">
			  <label>Page Privacy</label>
			  	<div class="pk-page-settings-status">
						<?php echo $form['view_is_secure'] ?>
						<?php if(isset($form['cascade_view_is_secure'])): ?>
                <?php echo $form['cascade_view_is_secure'] ?> Cascade privacy changes to children
            <?php endif ?> 
					</div>
			</div>
		</div>
	
  <div id="pk-page-settings-right">
	
		<h4>Page Permissions</h4>
	
		<div class="pk-page-permissions">
		  <?php include_partial('pkContextCMS/privileges', 
		    array('form' => $form, 'widget' => 'editors',
		      'label' => 'Editors', 'inherited' => $inherited['edit'],
		      'admin' => $admin['edit'])) ?>
		  <?php include_partial('pkContextCMS/privileges', 
		    array('form' => $form, 'widget' => 'managers',
		      'label' => 'Managers', 'inherited' => $inherited['manage'],
		      'admin' => $admin['manage'])) ?>
			</div>
  </div>
	
	<ul id="pk-page-settings-footer" class="pk-controls pk-page-settings-form-controls">
		<li>
		  <input type="submit" name="submit" value="Save Changes" class="pk-submit" id="pk-page-settings-submit" />
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
			<?php $childMessage = ''; ?>
			<?php if($page->hasChildren()): ?>
			<?php $childMessage = "This page has children that will also be deleted. "; ?>
			<?php endif; ?>
      <?php echo link_to("Delete This Page", "pkContextCMS/delete?id=" . $page->getId(), array("confirm" => $childMessage."Are you sure? This operation can not be undone. Consider archiving the page instead.", 'class' => 'pk-btn icon pk-delete')) ?>
    </li>
		<?php endif ?>
	</ul>

</form>
<script type="text/javascript" charset="utf-8">
	function pkUpdateEngineAndTemplate()
	{
	  if (!$('#pk_context_cms_settings_settings_engine').val().length)
	  {
	    $('#pk_context_cms_settings_settings_template').show();
	  }
	  else
	  {
	    $('#pk_context_cms_settings_settings_template').hide();
	  }
	}
	pkUpdateEngineAndTemplate();
	<?php // you can do this: { remove: 'custom html for remove button' } ?>
	pkMultipleSelect('#pk-page-settings', { });

	<?php // you can do this: { linkTemplate: "<a href='#'>_LABEL_</a>",  ?>
	<?php //                    spanTemplate: "<span>_LINKS_</span>",     ?>
	<?php //                    betweenLinks: " " }                       ?>
	pkRadioSelect('.pk-radio-select', { });
	$('#pk-page-settings').show();
</script>