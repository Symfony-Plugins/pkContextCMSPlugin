<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-hub<?php end_slot() ?>


<?php include_slot('before-breadcrumb') # Optional ?>

<?php include_component('pkContextCMS', 'breadcrumb') # Breadcrumb Navigation ?>

<?php include_slot('before-subnav') # Optional ?>

<?php // TODO: this REALLY should be a component ?>

<?php include_component('pkContextCMS', 'subnav') # Left Side Navigation ?>


<div id="pk-context-cms-content" class="main">

	<div class="content-container">
		<div class="content">

			<p>Before the body</p>3

			<?php pk_context_cms_slot('body', 'pkContextCMSRichText') ?>
			<p>
			After the body
			</p>
		</div>
	</div>
</div>
