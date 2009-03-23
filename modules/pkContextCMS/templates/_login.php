<?php use_helper('pkContextCMS') ?>
<?php $user = sfContext::getInstance()->getUser() ?>
<?php if ($user->isAuthenticated()): ?>
<div id="pk-context-cms-admin-bar">

 	<ul class="pk-context-cms-admin-controls">
		<li><?php include_partial('pkContextCMS/showArchived') ?></li>
	</ul>

	<ul class="pk-context-cms-logout">
	<li>You are authenticated as <?php echo $user->getUsername() ?>.</li>
	<li><?php echo link_to("Log Out", sfConfig::get('app_pkContextCMS_actions_logout', "sfGuardAuth/signout")) ?></li>
	</ul>
</div>
<?php else: ?>
<ul class="pk-context-cms-login">
	<li>You are not logged in.</li>
	<li><?php echo link_to("Log In", sfConfig::get('app_pkContextCMS_actions_login', "sfGuardAuth/signin") . "?" . http_build_query(array("after" => $sf_request->getUri()))) ?></li>
</ul>
<?php endif ?>
