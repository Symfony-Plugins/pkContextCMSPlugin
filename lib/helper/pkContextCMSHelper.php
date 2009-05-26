<?php

use_helper("Form", "jQuery", "I18N");

sfContext::getInstance()->getResponse()->addJavascript(
  sfConfig::get('sf_pkContextCMS_web_dir', '/pkContextCMSPlugin') . 
  '/js/pkContextCMS.js');

sfContext::getInstance()->getResponse()->addJavascript(
  sfConfig::get('sf_pkContextCMS_web_dir', '/pkContextCMSPlugin') . 
  '/js/jquery.autogrow.js');

// Too many jquery problems
//sfContext::getInstance()->getResponse()->addJavascript(
// sfConfig::get('sf_pkContextCMS_web_dir', '/pkContextCMSPlugin') . 
// '/js/pkSubmitButton.js');
//<script>
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

function pk_context_cms_slot_body($name, $type, $permid, $options, 
  $validationData, $editorOpen)
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

// Keeping this functionality in a helper is very questionable.
// It should probably be a component.

// ... Sure enough, it's now called by a component in preparation to migrate
// the logic there as well.

function pk_context_cms_navcolumn()
{
  $page = pkContextCMSTools::getCurrentPage();
  return _pk_context_cms_navcolumn_body($page, true);
}

// Displays a two-dimensional table of links, children as headings,
// grandchildren below. Direct child pages are accessible
// only to the admin, who uses them to edit and order the 
// grandchildren. 

function pk_context_cms_navcolumn_grandchildren($page)
{
  return _pk_context_cms_navcolumn_body($page, true, true);
}

function _pk_context_cms_navcolumn_body(
  $page, $toplevel, $grandchildren = false, $editable = true)
{
  $sortHandle = "";
  $sf_user = sfContext::getInstance()->getUser();
  if (!$editable)
  {
    $admin = false;
  }
  else
  {
    $admin = $page->userHasPrivilege('edit');
  }
  $adminPrivileges = $page->userHasPrivilege('edit');
  if ($admin)
  {
    $sortHandle = "<div class='pk-context-cms-drag-handle'></div>";
  }
  $result = "";
  if ($adminPrivileges && 
    $sf_user->getAttribute('show-archived', false, 'pk-context-cms'))
  {
    $livingOnly = false;
  }
  else
  {
    $livingOnly = true;
  }
  if ($grandchildren)
  {
    // TODO: this could be a getDescendants(2) call instead of
    // nested calls to getChildren()
    $children = $page->getChildren($livingOnly);
    if ($children === false)
    {
      $children = array();
    }
    $last = count($children) - 1;
    $i = 0;
    foreach ($children as $child)
    {  
      $result .= "<ul id=\"column_" . basename($child->slug) . "\">\n";
      $result .= "<li>";
      $result .= "<h4>";
      if ($admin)
      {
        if ($i != 0)
        {
          $result .= link_to(
            "&laquo;",
            "pkContextCMS/move?id=" . $child->id .
              "&shift=-1");
          $result .= "&nbsp;";
        }
      }
      if ($admin)
      {
        $result .= link_to($child->getTitle(), $child->getUrl());
      }
      else
      {
        $result .= $child->getTitle();
      }
      if ($admin)
      {
        if ($i != $last)
        {
          $result .= "&nbsp;";
          $result .= link_to(
            "&raquo;",
            "pkContextCMS/move?id=". $child->id . 
              "&shift=1");
        }
      }
      $result .= "</h4></li>\n";
      if ($child->hasChildren(!$adminPrivileges))
      {
        $result .= _pk_context_cms_navcolumn_body($child, false, false, false);
      }
      $result .= "</ul>\n";
      $i++;
    }
    return $result;
  }
  if ($toplevel)
  {
    $result = '<ul id="pk-context-cms-navcolumn">';
  }
  if ($page->hasChildren($livingOnly))
  {
    $children = $page->getChildren($livingOnly);
  } 
  else
  {
    $parent = $page->getNode()->getParent();
    $children = array();
    if ($parent && $parent->hasChildren($livingOnly))
    {
      $children = $parent->getChildren($livingOnly);
    }
  }
  if ($children !== false)
  {
    foreach ($children as $child)
    {
      $class = "peer_item";
      if ($child->id == $page->id)
      {
        $class = "self_item";
      }
      // Specific format to please jQuery.sortable
      $result .= "<li id=\"pk-context-cms-navcolumn_" . $child->id . "\" class=\"$class\">\n";
      $title = $child->getTitle();
      if ($child->getArchived())
      {
        $title = "<span class='pk-context-cms-archived'>$title</span>";
      }
      if ($sortHandle !== false)
      {
        $title = $title;
      }
      $result .= $sortHandle.link_to($title, $child->getUrl());
      $result .= "</li>\n";
    }
  }
  if ($toplevel)
  {
    $result .= "</ul>\n";
  }
  if ($admin)
  {
    $result .= jq_sortable_element('#pk-context-cms-navcolumn', array('url' => 'pkContextCMS/sort?page=' . $page->getId()));    
  }
  return $result;
}

function pk_context_cms_alpha_select_navcolumn($page,
  $defaultText)
{
  $query = pkContextCMSPageTable::queryWithTitles();
  $treeObject = Doctrine::getTable('pkContextCMSPage')->getTree();
  $treeObject->setBaseQuery($query);
  if ($page->getNode()->hasChildren())
  {
    $children = $page->getNode()->getChildren();
  } 
  else
  {
    $parent = $page->getNode()->getParent();
    if (!$parent) 
    {
      $treeObject->resetBaseQuery();
      return;
    }
    $children = $parent->getChildren();
    $defaultText = $page->getTitle();
  }
  $options = array();
  foreach ($children as $child)
  {
    if ($child->id == $page->id)
    {
      continue;
    }
    $options[$child->slug] = $child->getTitle();
  }
  asort($options);
  $noptions = array("" => $defaultText); 
  $options = array_merge($noptions, $options);
  return select_tag(
    'pk-context-nav-selector',
    options_for_select($options, ""),
    array("onChange" => <<<EOM
value = $('pk-context-nav-selector').options
  [$('pk-context-nav-selector').selectedIndex].value;
if (value) 
{
  document.location = '/' + value;
}
EOM
));
}


