<?php use_helper('editablePathComponent') ?>
<div id="pk-context-cms-breadcrumb">
<?php $first = true; ?>
<?php $skipNext = false; ?>
<?php $pages[] = $page; ?>
<?php foreach ($pages as $p): ?>
  <?php if ($skipNext): ?>
    <?php $skipNext = false ?>
    <?php continue ?>
  <?php endif ?>
  <?php if (!$sf_user->hasCredential('cms_admin')): ?>
    <?php if ($p->template == 'grandchildren'): ?>
      <?php $skipNext = true ?>
    <?php endif ?>
  <?php endif ?>
  <?php if (!$first): ?>
  <?php echo ('<span class="pk-context-cms-breadcrumb-slash">/</span>') ?>
  <?php else: ?>
  <?php $first = false; ?>
  <?php endif ?>
  <?php $title = $p->getTitle() ?>
  <?php if ($p->archived): ?> 
    <?php $title = "<span class='pk-context-cms-archived'>$title</span>" ?>
  <?php endif ?>
  <?php if ($page === $p): ?>
    <?php echo('<div class="pk-context-cms-rename you-are-here" id="pk-context-cms-rename">') ?>
    <?php echo editable_path_component($title, "pkContextCMS/rename", array("id" => $page->id), $p->userHasPrivilege('edit'), "epc") ?>
	<?php else: ?>
    <?php echo link_to($title, $p->getUrl()) ?>
	<?php endif ?>
  <?php if ($page === $p): ?>
    <?php echo("</div>") ?>
    <?php if ($p->userHasPrivilege('edit')): ?>  
      <?php $id = $p->getId() ?>
      <?php echo jq_link_to_remote("Manage This Page", 
        array(
          "url" => "pkContextCMS/settings?id=$id",
          "update" => "pk-context-cms-settings",
          "script" => true,
					"loading" => "$('.pk-context-cms-settings-button.open').addClass('loading')", 
          "complete" => jq_visual_effect("slideDown", "#pk-context-cms-settings")."$('#pk-context-cms-settings-button-open').removeClass('loading').hide(); $('#pk-context-cms-settings-button-close').removeClass('loading').show()",
        ),  
        array('class' => 'pk-context-cms-settings-button open', 'id' => 'pk-context-cms-settings-button-open', 'title'=>'Manage This Page')) ?>
				<?php echo jq_link_to_function('Close Page Settings', '$("#pk-context-cms-settings-button-close").addClass("loading").hide(); $("#pk-context-cms-settings-button-open").show(); $("#pk-context-cms-settings").slideUp();', array('class' => 'pk-context-cms-settings-button close', 'id' => 'pk-context-cms-settings-button-close',  'title' => 'Close Page Settings', )) ?>
    <?php endif ?>		
  <?php endif ?>
<?php endforeach ?>
<?php if ($p->userHasPrivilege('manage')): ?>
	<span class="pk-context-cms-breadcrumb-slash">/</span>
  <span id="create_form"> 
  	<div class="you-are-here"><?php echo link_to_function("Add Page<span></span>", '$("#pk-context-add-child-form").fadeIn(); ' . jq_visual_effect("fadeOut", "#pk-context-add-child-button"), array("id" => "pk-context-add-child-button", 'class' => 'pk-btn add', ) ) ?>
	  <?php echo form_tag("pkContextCMS/create", array("id" => "pk-context-add-child-form", "style" => "display: none")) ?>
	  <?php echo input_hidden_tag("parent", $page->slug) ?>
	  <?php echo input_tag("title", "", array("class" => "pk-context-cms-add-page-title")) ?>
		<div class="pk-context-cms-breadcrumb-add-controls">
		  <?php echo submit_tag("Add", array("class" => "submit")) ?>
			<span class="or">or</span>
		  <?php echo link_to_function("cancel", jq_visual_effect("fadeOut", "#pk-context-add-child-form") . jq_visual_effect("fadeIn", "#pk-context-add-child-button"), array('class' => 'pk-cancel', )) ?>
		</div>
	  </form>
    </div>
  </span>
<?php endif ?>

<?php echo include_partial('postBreadcrumb', array('page' => $page)) ?>

</div>

<?php // You can put this anywhere ?>
<div id="pk-context-cms-settings" class="shadow"></div>
<br class="clear c"/>
