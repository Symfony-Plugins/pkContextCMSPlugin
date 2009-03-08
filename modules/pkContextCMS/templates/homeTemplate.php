<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-home<?php end_slot() ?>

<?php slot('tabs') ?>
<?php // No tabs on home page ?>
<?php end_slot() ?>

<?php if (pkContextCMSTools::getCurrentPage()->userHasPrivilege('edit')): ?>
	<?php include_component('pkContextCMS', 'breadcrumb') # Breadcrumb Navigation ?>
<?php endif ?>

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


<div id="pk-context-cms-content" class="sidebar">
	<div class="content-container">	
		<div class="content ">
			<?php pk_context_cms_slot('sidebar', 'pkContextCMSRichText', array('tool' => 'Sidebar')) ?>
		</div>
	</div>
</div>
