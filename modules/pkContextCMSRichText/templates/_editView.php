<?php echo $form['value']->render() ?>

<script type="text/javascript">
pkContextCMS.registerOnSubmit("<?php echo $id ?>", 
  function(slotId)
  {
    <?php # FCK doesn't do this automatically on an AJAX "form" submit on every major browser ?>
    var value = FCKeditorAPI.GetInstance('slotform-<?php echo $id ?>-value').GetXHTML();
    $('#slotform-<?php echo $id ?>-value').val(value);
  }
);
</script>
