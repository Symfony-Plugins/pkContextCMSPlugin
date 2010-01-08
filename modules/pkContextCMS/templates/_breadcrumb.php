<ul id="pk-breadcrumb">

<?php $first = true; ?> 
<?php $skipNext = false; ?>
<?php $ancestorsInfo[] = array('title' => $page->title, 'slug' => $page->slug, 'archived' => $page->archived, 'id' => $page->id); ?> <?php // ancestors info doesn't include the page itself ?>

<?php foreach ($ancestorsInfo as $pinfo): ?>

  <?php if ($skipNext): ?>
    <?php $skipNext = false ?>
    <?php continue ?>
  <?php endif ?>

  <?php if (!$first): ?>
  	<li class="pk-breadcrumb-slash">/</li>
  <?php else: ?>
  	<?php $first = false; ?>
  <?php endif ?>
	
  <?php $title = $pinfo['title'] ?>
  <?php if ($pinfo['archived']): ?> 
    <?php $title = "<span class='pk-archived-page'>".$title."</span>" ?>
  <?php endif ?>

  <?php if ($page->id === $pinfo['id']): ?>
		<li class="pk-breadcrumb-title current-page" id="pk-breadcrumb-title-rename">
			<?php include_partial('pkContextCMS/renamePage', array('page' => $page, 'edit' => $page->userHasPrivilege('edit'))) ?>
		</li>
	<?php else: ?>
		<li class="pk-breadcrumb-title" id="pk-breadcrumb-title">
			<?php echo link_to($title, pkContextCMSTools::urlForPage($pinfo['slug'])) ?>
		</li>
	<?php endif ?>
	
  <?php if ($page->id === $pinfo['id']): ?>
    <?php if ($page->userHasPrivilege('edit')): ?>  
		<li class="pk-breadcrumb-page-settings">
      <?php $id = $page->id ?>
      <?php // Sets up open and close buttons, ajax loading of form ?>
      <?php echo pk_remote_dialog_toggle(
        array("id" => "pk-page-settings", 
          "label" => "Page Settings",
          "loading" => "/pkToolkitPlugin/images/pk-icon-page-settings-ani.gif",
          "chadFrom" => ".pk-breadcrumb-page-settings",
          "action" => "pkContextCMS/settings?id=$id")) ?>
		</li>												
    <?php endif ?>	
  <?php endif ?>

<?php endforeach ?>

<?php if ($page->userHasPrivilege('manage')): ?>
  <?php if (has_slot('pk_add_page')): ?>
    <?php include_slot('pk_add_page') ?>
  <?php else: ?>
  	<li class="pk-breadcrumb-slash">/</li>
    <li class="pk-breadcrumb-create-childpage">
			<?php include_partial('pkContextCMS/createPage', array('page' => $page, 'edit' => $page->userHasPrivilege('edit'))); ?>
    </li>	
  <?php endif ?>
<?php endif ?>
<?php include_partial('pkContextCMS/breadcrumbExtra', array('page' => $page)); ?>
</ul>

<script type="text/javascript">
	$(document).ready(function(){
		$('#pk-breadcrumb .pk-breadcrumb-form.add').hide();
		
		<?php if (0): ?>
		// I don't think we should filter this way, so I am commenting this out for now.
		<?php if ($page->userHasPrivilege('edit')): ?>
		 if ($.browser.msie && $.browser.version < 7) { $('#pk-breadcrumb').before('<h3 id="editing-disabled">Editing is not available in Internet Explorer 6. Use IE 7+ or Firefox.<\/h3>'); }
		<?php endif ?>
		<?php endif ?>		
	});
</script>