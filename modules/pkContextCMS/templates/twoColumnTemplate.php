<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-2column<?php end_slot() ?>

<?php if (pkContextCMSTools::getCurrentPage()->userHasPrivilege('edit')): ?>
	<?php include_component('pkContextCMS', 'breadcrumb') # Breadcrumb Navigation ?>
<?php endif ?>

<?php pk_context_cms_area('column-one', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSText', 'pkContextCMSSlideshow', 'pkContextCMSVideo'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Main'), 	
  	'pkContextCMSText' => array('multiline' => true)
	))) ?>

<?php pk_context_cms_area('column-two', array(
	'allowed_types' => array('pkContextCMSRichText', 'pkContextCMSText', 'pkContextCMSSlideshow', 'pkContextCMSVideo'),
  'type_options' => array(
		'pkContextCMSRichText' => array('tool' => 'Main'), 	
  	'pkContextCMSText' => array('multiline' => true)
	))) ?>	