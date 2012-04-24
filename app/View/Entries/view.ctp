<?php

echo "<b>" . $Entry['Entry']['name'] . "</b><br>";

echo $Entry['Entry']['address'] . "<br>";


$date = new DateTime();
$startTime = $Entry['Date'][0]['start_time'];
if(!empty($startTime)){
	$t = explode(":", $startTime);
	$date->setTime($t[0], $t[1], $t[2]);
	echo $date->format("g:i A");
}
else
	echo "[Notice: A start time was not defined...]";

	
echo " - ";	
	
$endTime = $Entry['Date'][0]['end_time'];
if(empty($endTime)){
	echo " whenever";
} else {
	$t = explode(":", $endTime);
	$date->setTime($t[0], $t[1], $t[2]);
	echo $date->format("g:i A");
}


echo "<br><br>";

echo $this->Html->link('back to calendar', array('controller' => 'entries', 'action'=>'index')) . " | ";
echo $this->Html->link('edit', array('controller' => 'entries', 'action'=>'edit', $Entry['Entry']['id'])) . " | ";
echo $this->Html->link('delete', 
						array('controller' => 'entries', 'action'=>'delete', $Entry['Entry']['id']),
						array(),
						"Calendar entry '" . $Entry['Entry']['name'] . "' will be deleted. Proceed?");

?>


