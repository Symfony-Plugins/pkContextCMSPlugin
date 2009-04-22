<?php if (!strlen($value)): ?>
  <?php if ($editable): ?>
    Double-click to edit.
  <?php endif ?>
<?php else: ?>
<?php echo $value ?>
<?php endif ?>

