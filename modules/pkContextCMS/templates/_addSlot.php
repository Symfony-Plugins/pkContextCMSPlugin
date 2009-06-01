<?php
$slotoptions = pkContextCMSTools::getSlotTypeOptions($options);

foreach ($slotoptions as $option => $label) {

	$link = jq_link_to_remote($label, array(
		"url" => "pkContextCMS/addSlot?" . http_build_query(array('name' => $name, 'id' => $id, 'type' => $option, )),
		"update" => "pk-slots-$name",
		'script' => true,
		'complete' => 'init_pk_controls();', 
		), 
		array(
			'class' => 'pk-btn icon '.str_replace('pkcontextcms','pk-', strtolower($option)).' slot', 
	));

	echo "<li>".$link."</li>";
}
?>