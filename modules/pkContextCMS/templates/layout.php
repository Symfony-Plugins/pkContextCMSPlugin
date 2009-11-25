<?php // This is a copy of pkContextCMSPlugin/modules/pkContextCMS/templates/layout.php ?>
<?php // It also makes a fine site-wide layout, which gives you global slots on non-page templates ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<?php use_helper('pkContextCMS') ?>
	<?php $page = pkContextCMSTools::getCurrentPage() ?>

<head>
	<?php include_http_metas() ?>
	<?php include_metas() ?>
	<?php include_title() ?>
	<?php // 1.3 and up don't do this automatically (no common filter) ?>
	<?php include_javascripts() ?>
  <?php include_stylesheets() ?>
	<link rel="shortcut icon" href="/favicon.ico" />
		
</head>

<?php // body_class allows you to set a class for the body element from a template ?>
<body class="<?php if (has_slot('body_class')): ?><?php include_slot('body_class') ?><?php endif ?>">

  <?php // Everyone gets this now, but internally it determines which controls you should ?>
  <?php // actually see ?>
  
  <?php include_partial('pkContextCMS/globalTools') ?>

	<div id="pk-wrapper">
    <?php // Note that just about everything can be suppressed or replaced by setting a ?>
    <?php // Symfony slot. Use them - don't write zillions of layouts or do layout stuff ?>
    <?php // in the template (except by setting a slot). To suppress one of these slots ?>
    <?php // completely in one line of code, just do: slot('pk-whichever', '') ?>
      
    <?php if (has_slot('pk-search')): ?>
      <?php include_slot('pk-search') ?>
    <?php else: ?>
      <?php include_partial('pkContextCMS/search') ?>
    <?php endif ?>
    
    <?php if (has_slot('pk-header')): ?>
      <?php include_slot('pk-header') ?>
    <?php else: ?>
      <div id="pk-header">
        <?php if (has_slot('pk-logo')): ?>
          <?php include_slot('pk-logo') ?>
        <?php else: ?>
          <?php pk_context_cms_slot("logo", 'pkContextCMSImage', array("global" => true, "width" => 125, "flexHeight" => true, "resizeType" => "s", "link" => "/", "defaultImage" => "/pkContextCMSPlugin/images/cmstest-sample-logo.png")) ?>
        <?php endif ?>
    		<?php pk_context_cms_slot('header', 'pkContextCMSRichText', array("global" => true)) ?>
      </div>
    <?php endif ?>

    <?php if (has_slot('pk-tabs')): ?>
      <?php include_slot('pk-tabs') ?>
    <?php else: ?>
		  <?php include_component('pkContextCMS', 'tabs') # Top Level Navigation ?>
		<?php endif ?>

    <?php if (has_slot('pk-subnav')): ?>
      <?php include_slot('pk-subnav') ?>
    <?php elseif ($page): ?>
		  <?php include_component('pkContextCMS', 'subnav') # Subnavigation ?>
		<?php endif ?>

		<div id="pk-content">
			<?php echo $sf_data->getRaw('sf_content') ?>
		</div>
	
	  <?php include_partial('pkContextCMS/footer') ?>
	</div>

</body>
</html>
