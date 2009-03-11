<?php if ($editable): ?>
  <?php // Normally we have an editor inline in the page, but in this ?>
  <?php // case we'd rather use the picker built into the media plugin. ?>
  <?php // So we link to the media picker and specify an 'after' URL that ?>
  <?php // points to our slot's edit action. Setting the ajax parameter ?>
  <?php // to false causes the edit action to redirect to the newly ?>
  <?php // updated page. ?>
  <?php echo link_to('Choose photos<span></span>',
    sfConfig::get('app_pkContextCMS_media_site', false) . "/media/select?" .
      http_build_query(
        array("multiple" => true,
        "pkMediaIds" => implode(",", $itemIds),
        "type" => "image",
        "after" => url_for("pkContextCMSSlideshow/edit?" . 
          http_build_query(
            array(
              "slot" => $name, 
              "slug" => $slug, 
              "permid" => $permid,
              "noajax" => 1)), true))),
    array('class' => 'pk-btn pk-context-media-choose')) ?>
<br class="c"/>
<?php endif ?>
<ul class="pk-context-media-show">
<?php $first = true ?>
<?php $n=0; foreach ($items as $item): ?>
  <?php $embed = str_replace(
    array("_WIDTH_", "_HEIGHT_", "_c-OR-s_", "_FORMAT_"),
    array($width, 
      $flexHeight ? (($width / $item->width) * $item->height) : $height, 
      $resizeType,
      $item->format),
    $item->embed) ?>
  <li class="pk-context-media-show-item shadow" id="pk-context-media-show-item-<?php echo $n ?>" style="height:<?php echo $height ?>"><?php echo $embed ?></li>
  <?php $first = false ?>
<?php $n++; endforeach ?>
</ul>
<script>
$(function() {
  $('.pk-context-media-show li').click(function() {
    $(this).hide();
    var next = $(this).next();
    if (!next.length)
    {
      next = $($(this).parent()).children(":first");
    }
    next.fadeIn();
  });
});
</script>