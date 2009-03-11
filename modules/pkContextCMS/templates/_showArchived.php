<?php $page = pkContextCMSTools::getCurrentPage(); ?>
<?php if ($page): ?>
    <?php if ($sf_user->getAttribute("show-archived", 
      false, "pk-context-cms")): ?>
      <?php echo link_to("Hide \"Off\" Pages", "pkContextCMS/showArchived?state=0&id=" . pkContextCMSTools::getCurrentPage()->getId()) ?>
    <?php else: ?>      
      <?php echo link_to("Show \"Off\" Pages", "pkContextCMS/showArchived?state=1&id=" . pkContextCMSTools::getCurrentPage()->getId()) ?>
    <?php endif ?>
<?php endif ?>
