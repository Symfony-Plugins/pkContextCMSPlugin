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

<body class="<?php if (has_slot('body_class')): ?><?php include_slot('body_class') ?><?php endif ?>">

	<?php if (pkContextCMSTools::getCurrentPage()): ?>
		<?php if (pkContextCMSTools::getCurrentPage()->userHasPrivilege('edit')): ?>
		  <?php include_partial('pkContextCMS/globalTools') ?>
		<?php endif ?>
	<?php endif ?>

	<div id="pk-wrapper">
		<?php // Demo requires an obvious way to test login ?>

    <div id="pk-header">
      <?php pk_context_cms_slot("logo", 'pkContextCMSImage', array("global" => true, "width" => 125, "height" => 200, "resizeType" => "s", "link" => "/")) ?>
  		<?php pk_context_cms_slot('header', 'pkContextCMSRichText', array("global" => true)) ?>
    </div>

		<?php include_component('pkContextCMS', 'tabs') # Top Level Navigation ?>

		<div id="pk-content">
			<?php echo $sf_data->getRaw('sf_content') ?>
		</div>

	  <?php pk_context_cms_slot('footer', 'pkContextCMSRichText', array("global" => true)) ?>

		<div id="pk-login">
	  	<?php include_partial("pkContextCMS/login") ?>
		</div>

	</div>

</body>
</html>
