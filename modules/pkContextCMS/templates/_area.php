<?php use_helper('pkContextCMS', 'jQuery') ?>

<?php if (!$refresh): ?>

<!-- START SLOT -->
<div id="pk-context-cms-contents-container-<?php echo $name ?>" class="pk-context-cms-contents-container">
	
  <?php if ($editable): ?>
		<div class="pk-context-cms-add-slot-controls">
    
		<?php echo jq_link_to_remote("History",
      array(
        "url" => "pkContextCMS/history?" . http_build_query(
          array("id" => $page->id, "name" => $name)),
        "update" => "pk-context-cms-history-container-$name"
      ), array('class' => 'pk-context-cms-slot-history', ) ) ?>

    <div id="pk-context-cms-history-container-<?php echo $name ?>" class="pk-context-cms-history-container"></div>

    <?php if ($infinite): ?>
	      <?php echo link_to_function("Add Slot<span></span>", "$('#pk-context-cms-add-slot-form-$name').show(); $(this).hide()", array('class' => 'pk-btn add', )) ?>
	      <?php include_partial('pkContextCMS/addSlot', array('id' => $page->id, 'name' => $name, 'options' => $options)) ?>
    <?php endif ?>
		</div>

		<br class="clear c"/>
  <?php endif ?>


<?php endif ?>

<?php if ($preview): ?>
  <div class="pk-context-cms-vc-preview">
    You are previewing another version of this material. 
    This will not become the current version unless you click "Revert." If you change your
    mind, click "cancel."
  </div>
<?php endif ?>

<?php $i = 0 ?>
<?php foreach ($slots as $permid => $slot): ?>
   <?php if ($infinite): ?>
  	<?php if (isset($options['type_options'][$slot->type])): ?>
  	  <?php $slotOptions = $options['type_options'][$slot->type]; ?>
  	<?php else: ?>
  	  <?php $slotOptions = array() ?>
  	<?php endif ?>
  <?php else: ?>
  	<?php $slotOptions = $options ?>
  <?php endif ?>
  <?php $outlineEditableClass = "" ?>
  <?php if ($editable && ((isset($slotOptions['outline_editable']) && $slotOptions['outline_editable']) || $slot->isOutlineEditable())): ?>
    <?php $outlineEditableClass = "pk-context-cms-slot-has-outline" ?>
  <?php endif ?>
<div id="pk-context-cms-contents-<?php echo $name ?>" class="pk-context-cms-contents-all-slots">
	<div class="pk-context-cms-slot <?php echo $outlineEditableClass ?>">
		<div class="pk-context-cms-slot-controls">		
  <?php if ($infinite): ?>
    <?php if ($editable): ?>
      <?php echo jq_link_to_remote("Delete Slot", 
        array(
          "url" => "pkContextCMS/deleteSlot?" .
            http_build_query( 
              array("id" => $page->id,
                "name" => $name,
                "permid" => $permid)),
          "update" => "pk-context-cms-contents-$name"), array('class' => 'pk-context-cms-slot-controls-delete', 'title' => 'Delete Slot', )) ?>
      <?php if ($i > 0): ?>
        <?php echo jq_link_to_remote("Up",
          array(
            "url" => "pkContextCMS/moveSlot?" .
              http_build_query( 
                array("id" => $page->id,
                  "name" => $name,
                  "up" => 1,
                  "permid" => $permid)),
            "update" => "pk-context-cms-contents-$name"), array('class' => 'pk-context-cms-slot-controls-up', 'title' => 'Move Up', )) ?>
      <?php endif ?>
      <?php if (($i + 1) < count($slots)): ?>
        <?php echo jq_link_to_remote("Down",
          array(
            "url" => "pkContextCMS/moveSlot?" .
              http_build_query( 
                array("id" => $page->id,
                  "name" => $name,
                  "permid" => $permid)),
            "update" => "pk-context-cms-contents-$name"), array('class' => 'pk-context-cms-slot-controls-down', 'title' => 'Move Down', )) ?>
      <?php endif ?>
    <?php endif ?>
  <?php endif ?>
		</div>
		
  	<div id="pk-context-cms-contents-<?php echo $name ?>-<?php echo $permid?>">
  		<?php pk_context_cms_slot_body($name, $slot->type, $permid, array_merge(array("preview" => $preview), $slotOptions), array(), false) ?>
  	</div>
	</div>
</div>
<?php $i++; endforeach ?>

<?php if (!$refresh): ?>
		</div>

<!-- END SLOT -->
  <?php if (0): ?>
    <?php echo jq_sortable_element("#pk-context-cms-contents-$name", array(
    	'url' => 'pkContextCMS/sortArea?' .
        http_build_query(array('page' => $page->getId(), 'name' => $name)), 
      'handle' => ".pk-context-slot-reorder-handle")) ?>
  <?php endif ?>
<?php endif ?>
