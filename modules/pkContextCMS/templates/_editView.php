<?php
  // Displays the slot's contents in an editable form. An HTML form
  // tag is already open at this point (see _slot.php).
  //
  // You'll override this by writing your own
  // executeEditView method in your own slot module's components class
  // which extends pkContextCMSBaseComponents, and providing an
  // _editView.php template in that module. Be sure to call 
  // parent::executeEditView() in that method
?>
<?php echo textarea_tag('value', $value, array('id' => $value)) ?>
