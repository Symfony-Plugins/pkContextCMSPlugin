<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-2column<?php end_slot() ?>

<?php include_component('pkContextCMS','subnav') ?>

<?php pk_context_cms_area('column-one', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSText', 'pkContextCMSSlideshow', 'pkContextCMSVideo'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Main'), 	
  	'pkContextCMSText' => array('multiline' => true),
		'pkContextCMSImage' => array("width" => 480, "flexHeight" => true),
		'pkContextCMSButton' => array("width" => 480, "flexHeight" => true),
		'pkContextCMSPDF' => array("width" => 480, "flexHeight" => true),
		'pkContextCMSSlideshow' => array("width" => 480, "flexHeight" => true)
	))) ?>

<?php pk_context_cms_area('column-two', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSText', 'pkContextCMSSlideshow', 'pkContextCMSVideo'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Main'), 	
  	'pkContextCMSText' => array('multiline' => true),
		'pkContextCMSSlideshow' => array("width" => 198, "flexHeight" => true),
		'pkContextCMSButton' => array("width" => 198, "flexHeight" => true),
		'pkContextCMSPDF' => array("width" => 198, "flexHeight" => true)
	))) ?>