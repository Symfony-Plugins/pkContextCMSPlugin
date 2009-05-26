<?php use_helper('jQuery') ?>
<?php jq_add_plugins_by_name(array('sortable')) ?>
<?php // Semantic nested lists will not work with drag and drop. But a single list with ?>
<?php // manual indentation will work very well, and it allows us to emit special list items ?>
<?php // which are just virtual placeholders for dragging or dropping something to be before or ?>
<?php // after all of the existing peers at a particular level. It also keeps the code here ?>
<?php // in the template rather simple- structuring the tree as a flat list is taken care of ?>
<?php // at the controller level ?>
<style>
#pagetree li
{
  padding-left: 20px;
  display: none;
}
#pagetree li.toplevel
{
  display: block;
}
#pagetree li.pagetree-before,
#pagetree li.pagetree-after
{
  color: red;
}
</style>
<?php $lastLevel = 0 ?>
<ul id="pagetree">
  <?php foreach ($pageInfos as $pageInfo): ?>
    <?php $newLevel = $pageInfo['level'] ?>
    <?php if ($newLevel > $lastLevel): ?>
      <ul>
    <?php endif ?>
    <?php for ($i = $newLevel; ($i < $lastLevel); $i++): ?>
      </ul></li>
    <?php endfor ?>
    <?php $id = $pageInfo['id'] ?>
    <li id="<?php echo $id ?>" class="<?php echo ($newLevel < 2) ? 'toplevel' : '' ?>">
    <?php // If it has kids... ?>
    <?php if (isset($tree[$id])): ?>
      <?php echo link_to_function("&gt; ", "$('#$id > ul').children().show(); $('#$id-close').show(); $('#$id-open').hide();", array("id" => "$id-open")) ?>
      <?php echo link_to_function("&lt; ", "$('#$id > ul').children().hide(); $('#$id-close').hide(); $('#$id-open').show();", array("id" => "$id-close", 'style' => 'display: none')) ?>
    <?php else: ?>
      o
    <?php endif ?>
    <?php echo $pageInfo['title'] ?>
    <?php if (!isset($tree[$id])): ?>
      </li>
    <?php endif ?>
    <?php $lastLevel = $newLevel ?>
  <?php endforeach ?>
<?php for ($i = 0; ($i < $lastLevel); $i++): ?>
  </ul></li>
<?php endfor ?>
</ul>
<script>
$('#pagetree li').draggable();
$('#pagetree li').droppable({
  drop: function(event, ui)
  { 
    var e = ui.draggable;
    $(e).css({ top: '0px', left: '0px' });
    $(this).before(e);
  }
});
</script>