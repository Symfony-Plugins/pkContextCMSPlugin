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

<?php if (pkContextCMSTools::getCurrentPage()->userHasPrivilege('edit')): ?>
  <?php include_partial('pkContextCMS/globalTools') ?>
<?php endif ?>

	<div id="pk-wrapper">
		<?php // Demo requires an obvious way to test login ?>
	  <?php include_partial("pkContextCMS/login") ?>

    <div id="header">
      <?php pk_context_cms_slot("logo", 'pkContextCMSImage', array("global" => true, "width" => 240, "height" => 140, "resizeType" => "c", "link" => "/")) ?>
  		<?php pk_context_cms_slot('header', 'pkContextCMSRichText', array("global" => true)) ?>
    </div>

		<?php pk_context_cms_area('pk-header', array(
			'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSText'),
		  'type_options' => array(
				'pkContextCMSRichText' => array('tool' => 'Main'), 	
		  	'pkContextCMSText' => array('multiline' => true)
			))) ?>

		<?php include_component('pkContextCMS', 'tabs') # Top Level Navigation ?>

		<?php echo $sf_data->getRaw('sf_content') ?>

	  <?php pk_context_cms_slot('footer', 'pkContextCMSRichText', array("global" => true)) ?>

	</div>

</body>
</html>
