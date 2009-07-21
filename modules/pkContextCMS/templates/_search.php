<form id="pk-search-global" action="<?php echo url_for("pkContextCMS/search") ?>" method="GET" class="pk-search-form">
<input type="text" name="q" value="<?php echo htmlspecialchars($sf_request->getParameter('q')) ?>" class="pk-search-field"/> 
<input type="image" src="/pkContextCMSPlugin/images/pk-special-blank.gif" class="submit" value="Search Pages" />
</form>
