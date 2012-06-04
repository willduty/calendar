<?php
	
	class CategoriesController extends AppController{
		
		var $helpers = array('Html', 'Form');
		var $uses = array('Entry', 'Category');
		var $name = "Categories";
		
		function index(){
			$this->set('categories', $this->Category->find('all'));
		
		}
		
		function update($id){
			$this->Category->id = $id;
			
			if(empty($this->data))
				$this->data = $this->Category->read();
			else{
				$this->Category->save($this->data);
				$this->Session->setFlash('changes saved', 'flashElem');
				$this->redirect(array('controller'=>'entries', 'action' => 'index'));
			}		
		}
		
		function add(){
			$json = array();
			try{
				if(!empty($this->request->data)){
					$data = $this->request->data;
					$data['Category']['calendar_id'] = CakeSession::read('UserValues.calendarId');
					if($this->RequestHandler->isAjax()){
						$success = $this->Category->save($data);
						$json['success'] = $success;
						$json['message'] = $success ? 'New Category Saved' : 'Save Failed';
						echo json_encode($json);
						die();
					}
					else {
						if($this->Category->save($this->request->data))
							$this->Session->setFlash("new category added");
						
						$this->redirect(array('action' => 'index'));
					}
				}
			}catch(Exception $e){
				$json['success'] = false;
				$json['message'] = $e->message; // maybe not in production..
				echo json_encode($json);
				die();
			}
		}
		
		
		function delete($id){
			
			$arr = explode('/', CakeRequest::referer());
			
			// Delete the category
			$this->Category->delete($id);	
			
			// Remove the category from calendar entries that belong to it
			$entries = $this->Entry->findAllByCategoryId($id);
			foreach($entries as $entry){
				$this->Entry->id = $entry['Entry']['id'];
				$this->Entry->saveField('category_id', NULL);
			}
			
			$this->Session->setFlash('category removed', 'flashElem');
			
			if($arr[3] != 'categories')
				$this->redirect(CakeRequest::referer());
			else	
				$this->redirect(array('action' => 'index'));
	
			$this->redirect(array('action' => 'index'));
			
		}
	
	}



?>

