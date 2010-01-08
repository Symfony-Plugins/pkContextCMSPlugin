<?php use_helper('jQuery', 'Url') ?>

<?php $page = pkContextCMSTools::getCurrentPage() ?>

<?php if ($edit): ?>

  <form method="POST" action="<?php echo url_for('pkContextCMS/rename') ?>" id="pk-breadcrumb-rename-form" class="epc-form pk-breadcrumb-form rename">

	<?php $form = new pkContextCMSRenameForm($page) ?>
	<?php echo $form['id']->render(array('id' => 'pk-breadcrumb-rename-id', )) ?>
	<?php echo $form['title']->render(array('id' => 'pk-breadcrumb-rename-title', )) ?>

	  <ul id="pk-breadcrumb-rename-controls" class="pk-form-controls pk-breadcrumb-controls rename" style="display:none;">
			<li>
				<input type="submit" value="Rename" class="pk-submit" />
			</li>
	  	<li>
				<?php echo jq_link_to_function("cancel", '', array('class' => 'pk-btn icon pk-cancel event-default')) ?>
	  	</li>
	  </ul>

  </form>

<?php else: ?>

  <?php echo $page->getTitle() ?>

<?php endif ?>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		
		var renameForm = $('.pk-breadcrumb-form.rename')
		renameForm.prepend('<b class="pk-breadcrumb-rename-title-spacer" style="display:none;float:left;white-space:nowrap;"><?php echo $page->getTitle() ?></b>')

		var renameControls = $('#pk-breadcrumb-rename-controls');
		var renameSpacer = $('.pk-breadcrumb-rename-title-spacer');
		var renameInput = $('#pk-breadcrumb-rename-title');
		var renameInputWidth = checkInputWidth(renameSpacer.width());		
		renameInput.css('width', renameInputWidth);		
		
		var currentTitle = "<?php echo $page->getTitle() ?>";
		renameInput[0].value = currentTitle;
		var liveTitle = renameInput[0].value;
		
					
		renameInput.bind('cancel', function(e){
			renameSpacer.text(currentTitle)
			renameInput[0].value = currentTitle;
			renameInputWidth = checkInputWidth(renameSpacer.width());
			renameInput.css('width', renameInputWidth);
			renameControls.hide();
			renameInput.blur();
		});
		
		renameInput.focus(function(){
			renameControls.fadeIn();
			renameInput.select();
		})
		
		renameInput.blur(function(){
			renameControls.hide();
		})
		
		renameInput.keydown(function(e){
			liveTitle = renameInput[0].value;
			renameSpacer.text(liveTitle);
		});

		renameInput.keyup(function(e){
			if ($.charcode(e) == 'escape')
			{
				renameInput.trigger('cancel');
			}			
			renameInputWidth = checkInputWidth(renameSpacer.width());
			renameInput.css('width', renameInputWidth);
		});
		
		renameControls.find('.pk-cancel').click(function(){
			renameInput.trigger('cancel');
		});
		
		function checkInputWidth(w)
		{
			var minWidth = 50;
			var maxWidth = 250;
			if (w < minWidth)
			{
				return minWidth;
			} 
			else if (w > maxWidth)
			{
				// we are not enforcing maxWidth at the moment;
				// return maxWidth;
				return w;
			}
			else
			{
				return w;
			}
		}
		
	});
	
</script>