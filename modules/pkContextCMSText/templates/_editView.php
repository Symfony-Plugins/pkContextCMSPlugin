<?php if ($multiline): ?>
  <?php // Remember, it's preescaped with valid HTML escape characters. ?>
  <?php // And when multiline is active there are <br>'s as well, followed ?>
  <?php // by \n. On the save side we'll convert \n back to <br>\n. ?>
  <?php echo textarea_tag("value",
    html_entity_decode(strip_tags($value)),
    array_merge(array("id" => "$id-value", 'class' => 'pkContextCMSTextSlot multi-line'), $options)) ?>
<?php else: ?>
  <?php echo input_tag("value",
    html_entity_decode(strip_tags($value)),
    array_merge(array("id" => "$id-value", 'class' => 'pkContextCMSTextSlot single-line'), $options)) ?>
<?php endif ?>

<script type="text/javascript">

$(document).ready (function() {
	
	$('textarea.pkContextCMSTextSlot.multi-line').autogrow({
		// maxHeight: 400, //Max Height was causing problems.
		minHeight: 30,
		lineHeight: 16
	});
							
});

</script>