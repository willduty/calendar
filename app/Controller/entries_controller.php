<?php

class EntriesController extends AppController{
	var $helpers = array('Html', 'Js', 'Calendar');
	var $name = 'Entries';
	var $uses = array('Entry', 'Date', 'User', 'Calendar');
	
	
	// main calendar page
	function index($view = null, $year = null, $month = null, $day = null, $search = null, $calendarId = null){	
	
		
		$userId = $this->Auth->user('id');
	
		// if set explicitly in url, write calendar view type to session
		if($view)
			CakeSession::write('UserValues.view', $view);
		elseif(CakeSession::read('UserValues.view'))
			$view = CakeSession::read('UserValues.view');
		else 
			$view = "month"; // default is month view
			
		$this->set('view', $view);



		// if set explicitly in url, write calendarId to session
		$calendarId = @$this->params['named']['calendarId'];
		if(isset($calendarId)){
			CakeSession::write('UserValues.calendarId', $calendarId);
		}
		elseif(CakeSession::read('UserValues.calendarId')){
			$calendarId = CakeSession::read('UserValues.calendarId');	
		}
		else{
			$this->Calendar->recursive = 0;
			$calendar = $this->Calendar->find('first',
					array('conditions'=>array('Calendar.is_default'=>1, 
								'Calendar.user_id'=>$userId))
					);
			$calendarId = $calendar['Calendar']['id']; 
			CakeSession::write('UserValues.calendarId', $calendarId);
		}


		$today = new DateTime();
		
		if($month == null){
			$month = CakeSession::read('UserValues.month');
			$month = isset($month) ? $month : $today->format('n');
		}
		CakeSession::write('UserValues.month', $month);
		
		if($day == null){
			$day = CakeSession::read('UserValues.day');
			$day = isset($day) ? $day : $today->format('j');
		}
		CakeSession::write('UserValues.day', $day);
		
		if($year == null){
			$year = CakeSession::read('UserValues.year');
			$year = isset($year) ? $year : $today->format('Y');
		}
		CakeSession::write('UserValues.year', $year);
		
		$today->setDate($year, $month, $day);

		$this->set('today', $today);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->set('day', $day);
	
		try {
			$this->Entry->user_id = $this->Auth->user('id');
		} catch (Exception $e) {
			echo 'Caught exception: '.  $e->getMessage(). "\n";
		}
			
			
				
		// set category in user session if set in url
		// or unset if url param is set to zero
		if(isset($this->params['named']['categoryId'])){
			$val = $this->params['named']['categoryId'];
			($val == 0) ?
				CakeSession::delete('UserValues.categoryId') :
				CakeSession::write('UserValues.categoryId', $val);
		}
		
		
			
		// if category is set filter by category, else get all
		$categoryId = CakeSession::read('UserValues.categoryId');	
		
		if(isset($categoryId)){		
			$this->set('category', $this->Entry->Category->findById($categoryId));
			$this->Entry->recursive = 2;
			$this->set('entries', $this->Entry->find('all',
								array('conditions'=>
									array('Entry.calendar_id'=>$calendarId,
										'category_id' => $categoryId 
										))));
			
		}
		else {
			$this->Entry->recursive = 2;
			$this->set('entries', $this->Entry->findAllByCalendarId($calendarId));
		}

		if(isset($search)){
			$this->set('search', true);
		}
		
		// to list categories for user to filter calendar
		$this->loadModel('Category');
		$cats = $this->Entry->Category->find('all', array('conditions'=>array('Category.calendar_id'=>$calendarId)));
		$this->set('categories', $cats);
		$this->set('userId', $this->Auth->user('id'));
		
		
		// calendars and current calendar
		$this->Calendar->recursive = 0;	
		$this->set('calendars', $this->Calendar->findAllByUserId($userId));
		$this->set('calendarId', $calendarId);
		
	}
	
	
	// add a new calendar entry
	function add($year = null, $month = null, $day = null, $hour = null, $min = null, $meridian = null ){
	
		$data = $this->request->data;
		
		if(!empty($this->data)){ // form has been submitted

			$this->adjustDataArray($data);
			
			$data['Entry']['calendar_id'] = CakeSession::read('UserValues.calendarId');
		
			if($this->Entry->saveAll($data)){
			
				$this->Session->setFlash('Entry "'.$data['Entry']['name'].'" Saved', 'flashElem');
				$this->redirect(array('action' => 'index'));
			}
		}
		else{
			$this->set('year', $year);
			$this->set('month', $month);
			$this->set('day', $day);
			if(isset($year) &&isset($month) &&isset($day))
				$this->set('date', new DateTime($year .'-'. $month .'-'.$day));
				
			if(isset($hour) && isset($min) && isset($meridian))
				$this->set('hour', $hour.':'.$min.' '. $meridian);
				
			
			// to list categories 
			$cats = $this->Entry->Category->find('list', array('conditions'=>array('user_id'=>$this->Auth->user('id'))));
			$this->set('categories', $cats);
		}
		
	}
	
	
	// edit a calendar entry
	function edit($id){
		
		$this->Entry->id = $id;
		
		$data = $this->request->data;
		
		if(!empty($data)){	
			$this->adjustDataArray($data);			
		
			if($this->Entry->saveAll($data)){
			
				$this->Session->setFlash('Entry \''.$this->Entry->name.'\' Updated', 'flashElem');
				$this->redirect(array('action' => 'index'));
			}	
		}
		
		$this->set('categories', $this->Entry->Category->find('list', 
						array('conditions'=>array('Category.calendar_id'=>CakeSession::read('UserValues.calendarId'))
						)));
	
		$this->Entry->recursive = 2;
		$this->data = $this->Entry->read();
		
	}
	
	
	function updateEntryDetails($id){
		$this->Entry->id = $id;
		$this->Entry->recursive = 0;
		$b = $this->Entry->save($this->request->data);
		echo '{"success":'. ($b ? 'true' : 'false') .'}';
		die();
	}
	
	
	// view details of a specific calendar entry
	function view($id){
		$this->Entry->id = $id;
		$this->set('Entry', $this->Entry->read());
		if($this->RequestHandler->isAjax()){
			$this->render('viewajax', 'ajax');
		}
	}
	
	
	function getCalendar($view, $y, $m, $d){
		try{
		$this->Entry->recursive = 2;
		
		$this->set('entries', $this->Entry->findAllByCalendarId(CakeSession::read('UserValues.calendarId')));		
		$this->set('year', $y);		
		$this->set('month', $m);		
		$this->set('day', $d);			
		$this->set('view', $view);			
		$this->set("today", new DateTime($y .'-'. $m .'-'.$d));
		
		echo $this->render('/Elements/calendar_day_view_dlg');
		}catch(Exception $e){
			echo $e;
		}
		
		die();
	}
	
	
	// delete a calendar entry
	function delete($id){
		$this->Entry->id = $id;
		$data = $this->Entry->read();
		if($this->Entry->delete($id, true))
			$this->Session->setFlash('Entry "' . $data['Entry']['name'] . '" Deleted.', 'flashElem');
		else
			$this->Session->setFlash('Delete Failed.');
		
		$this->redirect(array('action' => 'index'));
	}
	
	
	
	// search for entries (via ajax)
	function search(){
	
		$arr = array();
		
		$searchTermsArr = explode(' ', $_REQUEST['searchTerms']);
		foreach($searchTermsArr as $term){
			array_push($arr, array('Entry.name LIKE ' => "%$term%"));
		}
		
		$conditions = array('OR' => $arr);
		
		$this->set('entries', $this->Entry->find('all', array('conditions' => $conditions)));
		$this->render('search', 'ajax');
	}
	
	
	
	// search for entries (via ajax)
	function entriesByCategory($catId){
	
		$entries = $this->Entry->findAllByCategoryId($catId);
		$entries = array('entries'=>$entries);
		echo json_encode($entries);
		
		die();
		
	}
	
	

	function highlightDay($view, $year, $month, $day){
		$this->redirect(array('action'=>'index', $view, $year, $month));
	}


}

function debug_showQueries(){
	$log = $this->Entry->getDataSource()->getLog(false, false);
	debug($log);
	die();
}



?>
