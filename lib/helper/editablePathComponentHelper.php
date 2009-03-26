<?php

use_helper('jQuery');

global $epcn;

function _next_epcn()
{
  if (!isset($epcn))
  {
    $epcn = 0;
  }
  $epcn++;
  return "epc-$epcn";
}

function editable_path_component($value, $renameAction, 
  $params = false, $edit = false, $classStem = "epc")
{
  $epcn = _next_epcn();
  if ($params === false)
  {
    $params = array();
  }
  $result = "";
  if ($edit)
  {
    sfContext::getInstance()->getLogger()->info("XXX Value is $value");
    $result .= link_to_function(
      $value, jq_visual_effect("fadeIn", "#$epcn-rename-form") . 
        "$('.pk-context-cms-rename').addClass('editing');", 
        array("id" => "$epcn-rename-button", "class" => "$classStem-rename-button"));
    $result .= form_tag($renameAction, 
      array("id" => "$epcn-rename-form", "style" => "display: none",
        'class' => "$classStem-form")); 
    foreach ($params as $key => $val)
    {
      $result .= input_hidden_tag($key, $val);
    }
    $result .= input_tag("title", html_entity_decode(strip_tags($value)), array("class" => "$classStem-value"));
    $result .= '<div class="epc-rename-button-controls">'.submit_tag("Rename", array("class" => "submit"));
    $result .= "<span class='or'>or</span>";
    $result .= link_to_function("<span class='cancel'>cancel</span>", '$("#'.$epcn.'-rename-form").fadeOut(250, function (){ $(".pk-context-cms-rename").removeClass("editing"); });'.jq_visual_effect("fadeIn", "#$epcn-rename-button"), array('class' => 'epc-form-cancel', ));
    $result .= "</div></form>";


  }
  else
  {
    // TBB: already HTML in pkContextCMS
    $result .= $value;
  }
  return $result;
}

function actionable_path_component($label, $action, $params = false, $classStem = "apc")
{
  if ($params === false)
  {
    $params = array(
      "labelConfirm" => $label
    );
  }
  $epcn = _next_epcn();
  $result = "<span id='$epcn-action-initial' class='$classStem-action'>" . 
    link_to_function($label,
      jq_visual_effect("fadeIn", "#$epcn-action-form") . jq_visual_effect("fadeOut", "#$epcn-action-initial"));
  $result .= "</span>";
  $result .= "<span class='$classStem-action-form' id='$epcn-action-form' style='display: none'>";
  $result .= "Really? ";
  $labelConfirm = $params['labelConfirm'];
  unset($params['labelConfirm']);
  $result .= button_to($labelConfirm, $action, array("query_string" => http_build_query($params)));
  $result .= "<span class='$classStem-action-cancel' id='$epcn-action-cancel'>";
  $result .= "<span class="or">or</span>" . link_to_function("cancel", 
    jq_visual_effect("fadeOut", "#$epcn-action-form") . 
    jq_visual_effect("fadeIn", "#$epcn-action-initial")) .
    "</span></span>";
  return $result;
}

