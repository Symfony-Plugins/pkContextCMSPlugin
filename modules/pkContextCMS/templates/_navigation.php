<ul class="pk-nav <?php echo $classes ?>">
<?php foreach ($navigation as $id => $item): ?>
<li class="pk-nav-item <?php
  echo ($item->isFirst()) ? 'first ' : '';
  echo ($item->isLast()) ? 'last ' : '';
  echo ($item->isCurrent()) ? 'pk-current-page ' : ''
?>" id="pk-nav-item-<?php echo $id ?>">

<?php echo link_to($item->getName(), $item->getUrl()) ?> 
<?php if ($item->hasChildren()): ?>
<?php echo include_partial('pkContextCMS/navigation', array('page' => $page, 'navigation' => $item->getChildren(), 'classes' => $classes)); ?>
<?php endif ?>
</li>
<?php endforeach ?>
</ul>
