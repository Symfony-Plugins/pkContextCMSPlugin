<ul id="pk-tab-navigation" >
<?php // TBB: Let's stop pretending this makes sense in shorthand syntax. ?>
<?php // When there's more logic than HTML, it's time to write real PHP. ?>

<?php
$tabcount = 0;

foreach ($tabs as $tab)
{

	if ($tabcount == 0) {
		$tabclass = "pk-tab-nav-item first";
	}
	elseif ($tabcount == count($tabs)-1) {
		$tabclass = "pk-tab-nav-item last";
	} 
	else
	{
		$tabclass = "pk-tab-nav-item";
	}
	
  $id = $tab['id'];
  echo('<li id="pk-tab-nav-item-' . $id . '" ');
  $classes = '';
  if ($page)
  {
    if ($tab['level'] > 0)
    {
      if (pkContextCMSTools::pageIsDescendantOfInfo($page, $tab))
      {
        $classes .= "pk-current-page ";
      }
    } 
    if ($page->slug === $tab['slug'])
    {
      $classes .= "pk-current-page ";
    }
  }  
  if ($tab['archived'])
  {
    $classes .= "pk-archived-page ";
  }
  echo("class='$classes $tabclass'>");
  echo link_to(
    $tab['title'], 
    pkContextCMSTools::urlForPage($tab['slug']),
    array('target' => '_top'));
  echo("</li>\n");
	$tabcount++;

}
?>
</ul>
<?php if ($draggable): ?>


	<script type="text/javascript">
	//<![CDATA[
	$(document).ready(
	  function() 
	  {
	    $("#pk-tab-navigation").sortable(
	    { 
	      update: function(e, ui) 
	      { 
	        var serial = jQuery("#pk-tab-navigation").sortable('serialize', {});
	        var options = {"url":<?php echo json_encode(url_for('pkContextCMS/sortTabs').'?page=' . $page->getId()); ?>,"type":"POST"};
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