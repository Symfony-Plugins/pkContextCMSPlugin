<?php use_helper('pkContextCMS') ?>
<?php $user = sfContext::getInstance()->getUser() ?>
<?php if ($user->isAuthenticated()): ?>
<div id="pk-context-cms-admin-bar">
	<div class="pk-context-cms-logout">
	<p>You are authenticated as <?php echo $user->getUsername() ?>.</p>
	<p><?php echo link_to("Log Out", sfConfig::get('app_pkContextCMS_actions_logout', "sfGuardAuth/signout")) ?></p>
  <?php include_partial('pkContextCMS/showArchived') ?>
	</div>
</div>
<?php else: ?>
<div class="pk-context-cms-login">
	<p>You are not logged in.</p>
	<p><?php echo link_to("Log In", sfConfig::get('app_pkContextCMS_actions_login', "sfGuardAuth/signin")) ?></p>
</div>
<?php endif ?>
