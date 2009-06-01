<?php use_helper('Form', 'jQuery') ?>

<?php $n=0; foreach ($versions as $version => $data): ?>
<tr class="pk-history-item" id="pk-history-item-<?php echo $n ?>">
	<td class="id">
		ID#
	</td>
	<td class="date">
		<?php echo date("j M Y - g:iA", strtotime($data['created_at'])); ?>
	</td>
	<td class="editor">
		<?php echo $data['author'] ?>
	</td>
	<td class="preview">
		<?php echo $data['diff'] ?>		
	</td>
</tr>
<?php $n++; endforeach ?>

<?php $n=0; foreach ($versions as $version => $data): ?>
<script>
  $("#pk-history-item-<?php echo $n ?>").data('params',
		{ 'preview': 
			{ 
	      id: <?php echo $id ?>, 
	      name: <?php echo json_encode($name) ?>, 
	      subaction: 'preview', 
	      version: <?php echo json_encode($version) ?>
	    },
			'revert':
			{
	      id: <?php echo $id ?>, 
	      name: <?php echo json_encode($name) ?>, 
	      subaction: 'revert', 
	      version: <?php echo json_encode($version) ?>
			},
			'cancel':
			{
	      id: <?php echo $id ?>, 
	      name: <?php echo json_encode($name) ?>, 
	      subaction: 'cancel', 
	      version: <?php echo json_encode($version) ?>
			}
		});
</script>
<?php $n++; endforeach ?>

<?php if (count($versions) == 0): ?>
	<tr class="pk-history-item">
		<td class="id">
		</td>
		<td class="date">
			No history found.
		</td>
		<td class="editor">
		</td>
		<td class="preview">
		</td>
	</tr>
<?php endif ?>

<script type="text/javascript">
	$('.pk-history-item').click(function() {

		$('.pk-history-browser').hide();
		
	  var params = $(this).data('params');
	
		var targetArea = "#"+$(this).parent().attr('rel');					// this finds the associated area that the history browser is displaying
		var historyBtn = $(targetArea+ ' a.pk-history');				// this grabs the history button
		var cancelBtn = $(targetArea+ ' a.pk-cancel');					// this grabs the cancel button for this area
		var revertBtn = $(targetArea+ ' a.pk-history-revert');	// this grabs the history revert button for this area
		
		$(historyBtn).siblings('.pk-history-options').show();

	  $.post(
	    <?php echo json_encode(url_for('pkContextCMS/revert')) ?>,
	    params.preview,
	    function(result)
	    {
				$('#pk-slots-<?php echo $name ?>').html(result);
				$(targetArea).addClass('previewing-history');
				// cancelBtn.parent().addClass('cancel-history');				
				$(targetArea+' .pk-controls-item').siblings('.cancel, .history').css('display', 'block'); // turn off all controls initially				
				$(targetArea+' .pk-controls-item.cancel').addClass('cancel-history');				
				$(targetArea+' .pk-history-options').css('display','inline');
				$('.pk-page-overlay').hide();
				init_pk_controls(targetArea,'history-preview');
	    }
	  );

		// Assign behaviors to the revert and cancel buttons when THIS history item is clicked
		
		revertBtn.click(function(){
		  $.post(
		    <?php echo json_encode(url_for('pkContextCMS/revert')) ?>,
		    params.revert,
		    function(result)
		    {
					$('#pk-slots-<?php echo $name ?>').html(result);
					$('.pk-history-preview-notice').css('display','none');				
					$('a.pk-history').removeClass('pk-btn-disabled');
					$('.pk-history-options').hide();
					$(this).parents('.pk-controls').find('a.pk-cancel').parent().hide();
					$('.pk-page-overlay').hide();
					init_pk_controls(targetArea, 'history-revert');
		  	}
			);	
		});
			
		cancelBtn.click(function(){ // additional functionality added to the existing cancel button
		  $.post(
		    <?php echo json_encode(url_for('pkContextCMS/revert')) ?>,
		    params.cancel,
		    function(result)
		    {
		     $('#pk-slots-<?php echo $name ?>').html(result);
				 init_pk_controls(targetArea);
		  	}
			);
		});
							
	});

	$('.pk-history-item').hover(function(){
		$(this).css('cursor','pointer');
	},function(){
		$(this).css('cursor','default');		
	})
	
</script>

<?php
/*
<?php echo jq_form_remote_tag(
  array(
    'update' => "pk-context-cms-contents-$name",
    'url' => 'pkContextCMS/revert',
    'script' => true),
  array(
    "name" => "pk-context-cms-vc-form-$name", 
    "id" => "pk-context-cms-vc-form-$name")) ?>
<?php echo input_hidden_tag('id', $id)?>
<?php echo input_hidden_tag('name', $name)?>
<?php echo input_hidden_tag('subaction', '', array("id" => "pk-context-cms-vc-subaction-$name"))?>
<?php echo select_tag('version',
  options_for_select(
    $versions, $version), array("id" => "pk-context-cms-vc-$name-version")) ?>
<?php echo submit_tag("Preview", array(
  "name" => "preview", "class" => "submit", "id" => "pk-context-cms-preview-$name", "onClick" => "$('#pk-context-cms-vc-subaction-$name').val('preview'); return true")) ?>
<?php echo submit_tag("Revert", array(
  "name" => "revert", "class" => "submit", "id" => "pk-context-cms-revert-$name", "onClick" => "$('#pk-context-cms-vc-subaction-$name').val('revert'); return true")) ?>
<?php echo submit_tag("Cancel", array(
  "name" => "cancel", "class" => "submit", "id" => "pk-context-cms-cancel-$name", "onClick" => "$('#pk-context-cms-vc-subaction-$name').val('cancel'); return true")) ?>
</form>
	*/
?>

