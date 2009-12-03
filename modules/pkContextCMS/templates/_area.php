<?php use_helper('pkContextCMS', 'jQuery') ?>

<?php // We don't replace the area controls on an AJAX refresh, ?>
<?php // just the contents ?>
<?php if ($editable): ?>

<?php slot('pk-cancel') ?>
<!-- .pk-controls.area Cancel Button -->
<li class="pk-controls-item cancel">
	<a href="#" class="pk-btn icon pk-cancel">Cancel</a>					
</li>
<?php end_slot() ?>

<?php slot('pk-history-controls') // START - PK-HISTORY SLOT ====================================  ?>
<!-- .pk-controls.pk-area-controls History Module -->
<li class="pk-controls-item history">
  <?php $moreAjax = "jQuery.ajax({type:'POST',dataType:'html',success:function(data, textStatus){jQuery('#pk-history-items-$name').html(data);},url:'/admin/pkContextCMS/history/id/".$page->id."/name/$name/all/1'}); return false;"; ?>
	<?php echo jq_link_to_remote("History", array(
      "url" => "pkContextCMS/history?" . http_build_query(array("id" => $page->id, "name" => $name)),
			'before' => '$(".pk-history-browser .pk-history-items").attr("id","pk-history-items-'.$name.'");
									 $(".pk-history-browser .pk-history-items").attr("rel","pk-area-'.$name.'");
                   $(".pk-history-browser .pk-history-browser-view-more").attr("onClick", "'.$moreAjax.'").hide();',
      "update" => "pk-history-items-$name"), 
			array(
				'class' => 'pk-btn icon pk-history', 
	)); ?>
	<ul class="pk-history-options">
		<li><a href="#" class="pk-btn icon pk-history-revert">Save as Current Revision</a></li>
	</ul>
</li>
<?php end_slot() // END - PK-HISTORY SLOT ==================================== ?>


<?php endif ?>

<?php if (!$refresh): ?>
  <?php // Wraps the whole thing, including the area controls ?>
  <div id="pk-area-<?php echo $name ?>" class="pk-area">
    
  <?php // The area controls ?>
  <?php if ($editable): ?>
    <?php if ($infinite): ?>
		<ul class="pk-controls pk-area-controls">
			<!-- .pk-controls.pk-area-controls Add Slot Module -->
			<li class="pk-controls-item slot">
				<?php echo link_to_function("Add Slot", "", array('class' => 'pk-btn icon pk-add slot', )) ?>
				<ul class="pk-area-options slot">
	      	<?php include_partial('pkContextCMS/addSlot', array('id' => $page->id, 'name' => $name, 'options' => $options)) ?>
				</ul>
			</li>	
			
			<?php include_slot('pk-history-controls') ?>
			<?php include_slot('pk-cancel') ?>
			
		</ul>
    <?php endif ?>


  <?php endif ?>
	<?php if (!$infinite): ?>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function(){
				$('#pk-area-<?php echo $name ?>').addClass('singleton');
				$('#pk-area-<?php echo $name ?>.singleton .pk-slot-controls').prependTo($('#pk-area-<?php echo $name ?>')).addClass('pk-area-controls').removeClass('pk-slot-controls');
			});
		</script>
	<?php endif ?>

  <?php // End area controls ?>

<?php endif ?>

<?php if ($preview): ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$('.pk-history-preview-notice').fadeIn();
	})
</script>
<?php endif ?>

<?php $i = 0 ?>
<?php // On an AJAX refresh we are updating pk-slots-$name, ?>
<?php // so don't nest another one inside it ?>
<?php if (!$refresh): ?>
  <?php // Wraps all of the slots in the area ?>
  <div id="pk-slots-<?php echo $name ?>" class="pk-slots">
<?php endif ?>
<?php // Loop through all of the slots in the area ?>
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
    <?php $outlineEditableClass = "pk-slot-is-editable" ?>
  <?php endif ?>
 <?php // Generate the content of the CMS slot early and capture it to a ?>
 <?php // Symfony slot so we can insert it at an appropriate point... and we ?>
 <?php // will also insert its slot-specific controls via a separate ?>
 <?php // pk-slot-controls-$name-$permid slot that the slot implementation ?>
 <?php // provides for us ?>

 <?php slot("pk-slot-content-$name-$permid") ?>
   <?php pk_context_cms_slot_body($name, $slot->type, $permid, array_merge(array("preview" => $preview), $slotOptions), array(), $slot->isOpen()) ?>
 <?php end_slot() ?>

 <?php // Wraps an individual slot, with its controls ?>
	<div class="pk-slot <?php echo $slot->type ?> <?php echo $outlineEditableClass ?>" id="pk-slot-<?php echo $name ?>-<?php echo $permid ?>">
    <?php // John shouldn't we suppress this entirely if !$editable? ?>
    <?php // Controls for that individual slot ?>
    <?php if ($editable): ?>
		<ul class="pk-controls pk-slot-controls">		
      <?php if ($infinite): ?>
						<!-- <li class="drag-handle"><a href="#" class="pk-btn icon drag" title="Drag to Re-Order Slot">Drag to Re-Order Slot</a></li> -->
						<!-- <li class="slot-history"><a href="#" class="pk-btn icon history">Slot History</a></li> -->
          <?php if ($i > 0): ?>
						<li class="move-up">
            <?php echo jq_link_to_remote("Move", array(
                "url" => "pkContextCMS/moveSlot?" .http_build_query(array(
									"id" => $page->id,
									"name" => $name,
									"up" => 1,
									"permid" => $permid)),
									"update" => "pk-slots-$name",
									'complete' => 'pkUI()'), 
									array(
										'class' => 'pk-btn icon pk-arrow-up', 
										'title' => 'Move Up', 
						)) ?>
						</li>
          <?php endif ?>

          <?php if (($i + 1) < count($slots)): ?>
						<li class="move-down">
            <?php echo jq_link_to_remote("Move", array(
                "url" => "pkContextCMS/moveSlot?" .http_build_query(array(
									"id" => $page->id,
									"name" => $name,
									"permid" => $permid)),
									"update" => "pk-slots-$name",
									'complete' => 'pkUI()'), 
									array(
										'class' => 'pk-btn icon pk-arrow-down', 
										'title' => 'Move Down', 
						)) ?>
            </li>
        <?php endif ?>
      <?php endif ?>

      <?php // Include slot-type-specific controls if the ?>
      <?php // slot has any ?>
      <?php include_slot("pk-slot-controls-$name-$permid") ?>

			<?php if (!$infinite): ?>
			  <?php include_slot('pk-history-controls') ?>
				<?php include_slot('pk-cancel') ?>
			<?php endif ?>

      <?php if ($infinite): ?>
        <li class="delete">
          <?php echo jq_link_to_remote("Delete", array(
            "url" => "pkContextCMS/deleteSlot?" .http_build_query(array(
              "id" => $page->id,
              "name" => $name,
              "permid" => $permid)),
              "update" => "pk-slots-$name",
							'before' => '$(this).parents(".pk-slot").fadeOut();', 
							'complete' => 'pkUI()'), 
              array(
                'class' => 'pk-btn icon pk-delete', 
                'title' => 'Delete Slot',
								'confirm' => 'Are you sure you want to delete this slot?', )) ?>
        </li>			
      <?php endif ?>
		</ul>
		
  <?php endif ?>

		<?php // End controls for this individual slot ?>		
		
    <?php if ($editable): ?>
		<!-- <ul class="pk-messages pk-slot-messages">
			<li class="background"></li>
			<li><span class="message">Double Click to Edit</span></li>
		</ul> -->
		<?php endif ?>
		
    <?php // Wraps the actual content - edit and normal views - ?>
    <?php // for this individual slot ?>
  	<div class="pk-slot-content" id="pk-slot-content-<?php echo $name ?>-<?php echo $permid?>">
      <?php // Now we can include the slot ?>
      <?php include_slot("pk-slot-content-$name-$permid") ?>
  	</div>
	</div>
<?php $i++; endforeach ?>

<?php if (!$refresh): ?>
  <?php // Closes the div wrapping all of the slots ?>
  </div>
<?php // Closes the div wrapping all of the slots AND the area controls ?>
</div>
<?php endif ?>
<!-- END SLOT -->