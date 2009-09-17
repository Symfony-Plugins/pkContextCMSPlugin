<?php include_partial('pkContextCMS/simpleEditButton',
  array('name' => $name, 'permid' => $permid)) ?>

<?php if (!strlen($value)): ?>
  <?php if ($editable): ?>
    <p>
    <?php if (isset($options['directions'])): ?>
      <?php echo $options['directions'] ?>
    <?php else: ?>
      Click edit to add raw HTML markup, such as embed codes. 
    <?php endif ?>
    </p>
    <p>
    Use this
    slot with caution. If bad markup causes the page to become uneditable, add ?safemode=1 to the URL
    and edit the slot to correct the markup.
    </p>
  <?php endif ?>
<?php else: ?>
  <?php if ($sf_params->get('safemode')): ?>
    <?php echo htmlspecialchars($value) ?>
  <?php else: ?>
    <?php echo $value ?>
  <?php endif ?>
<?php endif ?>

