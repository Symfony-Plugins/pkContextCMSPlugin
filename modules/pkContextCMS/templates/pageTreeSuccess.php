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
<ul id="pagetree">
  <?php foreach ($pageInfos as $pageInfo): ?>
    <li id="<?php echo $pageInfo['id'] ?>" class="<?php echo $pageInfo['class'] ?>">
      <?php echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $pageInfo['level']) ?>
      <?php if (isset($pageInfo['hasChildren'])): ?>
        <?php $id = $pageInfo['id'] ?>
        <?php echo link_to_function("&gt; ", "$('.childof-$id').show(); $('#$id-close').show(); $('#$id-open').hide();", array("id" => "$id-open")) ?>
        <?php echo link_to_function("&lt; ", "$('.descendantof-$id').hide(); $('#$id-close').hide(); $('#$id-open').show();", array("id" => "$id-close", 'style' => 'display: none')) ?>
      <?php else: ?>
        o 
      <?php endif ?>
      <?php echo $pageInfo['title'] ?>
    </li>
  <?php endforeach ?>
</ul>
<script>
$('#pagetree').sortable();
</script>