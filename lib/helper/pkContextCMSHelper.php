<?php

// Loading of the pkContextCMS CSS, JavaScript and helpers is now triggered here 
// to ensure that there is a straightforward way to obtain all of the necessary
// components from any partial, even if it is invoked at the layout level (provided
// that the layout does use_helper('pkContextCMS'). 

function _pk_context_cms_required_assets()
{
  $response = sfContext::getInstance()->getResponse();

  sfContext::getInstance()->getConfiguration()->loadHelpers(
    array("Url", "jQuery", "I18N", 'PkDialog'));

  jq_add_plugins_by_name(array("ui"));

  if (sfConfig::get('app_pkContextCMS_use_bundled_stylesheet', true))
  {
    $response->addStylesheet('/pkToolkitPlugin/css/pkToolkit.css', 'first');
    $response->addStylesheet('/pkContextCMSPlugin/css/pkContextCMS.css', 'first');
  }

  $response->addJavascript('/pkToolkitPlugin/js/pkUI.js');
  $response->addJavascript('/pkToolkitPlugin/js/pkControls.js');
  // $response->addJavascript('/pkToolkitPlugin/js/jquery.hotkeys-0.7.9.min.js'); // this is plugin for hotkey toggle for cms UI // I turned this off because we aren't using it right now 1-8-2010 JB
  $response->addJavascript('/pkToolkitPlugin/js/jquery.autogrow.js'); // Autogrowing Textareas
  // $response->addJavascript('/pkToolkitPlugin/js/jquery.pulse.js'); // Ajax update highlight a color  // I turned this off because we aren't using it right now 1-8-2010 JB
	$response->addJavascript('/pkToolkitPlugin/js/jquery.keycodes-0.2.js'); // keycodes
	$response->addJavascript('/pkToolkitPlugin/js/jquery.timer-1.2.js');		
  $webDir = sfConfig::get('sf_pkContextCMS_web_dir', '/pkContextCMSPlugin');
  $response->addJavascript("$webDir/js/pkContextCMS.js");

}

_pk_context_cms_required_assets();

// Too many jquery problems
//sfContext::getInstance()->getResponse()->addJavascript(
// sfConfig::get('sf_pkContextCMS_web_dir', '/pkContextCMSPlugin') . 
// '/js/pkSubmitButton.js');
//<script type="text/javascript" charset="utf-8">
//pkSubmitButtonAll();
//</script>
function pk_context_cms_slot($name, $type, $options = false)
{
  $options = pk_context_cms_slot_get_options($options);
  $options['type'] = $type;
  pkContextCMSTools::globalSetup($options);
  include_component("pkContextCMS", "area", 
    array("name" => $name, "options" => $options)); 
  pkContextCMSTools::globalShutdown();
}

function pk_context_cms_area($name, $options = false)
{
  $options = pk_context_cms_slot_get_options($options);
  $options['infinite'] = true; 
  pkContextCMSTools::globalSetup($options);
  include_component("pkContextCMS", "area", 
    array("name" => $name, "options" => $options)); 
  pkContextCMSTools::globalShutdown();
}

function pk_context_cms_slot_get_options($options)
{
  if (!is_array($options))
  {
    if ($options === false)
    {
      $options = array();
    }
    else
    {
      $options = pkContextCMSTools::getSlotOptionsGroup($options);
    }
  }
  return $options;
}

function pk_context_cms_slot_body($name, $type, $permid, $options, $validationData, $editorOpen)
{
  $page = pkContextCMSTools::getCurrentPage();
  $slot = $page->getSlot($name);
  $parameters = array("options" => $options);
  $parameters['name'] = $name;
  $parameters['type'] = $type;
  $parameters['permid'] = $permid;
  $parameters['validationData'] = $validationData;
  $parameters['showEditor'] = $editorOpen;
  $user = sfContext::getInstance()->getUser();
  $controller = sfContext::getInstance()->getController();
  if ($controller->componentExists($type, "executeSlot"))
  {
    include_component($type, "slot", $parameters);
  }
  else
  {
    include_component("pkContextCMS", "slot", $parameters);
  }
}

function pk_context_cms_navtree($depth = null)
{
  $page = pkContextCMSTools::getCurrentPage();
  $children = $page->getTreeInfo(true, $depth);
  return pk_context_cms_navtree_body($children);
}

function pk_context_cms_navtree_body($children)
{
  $s = "<ul>\n";
  foreach ($children as $info)
  {
    $s .= '<li>' . link_to($info['title'], pkContextCMSTools::urlForPage($info['slug']));
    if (isset($info['children']))
    {
      $s .= pk_context_cms_navtree_body($info['children']);
    }
    $s .= "</li>\n";
  }
  $s .= "</ul>\n";
  return $s;
}

function pk_context_cms_navaccordion()
{
  $page = pkContextCMSTools::getCurrentPage();
  $children = $page->getAccordionInfo(true);
  return pk_context_cms_navtree_body($children);
}

// Keeping this functionality in a helper is very questionable.
// It should probably be a component.

// ... Sure enough, it's now called by a component in preparation to migrate
// the logic there as well.

function pk_context_cms_navcolumn()
{
  $page = pkContextCMSTools::getCurrentPage();
  return _pk_context_cms_navcolumn_body($page);
}

function _pk_context_cms_navcolumn_body($page)
{
  $sortHandle = "";
  $sf_user = sfContext::getInstance()->getUser();
  $admin = $page->userHasPrivilege('edit');
  if ($admin)
  {
    $sortHandle = "<div class='pk-btn icon pk-drag pk-controls'></div>";
  }
  $result = "";
  // Inclusion of archived pages should be a bit generous to allow for tricky situations
  // in which those who can edit a subpage might not be able to find it otherwise.
  // We don't want the performance hit of checking for the right to edit each archived
  // subpage, so just allow those with potential-editor privs to see that archived pages
  // exist, whether or not they are allowed to actually edit them
  if (pkContextCMSTools::isPotentialEditor() && 
    $sf_user->getAttribute('show-archived', true, 'pk-context-cms'))
  {
    $livingOnly = false;
  }
  else
  {
    $livingOnly = true;
  }
  $result = '<ul id="pk-navcolumn" class="pk-navcolumn">';
  $childrenInfo = $page->getChildrenInfo($livingOnly);
  if (!count($childrenInfo))
  {
    $childrenInfo = $page->getPeerInfo($livingOnly);
  }
  foreach ($childrenInfo as $childInfo)
  {
    $class = "peer_item";
    if ($childInfo['id'] == $page->id)
    {
      $class = "self_item";
    }
    // Specific format to please jQuery.sortable
    $result .= "<li id=\"pk-navcolumn_" . $childInfo['id'] . "\" class=\"$class\">\n";
    $title = $childInfo['title'];
    if ($childInfo['archived'])
    {
      $title = '<span class="pk-archived-page" title="&quot;'.$title.'&quot; is Unpublished">'.$title.'</span>';
    }
    $result .= $sortHandle.link_to($title, pkContextCMSTools::urlForPage($childInfo['slug']));
    $result .= "</li>\n";
  }
  $result .= "</ul>\n";
  if ($admin)
  {
    $result .= jq_sortable_element('#pk-navcolumn', array('url' => 'pkContextCMS/sort?page=' . $page->getId()));    
  }
  return $result;
}

