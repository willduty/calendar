
<h2>Categories</h2>

<table style="width:200px;">

<?php

foreach($categories as $category){
	echo "<tr>";
	
	echo "<td>" . $category["Category"]["name"] . "</td>";
	echo "<td>" . $this->Html->link('edit', 
									array('controller' => 'categories', 'action' => 'edit', $category['Category']['id'])) 
									. "</td>";
	echo "<td>" . $this->Html->link('delete', 
									array('controller' => 'categories', 'action' => 'delete', $category['Category']['id']),
									array(),
									"proceed with delete?") 
									. "</td>";
	
	echo "</tr>";
}

?>

</table>

<br><br>

<?php


echo $this->Html->link('add category',
						array('controller' => 'categories',
								'action' => 'add'));
echo " | ";



echo $this->Html->link('back to calendar',
						array('controller' => 'entries',
								'action' => 'index'));
echo " | ";


echo $this->Html->link('logout',
						array('controller'=>'users',
						'action'=>'logout'));


?>

