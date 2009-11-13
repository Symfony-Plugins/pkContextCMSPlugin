
<ul class="nav-level-depth-<?php echo current($navigation)->getRelativeDepth()?>" id="pk-tab-navigation-<?php echo $nest ?>">
<?php foreach ($navigation as $id => $item): ?>
<li class="pk-tab-nav-item <?php
  echo ($item->isFirst()) ? 'first ' : '';
  echo ($item->isLast()) ? 'last ' : '';
  echo ($item->isCurrent()) ? 'pk-current-page ' : '';
  echo ($item->ancestorOfCurrentPage) ? 'ancestor-page ' : '';
  echo ($item->peerOfAncestorOfCurrentPage) ? 'ancestor-peer-page ' : '';
  echo ($item->peerOfCurrentPage) ? 'peer-page ' : '';
?>" id="pk-tab-nav-item-<?php echo $item->id ?>">
<?php echo link_to($item->getName(), $item->getUrl()) ?> 
<?php if ($item->hasChildren()): ?>
<?php echo include_partial('pkContextCMS/navigation', array('page' => $page, 'navigation' => $item->getChildren(), 'classes' => $classes, 'nest' => $nest + 1)); ?>
<?php endif ?>
</li>
<?php endforeach ?>
</ul>

