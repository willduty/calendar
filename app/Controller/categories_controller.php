<?php
	
	class CategoriesController extends AppController{
		
		var $helpers = array('Html', 'Form');
		var $name = "Categories";
		
		function index(){
			$this->set('categories', $this->Category->find('all'));
		
		}
		
		function edit($id){
			$this->Category->id = $id;
			
			if(empty($this->data))
				$this->data = $this->Category->read();
			else{
				$this->Category->save($this->data);
				$this->Session->setFlash('changes saved');
				$this->redirect(array('action' => 'index'));
			}		
		}
		
		function add(){
			
			if(!empty($this->request->data)){
				if($this->RequestHandler->isAjax()){
					$success = $this->Category->save($this->request->data);
					$json = array();
					$json['message'] = 'New Category Saved';
					$json['success'] = $success;
					echo json_encode($json);
					die();
				}
				else {
					if($this->Category->save($this->request->data))
						$this->Session->setFlash("new category added");
					
					$this->redirect(array('action' => 'index'));
				}
			}
		}
		
		
		function delete($id){
		
		
			$this->Category->delete($id);
		
			$arr = explode('/', CakeRequest::referer());
			
			foreach($arr as $key => $urlpart){
				if($this->base == '/'.$urlpart){
					if($arr[$key+1] != 'categories')
						$this->redirect(CakeRequest::referer());
					else	
						$this->redirect(array('action' => 'index'));
				}
			}
			
		}
	
	}



?>

