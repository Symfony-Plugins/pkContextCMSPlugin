<ul id="pk-context-cms-site-navigation" >
<?php // TBB: Let's stop pretending this makes sense in shorthand syntax. ?>
<?php // When there's more logic than HTML, it's time to write real PHP. ?>

<?php
$tabcount = 0;

foreach ($tabs as $tab)
{

	if ($tabcount == 0) {
		$tabclass = "first";
	}
	elseif ($tabcount == count($tabs)-1) {
		$tabclass = "last";
	} 
	else
	{
		$tabclass = "";
	}
	
  if (is_array($tab))
  {
    // Foreign tab implemented by a non-CMS page
    echo("<li");
    echo (fnmatch(isset($tab['pattern']) ? 
       $tab['pattern'] : $tab['url'], 
      $sf_params->get('module') . '/' .
    $sf_params->get('action')) ? "class='current'" : "") ;
    echo link_to(
      $tab['name'], $tab['url'], 
      array('class' => 'pk-context-cms-page-navigation'));
    echo("</li>");
  }
  else
  {
    echo("<li id='nav-$tabcount'");
    $classes = '';
    if ($page)
    {
      if (!($tab->getNode()->isRoot()))
      {
        if ($page->getNode()->isDescendantOf($tab))
        {
          $classes .= "pk-context-cms-current-page ";
        }
      } 
      sfContext::getInstance()->getLogger()->info("CHECKING");
      if ($page->isEqualTo($tab))
      {
        sfContext::getInstance()->getLogger()->info("EQUAL");
        $classes .= "pk-context-cms-current-page ";
      }
    }  
    if ($tab->getArchived())
    {
      $classes .= "pk-context-cms-archived ";
    }
    echo("class='$classes $tabclass'>");
    echo link_to(
      $tab->__toString(), 
      $tab->getUrl(),
      array('class' => 'pk-context-cms-page-navigation', 
        'target' => '_top'));
    echo("</li>\n");
  }

	$tabcount++;

}
?>
</ul>
