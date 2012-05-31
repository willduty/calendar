<?php
	

	class RemindersController extends AppController{
		
		var $uses = array('Entry', 'Reminder', 'User');
		var $helpers = array('Html', 'Form');
		var $name = "Reminders";
		
		function beforeFilter(){
			$this->Auth->allow('cron');
		}
		
		
		function index(){
			echo 'ok';		
			die();
		}
		
		
		function cron($token){
			
			// if $token valid
			
			// find reminders 
			$reminders = $this->Reminder->find('all');
			
			foreach($reminders as $reminder){
				echo '------<br>';
				// get entry
				
				$reminder = $reminder['Reminder'];
				//debug($reminder);
				$hours_before = $reminder['hours_before'];
				if(!isset($hours_before)){
					echo 'bad reminder';
					// delete reminder
					
					$this->Reminder->delete($reminder['id']);
					continue;
				}
						
				$entry = $this->Entry->findById($reminder['entry_id']);
				
				foreach($entry['Date'] as $date){
						
					//debug($date);
					
					// repeating date
					if(isset($date['weeks_pattern'])){ 
					
						//echo 'is repeating';
						
						//echo $hours_before;
						
						// if time to send
							
							// send email		
					}
					else{  // onetime date
						echo '<br>onetime<br>';
						
						$now = new DateTime();
						
						$startDate = $date['start_date'];
						if(isset($date['start_time']))
							$startDate .= ' ' . $date['start_time'];
						echo $startDate . '<br>';
						
						$dateEvent = new DateTime($startDate);
						$interval = date_diff($dateEvent, $now);		
						
						
						$hours_left = ($interval->format('%a%') * 24) + 
								$interval->h;
						
						// if time to send
						if($hours_left < $hours_before){
							
							// send email
							
							$message = 'You have a calendar entry  "'.$entry['Entry']['name'].'" scheduled for '. 
									$dateEvent->format('Y-m-d h:i:s');
							$user = $this->User->findById($entry['Entry']['user_id']);
							$email = $user['User']['email'];
							
							echo '<br>'.$email .'<br>';
							
							mail($email,  
								'Calendar Reminder for: "'.$entry['Entry']['name'].'"', 
								$message);
			
							// todo if no repeating dates and this is latest date
							
							
							// delete reminder
							echo 'deleting reminder';
							$this->Reminder->delete($reminder['id']);
							continue;
						}
					}
				}
				echo '<br><br>';
							
						
			}
			
			//mail('willduty@yahoo.com', 'cron test', 'cron test');
			die();
		}
		
		function edit($id){
		
			die();
		}
		
		function add($entryId){
	
			$hours = $this->data['Reminder']['days'] * 24 + $this->data['Reminder']['hours'];
			
			if($hours == 0)
				$hours = 24;
				
			$entry = $this->Entry->read($entryId);
			$b = $this->Reminder->save(array(
							'Reminder'=>array(
								'body'=>'Reminder for calendar date: '. $entry['Entry']['name'], 
								'entry_id' => $entryId,
								'hours_before'=>$hours)
							));
			
			
			if($this->request->is('ajax')){
				$reminder = $this->Reminder->read();
				$json = '{"success":'.$b.', obj:'.json_encode($reminder).'}';
				echo $json;
				die();
			}else{
				$this->redirect('/entries');
			}
			
			
		}
		
		function delete($id){
			
			die();
		}
		
		
		
		function test_uploadIronWorkerJob(){
			require("IronWorker.class.php");

			echo 'test_uploadIronWorkerJob:<br>';
				
			
			echo 'here0 - <br>';	
			
			$workername = "HelloWorker-php";
		try{
			$iw = new IronWorker('config.ini');
			//$iw = new IronWorker(array('token' => 'TlTWqXFbGqU_UatTqlBtXk89BcA', 
				//						'project_id' => '4f9abbfdf0b19932d200b41a'));
			}catch(Exception $e){
					echo 'exception:'.$e; 
					die();}		
			
			echo 'here1<br>';
			
			# Creating zip package.
			$zipName = "$workername.zip";
			IronWorker::createZip("", array('hello_worker.php'), $zipName, true);

			# Posting package.
			$res = $iw->postCode('hello_worker.php', $zipName, $workername);

			echo 'here2<br>';
			echo $res;
			
			# Pass any data you want to a worker task.
			$payload = array(
				'key_one' => 'Payload',
				'key_two' => 2
			);
			# Adding a new task.
			$task_id = $iw->postTask($workername, $payload);
			echo "task_id = $task_id \n";

			sleep(10);

			
			echo 'here3';
			
			
			$details = $iw->getTaskDetails($task_id);
			echo print_r($details);
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

			die();
		}
		
		
		function test_checkIronWorkerJob(){
			require("IronWorker.class.php");

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


			die();
		}
		
		
		
	
	}

?>

