<?php echo textarea_tag("value-$id",
  $value,
  array_merge($options, array("rich" => "fck"))) ?>
<script>
pkContextCMS.registerOnSubmit("<?php echo $id ?>", 
  function(slotId)
  {
    <?php # FCK doesn't do this automatically on an AJAX "form" submit ?>
    value = FCKeditorAPI.GetInstance('value-<?php echo $id ?>').GetXHTML();
    $('#value-<?php echo $id ?>').val(value);
  }
);
</script>
