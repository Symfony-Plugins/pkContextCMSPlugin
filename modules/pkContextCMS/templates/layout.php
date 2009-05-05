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

	<div class="wrapper">

	<?php include_partial('pkContextCMS/login') ?>
		
    <h1 id="header">
      <?php pk_context_cms_slot("logo", 'pkContextCMSImage', array("global" => true, "width" => 400, "height" => 200, "resizeType" => "c", "link" => "/")) ?>
    </h1>
    <?php # Top-level nav (tabs), if not overridden by the tabs slot ?>
    <?php # (note that you can also just insert things before and after) ?>
    <?php if (has_slot('tabs')): ?>
      <?php include_slot('tabs') ?>
    <?php else: ?>
      <?php include_slot('before-tabs') ?>
      <?php include_component('pkContextCMS', 'tabs') ?>
      <?php include_slot('after-tabs') ?>
    <?php endif ?>
    <?php # Breadcrumb nav, if not overridden by the breadcrumb slot ?>
    <?php # (note that you can also just insert things before and after) ?>
    <?php if (has_slot('breadcrumb')): ?>
      <?php include_slot('breadcrumb') ?>
    <?php else: ?>
      <?php include_slot('before-breadcrumb') ?>
      <?php include_component('pkContextCMS', 'breadcrumb') ?>
      <?php include_slot('after-breadcrumb') ?>
    <?php endif ?>
    <?php # Side nav, if not overridden by the subnav slot ?>
    <?php # (note that you can also just insert things before and after) ?>
    <?php if (has_slot('subnav')): ?>
      <?php include_slot('subnav') ?>
    <?php else: ?>
      <?php include_slot('before-subnav') ?>
      <?php include_component('pkContextCMS', 'subnav') # Side Navigation ?>
      <?php include_slot('after-subnav') ?>
    <?php endif ?>
		<?php echo $sf_data->getRaw('sf_content') ?>

	  <?php pk_context_cms_slot('footer', 'pkContextCMSRichText', array("global" => true)) ?>

	</div>

</body>
</html>
