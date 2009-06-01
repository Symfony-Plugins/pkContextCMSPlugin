<?php if (sfConfig::get('app_demomode', false)): ?>
  <?php if ($sf_user->isAuthenticated()): ?>
    <?php echo link_to("Log Out", "home/demoLogout") ?>
  <?php else: ?>
    <?php echo link_to("Log In", "home/demoLogin") ?>
  <?php endif ?>
<?php endif ?>
