<?php use_helper('pkContextCMS') ?>

<div id="pk-context-cms-content" class="col720">
	<!-- <img src="/images/shadow_720px.png" /> -->

		<div class="content editing">

			<p>Before the body</p>

			<?php pk_context_cms_slot('body', 'pkContextCMSRichText', array('tool' => 'Main', )) ?>
			<p>
			After the body
			</p>
			<p>
			Here comes the footer which has a basic toolbar and uses custom
			parameters to set up the desired editor:
			</p>
			<?php pk_context_cms_slot('footer', 'pkContextCMSRichText', array('tool' => 'Default')) ?>


		</div>
</div>
