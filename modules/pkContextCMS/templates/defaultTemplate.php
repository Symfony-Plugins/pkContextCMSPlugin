<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-default<?php end_slot() ?>

<?php pk_context_cms_area('body', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSImage', 'pkContextCMSButton', 'pkContextCMSSlideshow', 'pkContextCMSVideo', 'pkContextCMSPDF'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Main'), 	
		'pkContextCMSImage' => array('width' => 598, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSButton' => array('width' => 598, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSVideo' => array('width' => 598, 'flexHeight' => true, 'resizeType' => 's'),		
		'pkContextCMSSlideshow' => array("width" => 598, "flexHeight" => true),
		'pkContextCMSPDF' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),		
	))) ?>
	
<?php pk_context_cms_area('sidebar', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSImage', 'pkContextCMSButton', 'pkContextCMSSlideshow', 'pkContextCMSVideo', 'pkContextCMSPDF'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Sidebar'),
		'pkContextCMSImage' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSButton' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSVideo' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),				
		'pkContextCMSSlideshow' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSPDF' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),		
	))) ?>
