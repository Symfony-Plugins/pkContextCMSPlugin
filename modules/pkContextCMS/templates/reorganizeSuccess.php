<?php use_helper('jQuery') ?>

<?php slot('body_class','pk-admin') ?>

<?php sfContext::getInstance()->getResponse()->addJavascript('/pkContextCMSPlugin/js/jsTree/_lib/css.js') ?>
<?php sfContext::getInstance()->getResponse()->addJavascript('/pkContextCMSPlugin/js/jsTree/source/tree_component.js') ?>
<?php sfContext::getInstance()->getResponse()->addStylesheet('/pkContextCMSPlugin/js/jsTree/source/tree_component.css') ?>

<?php slot('tabs') ?>
<?php end_slot() ?>

<div id="pk-page-tree-container">

	<h3>Drag and drop pages to reorganize the site.</h3>

	<div id="tree"></div>

</div>

<script type="text/javascript">
$(function() {
  $('#tree').tree({
    data: {
      type: 'json',
      <?php // Supports multiple roots so we have to specify a list ?>
      json: [ <?php echo json_encode($treeData) ?> ]
    },
		ui: {
			theme_path: "/pkContextCMSPlugin/js/jsTree/source/themes/",
      theme_name: "punk",
			context: false
		},
    rules: {
      // Turn off most operations as we're only here to reorg the tree.
      // Allowing renames and deletes here is an interesting thought but
      // there's back end stuff that must exist for that.
      renameable: false,
      deletable: false,
      creatable: false,
      draggable: 'all',
      dragrules: 'all'
    },
    callback: {
      // move completed (TYPE is BELOW|ABOVE|INSIDE)
      onmove: function(node, refNode, type, treeObj, rb)
      {
        <?php 
          // To avoid creating an inconsistent tree we need to use
          // a synchronous request. If the request fails, refresh the
          // tree page (TODO: find out if there's some way to flunk an 
          // individual drag operation). This shouldn't happen anyway
          // but don't get into an inconsistent state if it does!
        ?>
        <?php
          // TODO: activate a spinner here 
        ?>
        var nid = node.id;
        var rid = refNode.id;
        jQuery.ajax({
          url: <?php echo json_encode(url_for("pkContextCMS/treeMove") . "?") ?> +
            "id=" + nid.substr("tree-".length) + 
            "&refId=" + rid.substr("tree-".length) + "&type=" + type,
          error: function(result) {
            <?php // 404 errors etc ?>
            window.location.reload();
          },
          success: function(result) {
            <?php // Look for a specific "all is well" response ?>  
            if (result !== 'ok')
            {
              window.location.reload();
            }
            <?php 
              // TODO: turn off the spinner here
            ?>
          },
          async: false
        });
      }
    }  
  });
});
</script>
