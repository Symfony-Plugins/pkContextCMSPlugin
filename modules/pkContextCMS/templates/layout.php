<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<?php use_helper('pkContextCMS') ?>
	<?php $page = pkContextCMSTools::getCurrentPage() ?>

<head>
	<?php include_http_metas() ?>
	<?php include_metas() ?>
	<?php include_title() ?>
	<link rel="shortcut icon" href="/favicon.ico" />
		
</head>

<?php // body_class allows you to set a class for the body element from a template ?>
<body class="<?php if (has_slot('body_class')): ?><?php include_slot('body_class') ?><?php endif ?>">

	<?php if (pkContextCMSTools::getCurrentPage()): ?>
		<?php if (pkContextCMSTools::getCurrentPage()->userHasPrivilege('edit')): ?>
		  <?php include_partial('pkContextCMS/globalTools') ?>
		<?php endif ?>
	<?php endif ?>

	<div id="pk-wrapper">
		<?php // Demo requires an obvious way to test login ?>

    <?php // You can easily suppress the logo by setting the pk-login Symfony slot ?>
    <?php // in a template (but if you do, think about how the user can still get home) ?>

    <div id="pk-header">
      <?php pk_context_cms_slot("logo", 'pkContextCMSImage', array("global" => true, "width" => 125, "height" => 200, "resizeType" => "s", "link" => "/", "defaultImage" => "/pkContextCMSPlugin/images/cmstest-sample-logo.png")) ?>
  		<?php pk_context_cms_slot('header', 'pkContextCMSRichText', array("global" => true)) ?>
    </div>

    <?php // You can easily suppress the tabs by setting the pk-login Symfony slot ?>
    <?php // in a template ?>

    <?php if (has_slot('pk-tabs')): ?>
      <?php include_slot('pk-tabs') ?>
    <?php else: ?>
		  <?php include_component('pkContextCMS', 'tabs') # Top Level Navigation ?>
		<?php endif ?>

		<div id="pk-content">
			<?php echo $sf_data->getRaw('sf_content') ?>
		</div>

	  <?php pk_context_cms_slot('footer', 'pkContextCMSRichText', array("global" => true)) ?>

    <?php // You can easily suppress this login prompt by setting the pk-login Symfony slot ?>
    <?php // in a template ?>
    <?php if (has_slot('pk-login')): ?>
      <?php include_slot('pk-login') ?>
    <?php else: ?>
  		<div id="pk-login">
  	  	<?php include_partial("pkContextCMS/login") ?>
  		</div>
    <?php endif ?>
	</div>

</body>
</html>
