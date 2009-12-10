<ul class="pk-raw-html-info">
	<li>
		<?php if (isset($options['directions'])): ?>
	  	<?php echo $options['directions'] ?>
		<?php else: ?>
	  	Use this slot to add raw HTML markup, such as embed codes. 
		<?php endif ?>
	</li>
	<li>
		Use this slot with caution. If bad markup causes the page to become uneditable, add ?safemode=1 to the URL and edit the slot to correct the markup.
	</li>
</ul>

<?php if (0): ?>
<?php // We could echo the entire form here, which would include validation errors etc., but raw HTML slots are unvalidated by definition ?>
<?php echo $form ?>
<?php endif ?>

<?php // For this simple case we just want the form field without a label, and we know there are no validation errors to display ?>
<?php echo $form['value']->render() ?>

<script type="text/javascript">
	$(document).ready (function() {
		$('textarea.pkContextCMSRawHTMLSlotTextarea').autogrow({
			minHeight: 416,
			lineHeight: 16
		});
	});
</script>