<?php

class DatesController extends AppController{
	var $helpers = array('Html', 'Js', 'Calendar');
	var $name = 'Dates';
	var $uses = array('Entry', 'Date', 'User');
	
	
	function index(){
		echo 'hello, nothing here...';
		die();
	}
	
	function add($entryId){
		
		// adjust date data and save
		$data = $this->request->data;
		$this->adjustDataArray($data);
		//echo debug($data);
		//die();
		
		
		$data['Date'][0]['entry_id'] = $entryId;
		if(!$this->Date->save($data['Date'][0])){
			echo '{"result":"failed"}';
			die();	
		}
		
		// return a json object, if success including the new date obj itself
		$this->set('date', $this->Date->read());
		
		if($this->RequestHandler->isAjax()){
			$this->render('viewajax', 'ajax');
		}
		
	}
	
	
	
	// delete a date associated with a calendar entry
	function delete($dateId, $entryId){
		
		$this->Date->id = $dateId;
		$b = $this->Date->delete($dateId, false);
		
		echo $b ? '{"result":"ok"}' : '{"result":"failed"}' ;	
		die();
	
	}
	
	

}


?>
