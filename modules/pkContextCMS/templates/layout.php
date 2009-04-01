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


	<?php include_partial('pkContextCMS/login') ?>
	<div class="container outer"> 	<?php // the outer crops the drop shadows as the browser collapses ?>
	<div class="container inner">
		<?php include_partial('pkContextCMS/search') ?>
    <div id="logo">
      <?php // Insert an image from the media plugin as our logo. ?>
      <?php // use the cropping feature, and link the logo to our home page ?>
      <?php pk_context_cms_slot("logo", 'pkContextCMSImage', array("global" => true, "width" => 400, "height" => 200, "resizeType" => "c", "link" => "/")) ?>
    </div>

		<?php include_component('pkContextCMS', 'tabs') # Top Level Navigation ?>

		<?php echo $sf_data->getRaw('sf_content') ?>
	</div>
  <div class="container footer">
  <?php // Use a global slot so it's the same on all pages ?>
  <?php pk_context_cms_slot('footer', 'pkContextCMSRichText', array("global" => true)) ?>
  </div>
	</div>

</body>
</html>
