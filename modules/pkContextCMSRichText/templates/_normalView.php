<?php include_partial('pkContextCMS/simpleEditButton',
  array('name' => $name, 'permid' => $permid)) ?>

<?php if (!strlen($value)): ?>
  <?php if ($editable): ?>
    Click edit to add text.
  <?php endif ?>
<?php else: ?>
<?php echo $value ?>
<?php endif ?>

