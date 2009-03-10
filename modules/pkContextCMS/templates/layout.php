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
	<div class="container outter"> 	<!-- the outter crops the drop shadows as the browser collapses -->
	<div class="container inner">
		<h1 id="head"><a href="/">Our Company</a></h1>  

		<?php include_component('pkContextCMS', 'tabs') # Top Level Navigation ?>

		<?php echo $sf_data->getRaw('sf_content') ?>

	</div>
	</div>

</body>
</html>
