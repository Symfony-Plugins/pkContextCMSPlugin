<?php use_helper('pkContextCMS') ?>

<?php slot('body_class') ?>pk-hub<?php end_slot() ?>

<div class="main">

	<div class="content-container">
		<div class="content">

			<p>Before the body</p>

			<?php pk_context_cms_slot('body', 'pkContextCMSRichText') ?>
			<p>
			After the body
			</p>
		</div>
	</div>
</div>
