<ul class="pk-controls" style="float:right;">
<?php if ($sf_user->isAuthenticated()): ?>
  <li><?php echo link_to("Log Out", "/logout", array('class' => 'pk-btn', )) ?></li>
<?php else: ?>
  <li><?php echo link_to("Log In", "/login", array('class' => 'pk-btn', )) ?></li>
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
