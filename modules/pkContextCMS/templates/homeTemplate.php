<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-home<?php end_slot() ?>

<?php // Subnav is removed for the home page template because it is redundant ?>
<?php slot('pk-subnav', '') ?>

<?php pk_context_cms_area('body', array(
	'allowed_types' => array('pkContextCMSText', 'pkContextCMSRichText', 'pkContextCMSImage', 'pkContextCMSButton', 'pkContextCMSSlideshow', 'pkContextCMSVideo', 'pkContextCMSPDF'),
  'type_options' => array(
    'pkContextCMSText' => array('multiline' => true, 'class' => 'foobar'),
		'pkContextCMSRichText' => array('tool' => 'Main'), 	
		'pkContextCMSImage' => array('width' => 720, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSButton' => array('width' => 720, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSSlideshow' => array("width" => 720, "flexHeight" => true, 'resizeType' => 's', ),
		'pkContextCMSPDF' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),		
	))) ?>

<?php pk_context_cms_area('sidebar', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSImage', 'pkContextCMSButton', 'pkContextCMSSlideshow', 'pkContextCMSVideo', 'pkContextCMSPDF'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Sidebar'),
		'pkContextCMSImage' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSButton' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSSlideshow' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),
		'pkContextCMSPDF' => array('width' => 200, 'flexHeight' => true, 'resizeType' => 's'),		
	))) ?>
