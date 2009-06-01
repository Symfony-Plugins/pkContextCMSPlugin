<?php // We can start calling regular textarea_tag when they fix ?>
<?php // http://trac.symfony-project.com/ticket/732 ?>

<?php use_helper('PkForm') ?>
<?php $tag = pk_textarea_tag("value-$id",
  $value, 
  array_merge($options, array("rich" => "fck"))) ?>
<?php $tag = str_replace('type="text"', 'type="hidden"', $tag) ?>
<?php echo $tag ?>
<script type="text/javascript">
pkContextCMS.registerOnSubmit("<?php echo $id ?>", 
  function(slotId)
  {
    <?php # FCK doesn't do this automatically on an AJAX "form" submit ?>
    var value = FCKeditorAPI.GetInstance('value-<?php echo $id ?>').GetXHTML();
    $('#value-<?php echo $id ?>').val(value);
  }
);
</script>
