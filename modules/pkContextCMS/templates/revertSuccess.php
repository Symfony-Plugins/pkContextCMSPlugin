<?php use_helper('jQuery') ?>
<?php include_component('pkContextCMS', 'area', 
  array('name' => $name, 'refresh' => true, 'preview' => $preview))?>
<?php if ($cancel || $revert): ?>
  <script type="text/javascript" charset="utf-8">
    $('#pk-context-cms-history-container-<?php echo $name?>').html("");
  </script>
 <?php endif ?>
