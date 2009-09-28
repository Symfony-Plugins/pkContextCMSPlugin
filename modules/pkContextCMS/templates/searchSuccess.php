<?php use_helper('pkContextCMS') ?>
<?php slot('body_class') ?>pk-search-results<?php end_slot() ?>


<div id="pk-search-results-container">

	<h2>Search: "<?php echo htmlspecialchars($sf_request->getParameter('q')) ?>"</h2>

	<dl class="pk-search-results">
	<?php foreach ($results as $result): ?>
	  <?php $url = $result->url ?>
	  <dt class="result-title <?php echo $result->class ?>">
			<?php echo link_to($result->title, $url) ?>
		</dt>
	  <dd class="result-summary"><?php echo $result->summary ?></dd>
		<dd class="result-url"><?php echo link_to($url, $url) ?></dd>
	<?php endforeach ?>
	</dl>

	<div class="pk-context-cms-search-footer">
	  <?php include_partial('pkPager/pager', array('pager' => $pager, 'pagerUrl' => $pagerUrl)) ?>
	</div>

</div>
