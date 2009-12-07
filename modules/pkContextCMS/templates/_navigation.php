
<ul class="nav-level-depth-<?php echo $nest?>" id="pk-tab-navigation-<?php echo $name ?>-<?php echo $nest ?>">
<?php foreach ($navigation as $id => $item): ?>
<li class="pk-tab-nav-item <?php
  echo ($item->isFirst()) ? 'first ' : '';
  echo ($item->isLast()) ? 'last ' : '';
  echo ($item->isCurrent()) ? 'pk-current-page ' : '';
  echo ($item->ancestorOfCurrentPage) ? 'ancestor-page ' : '';
  echo ($item->peerOfAncestorOfCurrentPage) ? 'ancestor-peer-page ' : '';
  echo ($item->peerOfCurrentPage) ? 'peer-page ' : '';
?>" id="pk-tab-nav-item-<?php echo $name ?>-<?php echo $item->id ?>">
<?php echo link_to($item->getName(), $item->getUrl()) ?> 
<?php if ($item->hasChildren()): ?>
<?php echo include_partial('pkContextCMS/navigation', array('page' => $page, 'name' => $name, 'draggable' => $draggable, 'navigation' => $item->getChildren(), 'classes' => $classes, 'pID' => $item->id, 'nest' => $nest + 1)); ?>
<?php endif ?>
</li>
<?php endforeach ?>
</ul>

<?php if ($draggable): ?>


  <script type="text/javascript">
  //<![CDATA[
  $(document).ready(
    function() 
    {
      $("#pk-tab-navigation-<?php echo $name ?>-<?php echo $nest ?>").sortable(
      { 
        delay: 100,
        update: function(e, ui) 
        { 
          var serial = jQuery("#pk-tab-navigation-<?php echo $name ?>-<?php echo $nest ?>").sortable('serialize', {key:'pk-tab-nav-item[]'});
          var options = {"url":<?php echo json_encode(url_for('pkContextCMS/sortNav').'?page=' . $item->id); ?>,"type":"POST"};
          options['data'] = serial;
          $.ajax(options);

          // This makes the tab borders display properly after re-positioning
          $('.pk-tab-nav-item').removeClass('last');
          $('.pk-tab-nav-item').removeClass('first');
          $('.pk-tab-nav-item:first').addClass('first');
          $('.pk-tab-nav-item:last').addClass('last');          
        }
      });
    });
  //]]>
  </script>
<?php endif ?>