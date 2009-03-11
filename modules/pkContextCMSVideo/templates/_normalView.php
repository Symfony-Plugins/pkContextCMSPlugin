<?php if ($editable): ?>
  <?php // Normally we have an editor inline in the page, but in this ?>
  <?php // case we'd rather use the picker built into the media plugin. ?>
  <?php // So we link to the media picker and specify an 'after' URL that ?>
  <?php // points to our slot's edit action. Setting the ajax parameter ?>
  <?php // to false causes the edit action to redirect to the newly ?>
  <?php // updated page. ?>
  <?php echo link_to('Choose video<span></span>',
    sfConfig::get('app_pkContextCMS_media_site', false) . "/media/select?" .
      http_build_query(
        array(
        "pkMediaId" => $itemId,
        "type" => "video",
        "after" => url_for("pkContextCMSVideo/edit?" . 
          http_build_query(
            array(
              "slot" => $name, 
              "slug" => $slug, 
              "permid" => $permid,
              "noajax" => 1)), true))),
    array('class' => 'pk-btn pk-context-media-choose')) ?>
<?php endif ?>
<?php if ($item): ?>
  <div class="pk-context-media-video">
  <?php $embed = str_replace(
    array("_WIDTH_", "_HEIGHT_", "_c-OR-s_", "_FORMAT_"),
    array($width, 
      $flexHeight ? (($width / $item->width) * $item->height) : $height, 
      $resizeType,
      $item->format),
    $item->embed) ?>
  <?php echo $embed ?>
  </div>
<?php endif ?>
