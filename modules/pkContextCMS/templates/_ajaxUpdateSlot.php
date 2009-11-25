<?php // 1.3 and up don't do this automatically (no common filter) ?>
<?php // We're using renderPartial so there is no layout to call this for us ?>
<?php include_javascripts() ?>
<?php include_stylesheets() ?>
<?php use_helper('pkContextCMS') ?>
<?php pk_context_cms_slot_body($name, $type, $permid, 
  $options, $validationData, $editorOpen) ?>
