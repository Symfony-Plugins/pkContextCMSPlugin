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
  	<li>
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
  	<li>
  		<?php include_component('pkContextCMS', 'breadcrumb') # Breadcrumb Navigation ?>
  	</li>
  	<?php endif ?>

  	<li class="pk-page-settings-container">
  		<div id="pk-page-settings"></div>
  	</li>
  	<li class="pk-personal-settings-container">
            <div id="pk-personal-settings"></div>
    </li>  
	<?php endif ?>
	<?php // also log out button and settings button ?>
	<li class="pk-login">
		<?php include_partial("pkContextCMS/login") ?>
	</li>
</ul>

<script type="text/javascript">

	var apostropheOpenState = 0;

	function apostropheOpen() 
	{
		$(this).parent().siblings().hide();
		$('.pk-global-toolbar-buttons').fadeIn();
		$('.pk-global-toolbar-buttons .pk-cancel').fadeIn();			
		$('.pk-global-toolbar-buttons .pk-cancel').parent().show();
		apostropheOpenState = 1;
	}

	function apostropheClose() 
	{
	  $(this).parent().siblings().fadeIn();
	  $('.pk-global-toolbar-buttons').hide();			
		apostropheOpenState = 0;
	}

	$(document).ready(function(){
	  $('#the-apostrophe').click(function(){

	   if (!apostropheOpenState)
	   {
	     apostropheOpen();
	   }
	   else
	   {
	     apostropheClose();
	   }

		$(this).toggleClass('open');

	  });
  
		$('.pk-global-toolbar-buttons .pk-cancel').click(function(){
	  $(this).parent().parent().hide();
	  $(this).parent().parent().parent().siblings().fadeIn();
	  apostropheOpenState = 0;
	
	  });      

	});
	
</script>

<?php // TODO: Rewrite this with jQuery so it's lighter! ?>
<?php if (0): ?>
  
<script type="text/javascript">
var prevColorRed = randomInteger(256);
var prevColorGreen = randomInteger(256);
var prevColorBlue = randomInteger(256);
setNextColor();
var nextColorRed = prevColorRed;
var nextColorGreen = prevColorGreen;
var nextColorBlue = prevColorBlue;
var colorSteps = 50;
var colorStep = 0;
transition();

function transition()
{
  prevColorRed = nextColorRed;
  prevColorGreen = nextColorGreen;
  prevColorBlue = nextColorBlue;
  setNextColor();
  colorStep = 0;
}

function setNextColor()
{
  nextColorRed = randomInteger(256);
  nextColorGreen = randomInteger(256);
  nextColorBlue = randomInteger(256);
}

function interpolate(prev, next)
{
  return prev + (next - prev) * colorStep / colorSteps;
}

function tohex(n)
{
  return (n >> 4).toString(16) + (n & 15).toString(16);
}

function randomInteger(n)
{
  return Math.floor(Math.random() * n);
}

setInterval(
  function() 
  {
    var strobeme = document.getElementById('the-apostrophe');
    var b = '#';
    var i;
    var red = interpolate(prevColorRed, nextColorRed);
    var green = interpolate(prevColorGreen, nextColorGreen);
    var blue = interpolate(prevColorBlue, nextColorBlue);
    b += tohex(red);
    b += tohex(green);
    b += tohex(blue);
    strobeme.style.backgroundColor = b;
    colorStep++;
    if (colorStep == colorSteps)
    {
      transition();
    }
  }, 20);
</script>
<?php endif ?>

<?php if (pkContextCMSTools::getCurrentPage()): ?>
	<?php include_partial('pkContextCMS/historyBrowser') ?>
<?php endif ?>

<div class="pk-page-overlay"></div>