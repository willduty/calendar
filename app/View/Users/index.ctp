
<h3 style='color:#a60'><b>Admin</b></h3>
	
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



<h2>Cron:</h2>
<table style = "width:1000px;">
	<tr style="background-color:black; color:white;">
		<td style="background-color:black; ">name</td>
		<td style="background-color:black; ">id</td>
		<td style="background-color:black; ">status</td>
		<td style="background-color:black; ">details</td>
		<td style="background-color:black; ">log</td>
		<td style="background-color:black; ">created</td>
		<td style="background-color:black; ">delete</td>

	</tr>

<?php

foreach($ironWorkerTasks as $iwt){
	$task = $iwt['IronWorkerTask'];
	$id = $task['id'];
	$name = $task['name'];
	$task_id = $task['task_id'];
	$schedule_id = $task['schedule_id'];
	$details = $task['details'];
	$log = $task['log'];
	$created = $task['created'];
	
	echo "<tr>
		<td>$name</td>
		<td>$task_id</td>
		<td>$schedule_id</td>
		<td>$details</td>
		<td>$log</td>
		<td>$created</td>
		<td>".
			$this->Html->link('delete', array('controller'=>'iron_worker_tasks', 'action'=>'destroy',  $id))
		."</td>
		</tr>";
}

?>

</table>

<?php
	echo $this->Html->link('new cron &raquo;', array('controller'=>'iron_worker_tasks', 'action'=>'add', 'reminders_cron' ));
?>


<br><br>







<?php


echo $this->Html->link('calendar main page',
						array('controller' => 'entries',
								'action' => 'index'));
echo " | ";


echo $this->Html->link('logout',
						array('controller'=>'users',
						'action'=>'logout'));
?>

