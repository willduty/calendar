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
	
	}

?>

