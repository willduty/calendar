<?php 

// send back comma delimited string of ids that match search
$arr = array();
foreach($entries as $entry){
	array_push($arr, $entry['Entry']['id']);
} 
echo implode(",", $arr);

?>
