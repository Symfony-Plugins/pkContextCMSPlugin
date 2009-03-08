<?php $page = pkContextCMSTools::getCurrentPage(); ?>
<?php if ($page): ?>
    <?php # TODO: this "AJAX toggle with an impact on the page behavior" ?>
    <?php # really should be in a plugin somewhere. We did most of it in ?>
    <?php # the tagtools plugin but there's no support for triggering some ?>
    <?php # JS for each choice in that code. ?>
    <?php if ($sf_user->getAttribute("show-archived", 
      false, "pk-context-cms")): ?>
      <?php echo link_to("Hide Archived", "pkContextCMS/showArchived?state=0&id=" . pkContextCMSTools::getCurrentPage()->getId()) ?>
    <?php else: ?>      
      <?php echo link_to("Show Archived", "pkContextCMS/showArchived?state=1&id=" . pkContextCMSTools::getCurrentPage()->getId()) ?>
    <?php endif ?>
<?php endif ?>
