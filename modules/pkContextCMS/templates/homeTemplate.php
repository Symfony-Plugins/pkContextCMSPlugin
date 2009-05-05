<?php use_helper('pkContextCMS') ?>

<?php // A way to introduce separate body classes for separate templates. ?>
<?php // Here we use the same one for the home and default templates but ?>
<?php // you could set something else ?>
<?php slot('body_class') ?>pk-home<?php end_slot() ?>

<?php if (!pkContextCMSTools::getCurrentPage()->userHasPrivilege('edit')): ?>
  <?php # Remove the breadcrumb trail and side nav unless the user has ?>
  <?php # editing privs on the home page, in which case we need their ?>
  <?php # editing features. We do display tabs on the home page. ?>
  <?php slot('breadcrumb') ?>
  <?php end_slot() ?>
  <?php slot('subnav') ?>
  <?php end_slot() ?>
<?php endif ?>

<div class="main">
	<div class="content-container">
		<div class="content">
			<?php pk_context_cms_area('body',
        array('allowed_types' => array('pkContextCMSRichText', 'pkContextCMSText'),
          'type_options' => array(
						'pkContextCMSRichText' => array('tool' => 'Main'), 	
            'pkContextCMSText' => array('multiline' => true)))) ?> 
		</div>
	</div>
</div>


<div class="sidebar">
	<div class="content-container">	
		<div class="content ">
			<?php pk_context_cms_slot('sidebar', 'pkContextCMSRichText', array('tool' => 'Sidebar')) ?>
		</div>
	</div>
</div>
