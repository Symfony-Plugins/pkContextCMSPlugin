<p>
<?php if (isset($options['directions'])): ?>
  <?php echo $options['directions'] ?>
<?php else: ?>
  Use this slot to add raw HTML markup, such as embed codes. 
<?php endif ?>
</p>
<p>
Use this
slot with caution. If bad markup causes the page to become uneditable, add ?safemode=1 to the URL
and edit the slot to correct the markup.
</p>

<?php echo textarea_tag("value", $value, 
  html_entity_decode(strip_tags($value)),
  array_merge(array("id" => "$id-value", 'class' => 'pkContextCMSRawHTMLSlot'), $options)) ?>

<script type="text/javascript">

$(document).ready (function() {
	
	$('textarea.pkContextCMSRawHTMLSlot').autogrow({
		// maxHeight: 400, //Max Height was causing problems.
		minHeight: 30,
		lineHeight: 16
	});
							
});

</script>