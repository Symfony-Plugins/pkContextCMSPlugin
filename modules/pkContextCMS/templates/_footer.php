<?php if (has_slot('pk-footer')): ?>
  <?php include_slot('pk-footer') ?>
<?php else: ?>
  <?php pk_context_cms_slot('footer', 'pkContextCMSRichText', array("global" => true)) ?>
<?php endif ?>
<?php // Feel free to shut this off in app.yml or override the footer partial in your app ?>
<?php if (sfConfig::get('app_pkContextCMS_credit', true)): ?>
<div class="pk-attribution">Built with <a href="http://www.apostrophenow.com/">Apostrophe</a></div>
<?php endif ?>