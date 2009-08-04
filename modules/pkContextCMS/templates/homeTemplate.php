<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-home<?php end_slot() ?>

<?php pk_context_cms_area('body', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSText', 'pkContextCMSSlideshow', 'pkContextCMSVideo'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Main'), 	
  	'pkContextCMSText' => array('multiline' => true),
		'pkContextCMSSlideshow' => array('width' => 960, 'height' => 320, 'resizeType' => 'c')
	))) ?>
	
<?php pk_context_cms_slot("sidebar", 'pkContextCMSImage', array("global" => true, "width" => 125, "height" => 200, "resizeType" => "s", "link" => "/", "defaultImage" => "/pkContextCMSPlugin/images/cmstest-sample-logo.png")) ?>	