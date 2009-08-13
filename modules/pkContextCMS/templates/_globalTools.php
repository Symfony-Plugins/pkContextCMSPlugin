<?php
/*
Global Tools
This will be the top bar across the site when logged in.

It will contain global admin buttons like Users, Page Settings, and the Breadcrumb.

These are mostly links to independent modules. 
*/
?>

<ul id="pk-global-toolbar">
  <?php // All logged in users, including guests with no admin abilities, need access to the ?>
  <?php // logout button. But if you have no legitimate admin roles, you shouldn't see the ?>
  <?php // apostrophe or the global buttons ?>

  <?php $buttons = pkContextCMSTools::getGlobalButtons() ?>
  <?php $page = pkContextCMSTools::getCurrentPage() ?>
  <?php $pageEdit = $page && $page->userHasPrivilege('edit') ?>
  <?php $cmsAdmin = $sf_user->hasCredential('cms_admin') ?>

  <?php if ($cmsAdmin || count($buttons) || $pageEdit): ?>

  	<?php //The Apostrophe ?>
  	<li class="pk-global-toolbar-apostrophe">
  		<?php echo jq_link_to_function('Apostrophe Now','',array('id' => 'the-apostrophe', )) ?>
  		<ul class="pk-global-toolbar-buttons pk-controls">
  			<?php $buttons = pkContextCMSTools::getGlobalButtons() ?>
  			<?php foreach ($buttons as $button): ?>
  			  <li><?php echo link_to($button->getLabel(), $button->getLink(), array('class' => 'pk-btn icon ' . $button->getCssClass())) ?></li>
  			<?php endforeach ?>
  			<li><?php echo jq_link_to_function('Cancel','',array('class' => 'pk-btn icon pk-cancel', )) ?></li>					
  		</ul>
  	</li>

  	<?php //Breadcrumb ?>
  	<?php if (pkContextCMSTools::getCurrentPage()): ?>
	  	<li class="pk-global-toolbar-breadcrumb">
	  		<?php include_component('pkContextCMS', 'breadcrumb') # Breadcrumb Navigation ?>
	  	</li>
  	<?php endif ?>

  	<li class="pk-global-toolbar-page-settings pk-page-settings-container">
  		<div id="pk-page-settings"></div>
  	</li>

  	<li class="pk-global-toolbar-user-settings pk-personal-settings-container">
			<div id="pk-personal-settings"></div>
    </li>

	<?php endif ?>

		<?php // Login / Logout ?>
		<li class="pk-global-toolbar-login pk-login">
			<?php include_partial("pkContextCMS/login") ?>
		</li>
</ul>

<script type="text/javascript">

	$(document).ready(function(){

		var aposToggle = 0;

	  $('#the-apostrophe').click(function(){
		
			if (!aposToggle)
			{
				$(this).addClass('open');
				$('.pk-global-toolbar-breadcrumb').hide();
				$('.pk-global-toolbar-buttons').fadeIn();
				$('.pk-global-toolbar-buttons .pk-cancel').fadeIn();			
				$('.pk-global-toolbar-buttons .pk-cancel').parent().show();
				aposToggle = 1;
			}
			else
			{
				closeApostrophe();				
				aposToggle = 0;
			}

		});
  
		$('.pk-global-toolbar-apostrophe .pk-cancel').click(function(){
			closeApostrophe();
			aposToggle = 0;
	  });      

		function closeApostrophe()
		{
			$('#the-apostrophe').removeClass('open');
			$('.pk-global-toolbar-buttons').hide();			
			$('.pk-global-toolbar-breadcrumb').fadeIn();
		}

	});

</script>

<?php if (pkContextCMSTools::getCurrentPage()): ?>
	<?php include_partial('pkContextCMS/historyBrowser') ?>
<?php endif ?>

<div class="pk-page-overlay"></div>