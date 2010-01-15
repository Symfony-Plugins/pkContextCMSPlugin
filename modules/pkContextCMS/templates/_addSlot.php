<?php
$slotTypesInfo = pkContextCMSTools::getSlotTypesInfo($options);

foreach ($slotTypesInfo as $type => $info) {
  $label = $info['label'];
  $class = $info['class'];
	$link = jq_link_to_remote($label, array(
		"url" => "pkContextCMS/addSlot?" . http_build_query(array('name' => $name, 'id' => $id, 'type' => $type, )),
		"update" => "pk-slots-$name",
		'script' => true,
		'complete' => 'pkUI("#pk-area-'.$name.'","add-slot");', 
		), 
		array(
			'class' => 'pk-btn icon ' . $class .' slot', 
	));

	echo "<li>".$link."</li>";
}
?>