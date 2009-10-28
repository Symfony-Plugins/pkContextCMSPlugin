<div id="pk-search">
  <form id="pk-search-global" action="<?php echo url_for('pkContextCMS/search') ?>" method="get" class="pk-search-form">
    <input type="text" name="q" value="<?php echo htmlspecialchars($sf_params->get('q')) ?>" class="pk-search-field" id="pk-search-cms-field" /> 
    <input type="image" src="/pkContextCMSPlugin/images/pk-special-blank.gif" class="submit" value="Search Pages" />
  </form>
</div>

<script>
pkInputSelfLabel('#pk-search-cms-field', 'Search');
</script>