<?php


	echo '{"result":"ok", 
		"obj":'.json_encode($date).', 
		"dateAsString":"'.$this->Calendar->dateAsString($date['Date']).'"}';
	die();


?>
