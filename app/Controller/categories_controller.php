<?php
	
	class CategoriesController extends AppController{
		
		var $helpers = array('Html', 'Form');
		var $uses = array('Entry', 'Category');
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
			$json = array();
			try{
				if(!empty($this->request->data)){
					$data = $this->request->data;
					$data['Category']['user_id'] = $this->Auth->user('id');
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
			
			$this->Category->delete($id);	
			$entries = $this->Entry->findAllByCategoryId($id);
			foreach($entries as $entry){
				$this->Entry->id = $entry['Entry']['id'];
				$this->Entry->saveField('category_id', NULL);
			}
			
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

