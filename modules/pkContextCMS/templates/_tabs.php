<table id="pk-context-cms-site-navigation" cellspacing="0">
<tr>
<?php // TBB: Let's stop pretending this makes sense in shorthand syntax. ?>
<?php // When there's more logic than HTML, it's time to write real PHP. ?>

<?php
$tabcount = 0;
if (count($tabs))
{
  $width = ((960/count($tabs))/960)*100;
}
else
{
  $width = 960;
}

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
    echo("<th style='width:".$width."%;'");
    echo (fnmatch(isset($tab['pattern']) ? 
       $tab['pattern'] : $tab['url'], 
      $sf_params->get('module') . '/' .
    $sf_params->get('action')) ? "class='current'" : "") ;
    echo link_to(
      $tab['name'], $tab['url'], 
      array('class' => 'pk-context-cms-page-navigation'));
    echo("</th>");
  }
  else
  {
    echo("<th id='nav-$tabcount' style='width:".$width."%;'");
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
    echo("</th>\n");
  }
  $tabcount++;
}
?>
</tr>
</table>
