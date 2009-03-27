<form id="pk-context-cms-search-pages" action="<?php echo url_for("pkContextCMS/search") ?>" method="GET">
<input type="text" name="q" value="<?php echo htmlspecialchars($sf_request->getParameter('q')) ?>"/> <input type="submit" class="submit" value="Search Pages" />
</form>
