<?php

// echo "<b>" . $Entry['Entry']['name'] . "</b><br>";

// echo $Entry['Entry']['address'] . "<br>";


$address = $Entry['Entry']['address'];
$city = $Entry['Entry']['city'];
$state = $Entry['Entry']['state'];
$country = $Entry['Entry']['country'];

if(!empty($address)) echo $address . "<br>";


if(!empty($city)) echo $city;
if (!empty($city) && !empty($state) ) echo ", ";
if(!empty($state)) echo $state;
if (!empty($state) && !empty($country)) echo " ";
if(!empty($country)) echo $country;

if(!empty($city) || !empty($state) || !empty($country)) 
	echo "<br>";

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

echo "<br>";

$url = $Entry['Entry']['url'];
if(isset($url) && strlen($url)){
	echo "<div style='width:100; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;'>";
	echo $this->Html->link($url, 
							$url, 
							array(
							'target'=>'_blank', 
							'escape' => false));
	echo "</div>";
}

echo "<br>";



?>
<script type="text/javascript">
		function f(){
			$("#detailsDlg").dialog({title:"edit"});
			$.get("/calendar/entries/edit/" +  $(this).attr("entryId"), 
				"", function(param){
					$("#detailsDlg").html(param);
					$("#detailsDlg").width($("#detailsDlg").firstChild());
				});
			return false;
		}

		$("#detailsDlg").find("#closeBtn").click(function(){
			$("#detailsDlg").dialog('close'); 
			return false;
		});
</script>

<?php
// echo $this->Html->link('edit', array('controller' => 'entries', 'action'=>'edit', $Entry['Entry']['id']), array("id"=>"editLink", "onclick"=>"return f();")) . " | ";

echo $this->Html->link('close', array(), array('id'=>'closeBtn')) . " | ";
echo $this->Html->link('edit', array('controller' => 'entries', 'action'=>'edit', $Entry['Entry']['id'])) . " | ";
echo $this->Html->link('delete', 
						array('controller' => 'entries', 'action'=>'delete', $Entry['Entry']['id']),
						array(),
						"Calendar entry '" . $Entry['Entry']['name'] . "' will be deleted. Proceed?");

// echo $this->Html->link('close', array('controller' => 'entries', 'action'=>'index')) . " | ";

						
?>


