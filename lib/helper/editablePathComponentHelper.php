<!-- OVERRIDING EPC -->
<?php

/* JB Note: Overriding helper from pkContextCMSPlugin */
/* JB Note: Overriding helper from pkContextCMSPlugin */
/* JB Note: Overriding helper from pkContextCMSPlugin */
/* JB Note: Overriding helper from pkContextCMSPlugin */

use_helper('jQuery', 'Form');

global $epcn;

function _next_epcn()
{
  if (!isset($epcn)) { $epcn = 0; }
  $epcn++;
  return "epc-$epcn";
}

function editable_path_component(
	$value, 
	$renameAction, 
  $params = false, 
	$edit = false, 
	$classStem = "epc")
{
  $epcn = _next_epcn();
  
	if ($params === false)
  {
    $params = array();
  }
  
	$result = "";
  
	if ($edit)
  {

    $result .= jq_link_to_function($value, 
							 "$('#$epcn-rename-form').fadeIn(250, function(){ $('#$epcn-rename-form .epc-value').focus(); }); 
							  $('#$epcn-rename-button').hide(); 
							  $('#pk-breadcrumb-title-rename').addClass('editing');
								$('.epc-rename-button-controls .pk-cancel').parent().show();", 
				        array(
									"id" => "$epcn-rename-button", 
									"class" => "$classStem-rename-button",
								));
								
    $result .= form_tag($renameAction, 
      array(
				"id" => "$epcn-rename-form", 
        'class' => "$classStem-form pk-breadcrumb-form",	
			)); 
			
    foreach ($params as $key => $val)
    {
      $result .= input_hidden_tag($key, $val);
    }

    $result .= input_tag("title", html_entity_decode(strip_tags($value)), array("class" => "$classStem-value pk-breadcrumb-input"));

    $result .= '<ul class="pk-form-controls epc-rename-button-controls"><li>'.submit_tag("Rename", array("class" => "pk-submit")).'</li>';

    $result .= '<li>'.link_to_function("cancel",
								'$("#'.$epcn.'-rename-form").hide(); 
								 $("#pk-breadcrumb-title-rename").removeClass("editing"); 
								 $("#'.$epcn.'-rename-button").fadeIn();', 
								 array(
									'class' => 'pk-btn icon pk-cancel', 
								 )).'</li>';

    $result .= "</ul></form>";
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
  $result .= link_to_function("cancelllllllllll", 
    jq_visual_effect("fadeOut", "#$epcn-action-form") . 
    jq_visual_effect("fadeIn", "#$epcn-action-initial"), array('class' => 'pk-btn icon cancel', )) .
    "</span></span>";
  return $result;
}

