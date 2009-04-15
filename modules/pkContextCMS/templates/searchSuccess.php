<?php slot('body_class') ?>pk-search-results<?php end_slot() ?>

<h2>Search Results for: <?php echo htmlspecialchars($sf_request->getParameter('q')) ?></h2>
<dl class="pk-context-cms-search-results">
<?php foreach ($results as $page): ?>
  <dt><?php echo link_to($page->getTitle(), $page->getUrl()) ?></dt>
  <dd><?php echo $page->getSearchSummary() ?></dd>
<?php endforeach ?>
</dl>
<div class="pk-context-cms-search-footer">
  <?php include_partial('pkPager/pager', array('pager' => $pager, 'pagerUrl' => $pagerUrl)) ?>
</div>

