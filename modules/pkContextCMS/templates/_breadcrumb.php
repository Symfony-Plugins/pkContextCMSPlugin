<?php use_helper('editablePathComponent') ?>

<?php if ($page->userHasPrivilege('edit')): ?>
<script type="text/javascript">
	$(function() { 
		if ($.browser.msie && $.browser.version < 7) { $('#pk-breadcrumb').before('<h3 id="editing-disabled">Editing is not available in Internet Explorer 6. Use IE 7+ or Firefox.<\/h3>'); }
	});
</script>
<?php endif ?>

<ul id="pk-breadcrumb">

<?php $first = true; ?>
<?php $skipNext = false; ?>
<?php $pages[] = $page; ?>

<?php foreach ($pages as $p): ?>

  <?php if ($skipNext): ?>
    <?php $skipNext = false ?>
    <?php continue ?>
  <?php endif ?>

  <?php if (!$sf_user->hasCredential('cms_admin')): ?>
    <?php if ($p->template == 'grandchildren'): ?>
      <?php $skipNext = true ?>
    <?php endif ?>
  <?php endif ?>

  <?php if (!$first): ?>
  <?php echo ('<li class="pk-breadcrumb-slash">/</li>') ?>
  <?php else: ?>
  <?php $first = false; ?>
  <?php endif ?>
	
  <?php $title = $p->getTitle() ?>
  <?php if ($p->archived): ?> 
    <?php $title = "<span class='archived'>".$title."</span>" ?>
  <?php endif ?>

  <?php if ($page === $p): ?>
   <li class="pk-breadcrumb-title current-page" id="pk-breadcrumb-title-rename">
    <?php echo editable_path_component($title, "pkContextCMS/rename", array("id" => $page->id), $p->userHasPrivilege('edit'), "epc") ?>
   </li>
	<?php else: ?>
   <li class="pk-breadcrumb-title" id="pk-breadcrumb-title">
		<?php echo link_to($title, $p->getUrl()) ?>
	 </li>
	<?php endif ?>
	
  <?php if ($page === $p): ?>
    <?php if ($p->userHasPrivilege('edit')): ?>  
			<li class="pk-breadcrumb-page-settings">
      <?php $id = $p->getId() ?>
      <?php echo jq_link_to_remote("View Page Settings", 
        array(
          "url" => "pkContextCMS/settings?id=$id",
          "update" => "pk-page-settings",
          "script" => true,
					"before" => "$('.pk-page-settings-button.open').hide();
											 $('.pk-page-settings-loading').show();", 
          "complete" => "$('#pk-page-settings').fadeIn();
												 $('.pk-page-settings-loading').hide();
												 $('#pk-page-settings-button-close').show();
												var arrowPosition = parseInt($('.pk-breadcrumb-page-settings').offset().left);
												$('#pk-page-settings .pk-chad').css('left',arrowPosition+'px');
												 pkUI('#pk-page-settings');
												$('.pk-page-overlay').show();",
        ), array(
					'class' => 'pk-page-settings-button open', 
					'id' => 'pk-page-settings-button-open', 
					'title'=>'View Page Settings')) ?>
			<?php echo jq_link_to_function('Close Page Settings', 
								'$("#pk-page-settings-button-close").hide(); 
								 $("#pk-page-settings-button-open").show(); 
								 $("#pk-page-settings").hide();
								 $(".pk-page-overlay").hide();', 
								 array(
									'class' => 'pk-page-settings-button close', 
									'id' => 'pk-page-settings-button-close',  
									'title' => 'Close Page Settings', )) ?>
			<?php echo image_tag('/pkToolkitPlugin/images/pk-icon-page-settings-ani.gif', array('class' => 'pk-page-settings-loading', 'style' => 'display:none;',  )) ?>
			</li>												
    <?php endif ?>	
  <?php endif ?>

<?php endforeach ?>

<?php if ($p->userHasPrivilege('manage')): ?>
  <?php if (has_slot('pk_add_page')): ?>
    <?php include_slot('pk_add_page') ?>
  <?php else: ?>
  	<li class="pk-breadcrumb-slash">/</li>
    <li class="pk-breadcrumb-create-childpage">
  		<?php echo jq_link_to_function("Add Page", 
  							'$("#pk-breadcrumb-create-childpage-form").fadeIn(250, function(){ $(".pk-breadcrumb-create-childpage-title").focus(); }); 
  							 $("#pk-breadcrumb-create-childpage-button").hide(); 
  							 $("#pk-breadcrumb-create-childpage-button").prev().hide();
  							 $(".pk-breadcrumb-create-childpage-controls a.pk-cancel").parent().show();', 
  							 array(
  								'id' => 'pk-breadcrumb-create-childpage-button', 
  								'class' => 'pk-btn icon pk-add', 
  								)) ?>
  	  <?php echo form_tag("pkContextCMS/create", array("id" => "pk-breadcrumb-create-childpage-form", 'class' => 'pk-breadcrumb-form', )) ?>
  	  <?php echo input_hidden_tag("parent", $page->slug) ?>
  	  <?php echo input_tag("title", "", array("class" => "pk-breadcrumb-create-childpage-title pk-breadcrumb-input")) ?>
  		<ul class="pk-form-controls pk-breadcrumb-create-childpage-controls">
  		  <li><?php echo submit_tag("Create Page", array("class" => "pk-submit")) ?></li>
  		  <li><?php echo jq_link_to_function("cancel", 
  										'$("#pk-breadcrumb-create-childpage-form").hide(); 
  						 				 $("#pk-breadcrumb-create-childpage-button").fadeIn(); 
  						 				 $("#pk-breadcrumb-create-childpage-button").prev(".pk-i").fadeIn();', 
  										 array(
  											'class' => 'pk-btn icon pk-cancel', 
  											)) ?></li>
  		</ul>
  	  </form>

    </li>	
  <?php endif ?>
<?php endif ?>
</ul>

<?php // TBB: moved this out of the manage 'if', it's not really connected to its contents ?>
<script type="text/javascript">
	$(document).ready(function(){
		var actual_width = $('.epc-rename-button').width();
		var epc_controls_width = $('.epc-rename-button-controls').width();
		
		if ($.browser.safari) // Safari cannot read accurate widths (^above) at Dom ready
		{
			actual_width = 180;
			epc_controls_width = 115;
		}
		
		$('#pk-breadcrumb .pk-breadcrumb-form').hide();
		$('#pk-breadcrumb .epc-form input.epc-value').css('width', actual_width+5);
		$('#pk-breadcrumb .epc-form').css('width', actual_width+epc_controls_width+11);

	});
</script>
