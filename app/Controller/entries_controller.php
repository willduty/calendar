<?php

class EntriesController extends AppController{
	var $helpers = array('Html', 'Js', 'Calendar');
	var $name = 'Entries';
	var $uses = array('Entry', 'Date', 'User');
	
	
	// main calendar page
	function index($view = null, $year = null, $month = null, $day = null, $search = null){	
	
	
	
	
		$to      = $this->request->data['User']['email'];
				$subject = 'el calendario registration';
				$message = 'Hello,	
							To complete your registration, simply click the link below:\n\n
							<a href="localhost/users/confirm_registration/5" target=_blank>confirm link</a>
							\n\nThank you for registering. -Webmaster, El Calendario';
							
				$headers = 'From: webmaster@cakecalendar.com' . "\r\n" .
					'Reply-To: webmaster@cakecalendar.com' . "\r\n" .
					'MIME-version: 1.0\n'.
					'Content-type: text/html; charset= iso-8859-1\n';
					
				mail($to, $subject, $message, $headers);

	
	
	
	
	
	
	
	
		// if set explicitly in url, write calendar view type to session
		if($view){
			CakeSession::write('UserValues.view', $view);
		}
		elseif(CakeSession::read('UserValues.view'))
			$view = CakeSession::read('UserValues.view');
		else 
			$view = "month"; // default is month view
			
		$this->set('view', $view);
		
		
		$today = new DateTime();
		if($month == null)
			$month = $today->format('n');
			
		if($year == null)
			$year = $today->format('Y');
		
		if($day == null)
			$day = 1;
		$day = intval($day);
			
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
			
			
		$userId = $this->Auth->user('id');
				
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
			$this->set('category', $categoryId );
			$this->Entry->recursive = 2;
			// $this->set('entries', $this->Entry->findAllByCategoryId($this->params['named']['categoryId']));
			
			$this->set('entries', $this->Entry->find('all',
										array('conditions'=>
											array('Entry.user_id'=>$userId,
												'category_id' => $categoryId 
												))));
			
		}
		else {
			$this->Entry->recursive = 2;
			$this->set('entries', $this->Entry->findAllByUserId($userId));
		}

		if(isset($search)){
			$this->set('search', true);
		}
		
		// to list categories for user to filter calendar
		$this->loadModel('Category');
		$cats = $this->Entry->Category->find('list', array('conditions'=>array('user_id'=>$userId)));
		$this->set('categories', $cats);
		$this->set('userId', $this->Auth->user('id'));
		
	}
	
	
	// add a new calendar entry
	function add($year = null, $month = null, $day = null){
	
		$this->loadModel('DateType');
		$this->set('dateTypes', $this->DateType->find('list'));
		
		$data = $this->request->data;
		
		if(!empty($this->data)){ // form has been submitted

			$this->adjustDataArray($data);
			$data['Entry']['user_id'] = $this->Auth->user('id');
			
			if($this->Entry->saveAll($data)){
			
				$this->Session->setFlash("New Calendar Entry Saved", 'flashElem');
				$this->redirect(array('action' => 'index'));
			}
		}
		else{
			$this->set('year', $year);
			$this->set('month', $month);
			$this->set('day', $day);
			if(isset($year) &&isset($month) &&isset($day))
				$this->set('date', new DateTime($year .'-'. $month .'-'.$day));
				
			
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
		
		$this->set('categories', $this->Entry->Category->find('list', array('conditions'=>array('user_id'=>$this->Auth->user('id')))));
	
		$this->loadModel('DateType');
		$this->set('dateTypes', $this->DateType->find('list'));
		$this->Entry->recursive = 2;
		$this->data = $this->Entry->read();
		
	}
	
	
	function updateEntryDetails($id){
		$this->Entry->id = $id;
		$this->Entry->recursive = 0;
		$b = $this->Entry->save($this->request->data);
		
		echo '{"result":'. ($b ? '"ok"' : '"failed"') .'}';
		
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
	
	
	// delete a calendar entry
	function delete($id){
		$this->Entry->id = $id;
		$data = $this->Entry->read();
		if($this->Entry->delete($id, true))
			$this->Session->setFlash('Entry ' . $data['entry']['address'] . ' Deleted.', 'flashElem');
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
