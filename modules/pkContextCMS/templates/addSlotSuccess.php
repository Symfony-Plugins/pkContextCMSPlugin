<?php use_helper('jQuery') ?>
<?php include_component('pkContextCMS', 'area', 
  array('name' => $name, 'refresh' => true, 'addSlot' => $type, 'preview' => false))?>
<script type="text/javascript" charset="utf-8">
	$('#pk-context-cms-add-slot-form-<?php echo $name ?>').hide();
</script>
