<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-default<?php end_slot() ?>

<?php include_component('pkContextCMS','subnav') ?>

<?php pk_context_cms_area('body', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSSlideshow', 'pkContextCMSVideo'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Main'), 	
  	'pkContextCMSText' => array('multiline' => true),
		'pkContextCMSSlideshow' => array("width" => 598, "flexHeight" => true)
	))) ?>
	
<?php pk_context_cms_area('sidebar', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSSlideshow', 'pkContextCMSVideo', 'pkContextCMSImage', 'pkContextCMSPDF'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Main'),
		'pkContextCMSSlideshow' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSImage' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSPDF' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),		
	))) ?>
