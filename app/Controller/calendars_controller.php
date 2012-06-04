<?php

class CalendarsController extends AppController{
	
	var $name = 'Calendars';
	
	
	// add a new calendar entry
	function add(){
		
		$data = $this->request->data;
		$data['Calendar']['user_id'] = $this->Auth->user('id');
		$data['Calendar']['is_default'] = 0;
		
		if($this->Calendar->save($data))
			$this->Session->setFlash('Calendar \''.$data['Calendar']['name'].'\' Added', 'flashElem');
		else
			$this->Session->setFlash('Add Calendar Failed', 'flashElem');
			
		
		$this->redirect(array('controller'=>'entries', 'action'=>'index'));	
	}
	
	
	
	function update($id){
		echo 'update';
		$data = $this->request->data;
		debug($data);
		die();
	}
	
	
	
	
	// delete a calendar entry
	function delete($id){
		echo 'delete';
		die();
	}
	
}
	
?>
