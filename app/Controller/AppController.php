<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * This is a placeholder class.
 * Create the same file in app/Controller/AppController.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       Cake.Controller
 * @link http://book.cakephp.org/view/957/The-App-Controller
 */
class AppController extends Controller {

	var $components = array('Auth', 'Session', 'RequestHandler');
	function beforeFilter(){
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
		$this->Auth->loginRedirect = array('controller' => 'entries', 'action' => 'index');
		$this->Auth->allow('display');
		$this->Auth->authorize = 'Controller';
	}
	function isAuthorized() {
		return true;
	}
	
	
	
	function adjustDataArray(&$dataArray){
		
		if(isset($dataArray['Entry'])){
			foreach($dataArray['Entry'] as $key=>$field)
				if(strlen($field) == 0)
					$dataArray['Entry'][$key] = null;
			
		}
		
		
		// get any fields in the form post which don't directly represent the data
		// model and rework them into the data array
		
		
		if(!isset($dataArray['Date']))
			return;
		$dateArr =& $dataArray['Date'][0];
		
		if(isset($dateArr['days_of_week']) && is_array($dateArr['days_of_week']))
			$dateArr['days_of_week'] = implode($dateArr['days_of_week'], ",");
		
		if(isset($dateArr['weeks_of_month']) && is_array($dateArr['weeks_of_month']))
			$dateArr['weeks_of_month'] = implode($dateArr['weeks_of_month'], ",");
		
		if(isset($dateArr['months_of_year']) && is_array($dateArr['months_of_year']))
			$dateArr['months_of_year'] = implode($dateArr['months_of_year'], ",");
		
		
		if(empty($dateArr['days_of_week']))
			unset($dateArr['days_of_week']);
		
		if(empty($dateArr['weeks_of_month']))
			unset($dateArr['weeks_of_month']);
		
		if(empty($dateArr['start_date']))
			unset($dateArr['start_date']);
		else{
			if(!is_array($dateArr['start_date']))
				$this->toCakeDateFormat($dateArr['start_date']);	
		}
		
		if(empty($dateArr['end_date']))
			unset($dateArr['end_date']);
		else{
			if(!is_array($dateArr['end_date']))
				$this->toCakeDateFormat($dateArr['end_date']);	
		}
		
		if(empty($dateArr['start_time']))
			unset($dateArr['start_time']);
		else
			if(!is_array($dateArr['start_time']))
				$this->toCakeTimeFormat($dateArr['start_time']);		
		
		if(empty($dateArr['end_time']))
			unset($dateArr['end_time']);
		else	
			if(!is_array($dateArr['end_time']))
				$this->toCakeTimeFormat($dateArr['end_time']);		
	
	}
	
	
	
	// these replace date and time fields with strings like 2010-03-01 arrays (in place)
	// with fields for year, month, day, or hr, min, meridian 
	
	function toCakeTimeFormat(&$arrayField){
		$time = explode(' ', trim($arrayField));
		$arrayField = array();
		$arrayField['meridian'] = strtolower($time[1]);
		$time = explode(':', $time[0]);
		$arrayField['hour'] = $time[0];
		$arrayField['min'] = $time[1];
	}

	function toCakeDateFormat(&$arrayField){

		$d = new DateTime($arrayField);
		$arrayField = array('month'=>$d->format('n'),
					'day'=>$d->format('j'),
					'year'=>$d->format('Y'));

	}

	
	
}
