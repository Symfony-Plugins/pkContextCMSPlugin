<ul class="pk-controls">
<?php if ($sf_user->isAuthenticated()): ?>
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
