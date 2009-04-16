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

		<?php include_component('pkContextCMS', 'tabs') # Top Level Navigation ?>

		<?php echo $sf_data->getRaw('sf_content') ?>

	  <?php pk_context_cms_slot('footer', 'pkContextCMSRichText', array("global" => true)) ?>

	</div>

</body>
</html>
