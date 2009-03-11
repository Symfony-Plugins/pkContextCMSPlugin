<?php use_helper('pkContextCMS') ?>

<?php // A way to introduce separate body classes for separate templates. ?>
<?php // Here we use the same one for the home and default templates but ?>
<?php // you could set something else ?>
<?php slot('body_class') ?>pk-default<?php end_slot() ?>

<?php slot('tabs') ?>
<?php // No tabs on home page ?>
<?php end_slot() ?>

<?php if (pkContextCMSTools::getCurrentPage()->userHasPrivilege('edit')): ?>
	<?php include_component('pkContextCMS', 'breadcrumb') # Breadcrumb Navigation ?>
<?php endif ?>

<?php // You could remove this from the home page template. If you did, ?>
<?php // you'd want to provide a way to access it if you're an admin and ?>
<?php // you need to reorder the subpages of the home page ?>
<?php include_component('pkContextCMS', 'subnav') # Left Side Navigation ?>

<div id="pk-context-cms-content" class="main">
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


<div id="pk-context-cms-sidebar" class="sidebar">
	<div class="content-container">	
		<div class="content ">
			<?php pk_context_cms_slot('sidebar', 'pkContextCMSRichText', array('tool' => 'Sidebar')) ?>
		</div>
	</div>
</div>
