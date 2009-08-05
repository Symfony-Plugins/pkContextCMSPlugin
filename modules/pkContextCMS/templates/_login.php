<ul class="pk-controls">
<?php if ($sf_user->isAuthenticated()): ?>
  <li id="pk-logged-in-as">You are logged in as <span><?php echo $sf_user->getGuardUser()->getUsername() ?></span></li>
	<?php if (sfConfig::get('app_pkContextCMS_personal_settings_enabled', false)): ?>
	<li class="pk-personal-settings-control">
	<?php // Sets up open and close buttons, ajax loading of form ?>
	<?php echo pk_remote_dialog_toggle(
	  array("id" => "pk-personal-settings", 
	    "title" => "Settings",
	    "loading" => "/pkToolkitPlugin/images/pk-icon-personal-settings-ani.gif",
	    "action" => "pkContextCMS/personalSettings",
	    "chadFrom" => ".pk-personal-settings-control")) ?>
	</li>												
	<?php endif ?>
  <li><?php echo link_to("Log Out", sfConfig::get('app_pkContextCMS_actions_logout', 'sfGuardAuth/signout'), array('class' => 'pk-btn', )) ?></li>
<?php else: ?>
  <li><?php echo link_to("Log In", sfConfig::get('app_pkContextCMS_actions_login', 'sfGuardAuth/signin'), array('class' => 'pk-btn', )) ?></li>
<?php endif ?>
</ul>


<?php if (0): ?>
  
<?php if (sfConfig::get('app_demomode', false)): ?>
  <?php if ($sf_user->isAuthenticated()): ?>
    <?php echo link_to("Log Out", "home/demoLogout") ?>
  <?php else: ?>
    <?php echo link_to("Log In", "home/demoLogin") ?>
  <?php endif ?>
<?php endif ?>

<?php endif ?>
