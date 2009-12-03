<?php use_helper('jQuery', 'Form') ?>

<?php $page = pkContextCMSTools::getCurrentPage() ?>
<?php if ($edit): ?>
  <?php echo jq_link_to_function($page->getTitle(), 
							 "$('#epc-rename-form').fadeIn(250, function(){ $('#epc-rename-form .epc-value').focus(); }); 
							  $('#epc-rename-button').hide(); 
							  $('#pk-breadcrumb-title-rename').addClass('editing');
								$('.epc-rename-button-controls .pk-cancel').parent().show();", 
				        array(
									"id" => "epc-rename-button", 
									"class" => "epc-rename-button",
								)) ?>
	<?php echo form_tag('pkContextCMS/rename', 
      array(
				"id" => "epc-rename-form", 
        'class' => "epc-form pk-breadcrumb-form",	
			)) ?>
	<?php $form = new pkContextCMSRenameForm($page) ?>
	<?php echo $form['id'] ?>
	<?php echo $form['title'] ?>
  <ul class="pk-form-controls epc-rename-button-controls"><li><input type="submit" value="Rename" class="pk-submit" /></li>
  <li><?php echo jq_link_to_function("cancel",
  								'$("#epc-rename-form").hide(); 
  								 $("#pk-breadcrumb-title-rename").removeClass("editing"); 
  								 $("#epc-rename-button").fadeIn();', 
  								 array(
  									'class' => 'pk-btn icon pk-cancel', 
  								 )) ?>
  </li>
  </ul>
  </form>
<?php else: ?>
  <?php echo $page->getTitle() ?>
<?php endif ?>
