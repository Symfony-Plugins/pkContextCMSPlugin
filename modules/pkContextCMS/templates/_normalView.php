<?php
  // Displays the slot's contents in a non-editable form.
  //
  // You'll override this for your custom slot by writing your own 
  // executeNormalView method in your own slot module's components class 
   // which extends pkContextCMSBaseComponents, and providing an
  // _editView.php template in that module. Be sure to call 
  // parent::executeNormalView() in that method.
  //
  // Then again, if your custom slot is just a specialized HTML editor,
  // you might not override this component at all; you might just override
  // the editView component.
?>
<?php if (!strlen($value)): ?>
  <?php if ($editable): ?>
    Double-click to edit.
  <?php endif ?>
<?php else: ?>
<?php echo $value ?>
<?php endif ?>
