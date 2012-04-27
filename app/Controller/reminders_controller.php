<?php
	
	class RemindersController extends AppController{
		
		var $uses = array('Entry', 'Reminder');
		var $helpers = array('Html', 'Form');
		var $name = "Reminders";
		
		function index(){
		
		}
		
		function edit($id){
		
		}
		
		function add($entryId){
	
			$entry = $this->Entry->read($entryId);
			
			$b = $this->Reminder->save(array('Reminder'=>array('body'=>'Reminder for calendar date: '. $entry['Entry']['name'], 'entry_id' => $entryId)));
			$reminder = $this->Reminder->read();
			$json = '{"success":'.$b.', obj:'.json_encode($reminder).'}';
			echo $json;
			die();
			
		}
		
		function delete($id){
			
		}
		
		require("IronWorker.class.php");

		
		function test_uploadIronWorkerJob(){
			echo 'test_uploadIronWorkerJob:<br>';
				
			$name = "HelloWorker-php";

			$iw = new IronWorker('config.ini');

			# Creating zip package.
			$zipName = "$name.zip";
			IronWorker::createZip("", array('hello_worker.php'), $zipName, true);

			# Posting package.
			$res = $iw->postCode('hello_worker.php', $zipName, $name);

			# Pass any data you want to a worker task.
			$payload = array(
				'key_one' => 'Payload',
				'key_two' => 2
			);
			# Adding a new task.
			$task_id = $iw->postTask($name, $payload);
			echo "task_id = $task_id \n";

			sleep(10);

			$details = $iw->getTaskDetails($task_id);
			print_r($details);
			if ($details->status != 'queued'){
				$log = $iw->getLog($task_id);
				print_r($log);
			}


			# Scheduling task.

			# 3 minutes from now.
			$start_at = time()+3*60;

			# Run task every 2 minutes, repeat 10 times.
			$schedule_id = $iw->postScheduleAdvanced($name, $payload, $start_at, 2*60, null, 10);
			echo "schedule_id: $schedule_id\n";

		}
		
		
		function test_checkIronWorkerJob(){
						
			echo 'check:<br>';
			$opts = getopt("", array("task:"));

			$worker = new IronWorker(array(
			  'token' => 'TlTWqXFbGqU_UatTqlBtXk89BcA',
			  'project_id' => '4f9abbfdf0b19932d200b41a'
			));

			try{
				echo 'here';
				$details = $worker->getTaskDetails($opts["task"]);
				echo 'here';
				echo "Task is ".$details->status."\n";
			}catch(Exception $e){
				echo 'asfd';
				echo $e;
			}

		}
		
		
		
	
	}

?>

