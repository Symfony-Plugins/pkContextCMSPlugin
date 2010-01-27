<?php use_helper('jQuery', 'Url') ?>

<?php $page = pkContextCMSTools::getCurrentPage() ?>

<?php if ($edit): ?>

  <form method="POST" action="<?php echo url_for('pkContextCMS/rename') ?>" id="pk-breadcrumb-rename-form" class="epc-form pk-breadcrumb-form rename">

	<?php $form = new pkContextCMSRenameForm($page) ?>
	<?php echo $form['id']->render(array('id' => 'pk-breadcrumb-rename-id', )) ?>
	<?php echo $form['title']->render(array('id' => 'pk-breadcrumb-rename-title')) ?>

	  <ul id="pk-breadcrumb-rename-controls" class="pk-form-controls pk-breadcrumb-controls rename" style="display:none;">
			<li>
				<input type="submit" value="Rename" class="pk-submit" />
			</li>
	  	<li>
				<?php echo jq_link_to_function("cancel", '', array('class' => 'pk-btn icon pk-cancel event-default')) ?>
	  	</li>
	  </ul>

  </form>

	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {

			var renameForm = $('#pk-breadcrumb-rename-form');
			renameForm.prepend('<b id="pk-breadcrumb-rename-title-spacer" style="display:none;float:left;white-space:nowrap;"><?php echo str_replace(' ','-',$page->getTitle()) ?></b>');

			var renameControls = $('#pk-breadcrumb-rename-controls');
			var renameSpacer = $('#pk-breadcrumb-rename-title-spacer');
			var renameSubmitBtn = $('#a-breadcrumb-rename-submit');			
			var renameInput = $('#pk-breadcrumb-rename-title');
			var renameInputWidth = checkInputWidth(renameSpacer.width());		
			renameInput.css('width', renameInputWidth);		

			var currentTitle = "<?php echo $page->getTitle() ?>"
			renameInput[0].value = currentTitle;
			var liveTitle = renameInput[0].value;
			
			renameInput.bind('cancel', function(e){
				renameSpacer.text(cleanTitle(currentTitle))
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
				$(this).oneTime(250, "hide", function() {
					renameControls.hide();
				});
				// Tried to capture the click on the submit and cancel the hide
				// $(document).mousedown(function(e){
				// 	target = $(e.target);
				// 	if (!target.hasClass('a-submit')) {
				// 		renameControls.hide();						
				// 	}
				// })				
			})
			
			renameSubmitBtn.click(function(){
				renameInput.focus();
			})

			renameInput.keydown(function(e){
				liveTitle = renameInput[0].value;
				renameSpacer.text(cleanTitle(liveTitle));				
				renameInputWidth = checkInputWidth(renameSpacer.width());
				renameInput.css('width', renameInputWidth);
			});

			renameInput.keyup(function(e){
				if ($.charcode(e) == 'escape')
				{
					renameInput.trigger('cancel');
				}			
				renameInputWidth = checkInputWidth(renameSpacer.width());
				renameInput.css('width', renameInputWidth);
			})

			renameControls.find('a.pk-cancel').click(function(){
				renameInput.trigger('cancel');
			});

			function checkInputWidth(w)
			{
				var minWidth = 20;
				var maxWidth = 250;
				if (w < minWidth)
				{
					return minWidth;
				} 
				else if (w > maxWidth)
				{
					// we are not enforcing maxWidth at the moment;
					// return maxWidth;
					return w+1;
				}
				else
				{
					return w+1;
				}
			}
			
			function cleanTitle(t)
			{
				return t.replace(/ /g,'-');
			}

		});

	</script>

<?php else: ?>

  <?php echo $page->getTitle() ?>

<?php endif ?>