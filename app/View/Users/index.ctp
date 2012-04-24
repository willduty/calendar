
<h2>Users:</h2>
<table style = "width:600px;">
	<tr style="background-color:black; color:white;">
		<td style="background-color:black; ">username</td>
		<td style="background-color:black; ">email</td>
		<td style="background-color:black; ">about</td>
		<td style="background-color:black; ">edit</td>
	</tr>

<?php

foreach($users as $user){
	$username = $user['User']['username'];
	
	$about = $user['User']['about'];
	$email = $user['User']['email'];
	$editLink = $this->Html->link("edit", 
		array('action' => 'edit', $user['User']['id']));
	
	echo "<tr>
		<td>$username</td>
		<td>$email</td>
		<td>$about</td>
		<td>$editLink</td>
		</tr>";
}

?>

</table>

<br><br>

<?php

echo $this->Html->link('new user',
						array('action' => 'add'));
echo " | ";

echo $this->Html->link('calendar main page',
						array('controller' => 'entries',
								'action' => 'index'));
echo " | ";


echo $this->Html->link('logout',
						array('controller'=>'users',
						'action'=>'logout'));
?>

