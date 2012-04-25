<?php

// utility helper object for calendar functionality
class CalendarHelper extends AppHelper{

	
	// returns an array of month arrays (previous, current, and following month) 
	// where each month-array's keys correspond to month days and contain
	// either an array of entries or false
	function getMonthViewArray($entries, $year, $month){
	
		$view = "month";

		// arrays 
		$prevMonthNthDayMap = $this->getNthWeekdayOfMonthMap($month > 1 ? $year : $year - 1, $month > 1 ? $month - 1 : 12);
		$thisMonthNthDayMap = $this->getNthWeekdayOfMonthMap($year, $month);
		$nextMonthNthDayMap = $this->getNthWeekdayOfMonthMap($month < 12 ? $year : $year + 1, $month < 12 ? $month + 1 : 1);
		$monthMapsArray = array($prevMonthNthDayMap, $thisMonthNthDayMap, $nextMonthNthDayMap);
		$monthsArray = array(0=>array(), 1=>array(), 2=>array());

		$dayHoursArray = array();


		// iterate current and also previous/subsquent months to fill overlap days
		$prevMonthNum = $month > 1 ? $month - 1 : 12;
		$nextMonthNum = $month < 12 ? $month + 1 : 1;
		$prevYearNum = $month < 12 ? $year : $year - 1;
		$nextYearNum = $month < 12 ? $year : $year + 1;

		foreach($monthsArray as $key => $monthArray){
			switch($key){
				case 0: $this->initializeMonthArray($prevYearNum, $prevMonthNum, $monthArray); break;
				case 1: $this->initializeMonthArray($year, $month, $monthArray); break;
				case 2: $this->initializeMonthArray($nextYearNum, $nextMonthNum, $monthArray); break;
			}
				
			// iterate all entries and place in monthArrays
			foreach($entries as $entry){

				switch($view){
					case "month":
					foreach($entry['Date'] as $entryDate){
					
						// distinguish different date types
						
						$dateType = $this->getDateType($entryDate);
						
						
						switch($dateType){			
							case 'yearly':
								$monthNum = ($key == 1) ? $month : ($key == 0 ? $prevMonthNum : $nextMonthNum); // month we're currently iterating
								$arr = explode("-", $entryDate['start_date']); // break date into parts
								if($monthNum == $arr[1]){
									$dayNum = intval($arr[2]);
									array_push($monthArray[$dayNum], $entry);
								}
								break;
								
							case 'weekly':
							
								if($entryDate['weeks_of_month'] == 'nth_week'){
									//echo $entryDate['start_date'];
									$date = new DateTime($entryDate['start_date']);
									
									// is start date after current month?
										//break;
									
									$weekdays = explode(",", $entryDate['days_of_week']);
									$d = -1;
									$adjWd = $date->format('w');
									
									foreach($weekdays as $wd){
										if($wd == $adjWd){
											$d = $adjWd;
											break;
										}
									}
									// is date one of weekdays?
									if($d == -1){
										// set to beginning of week 
										//$date = new DateTime();
									}
									else {
										//scroll to weekday
									}
									
									$ctr = 0;
									while(true){
										// if in range of this month add to month array
										if($date->format('n') == $month)
											array_push($monthArray[$date->format('j')], $entry);

										// select day and other days if exist 
										// ...
										
										// scroll to next nth week
										$date->add(new DateInterval('P14D'));
										
										// if beyond range break
										if($date->format('n') > $nextMonthNum){
											break;
										}
										
										$ctr++;
										if($ctr > 1000) 
											break;
									}		
								} else{
									if($entryDate['weeks_of_month'] == 'every_week')
										$weeksOfMonth = '1,2,3,4,5';
									else 
										$weeksOfMonth = $entryDate['weeks_of_month'];
									
									$weeksOfMonth = explode(",", $weeksOfMonth);
									foreach($weeksOfMonth as $weekOfMonth){
										$daysOfWeek = explode(",", $entryDate['days_of_week']);
										foreach($daysOfWeek as $dayOfWeek){
											if(isset($monthMapsArray[$key][$dayOfWeek][$weekOfMonth])){ // some 5th weekdays don't exist
												$dayNum = $monthMapsArray[$key][$dayOfWeek][$weekOfMonth];
												array_push($monthArray[$dayNum], $entry);
											}
										}					
									}
								}
								break;
							
							case 'onetime':
								$monthNum = ($key == 1) ? $month : ($key == 0 ? $prevMonthNum : $nextMonthNum); // month we're currently iterating
								$arr = explode("-", $entryDate['start_date']); // break date into parts
								if($monthNum == $arr[1]){
									$dayNum = intval($arr[2]);
									array_push($monthArray[$dayNum], $entry);
								}
								break;
								}
							}
						break;
						
					case "year":
						
						break;
				}
			}
			
			$monthsArray[$key] = $monthArray; // todo: why doesn't it work without this?
		}
		return $monthsArray;

	}

	
	// get array of hours of day:  array(hour=>array of entries)

	function getDayViewArray($entries, $year, $month, $day){
		
		$dayHoursArray = array();
		$dayHoursArray['allday'] = array();
		for($i=0; $i<25; $i++)
			$dayHoursArray[$i] = array();

		// iterate current and also previous/subsquent months to fill overlap days
		$prevMonthNum = $month > 1 ? $month - 1 : 12;
		$nextMonthNum = $month < 12 ? $month + 1 : 1;
		$prevYearNum = $month < 12 ? $year : $year - 1;
		$nextYearNum = $month < 12 ? $year : $year + 1;

		
		$monthArray = array();
		$thisMonthNthDayMap = $this->getNthWeekdayOfMonthMap($year, $month);
		$this->initializeMonthArray($prevYearNum, $prevMonthNum, $monthArray);
					
			
			// iterate all entries and place in monthArrays
			foreach($entries as $entry){

				
				// loop through dates for entry
				foreach($entry['Date'] as $entryDate){
					
					
					$dateType = $this->getDateType($entryDate);
						
					if($dateType == 'yearly' || $dateType == 'onetime'){
							if($entryDate['start_date'] == $this->mySQLDateStamp($year, $month, $day)){
								isset($entryDate['start_time']) ? 
									($arr =& $dayHoursArray[$this->getHour($entryDate['start_time'])]) : 
									($arr =& $dayHoursArray['allday']);
								array_push($arr, $entry);
							}
					}
					else switch($dateType){	
						case 'weekly':
							
							$weeksOfMonth = ($entryDate['weeks_of_month'] == 'every_week') ?
								'1,2,3,4,5' : $entryDate['weeks_of_month'];
							$weeksOfMonth = explode(",", $weeksOfMonth);
							foreach($weeksOfMonth as $weekOfMonth){
								$daysOfWeek = explode(",", $entryDate['days_of_week']);
								foreach($daysOfWeek as $dayOfWeek){
									if(isset($thisMonthNthDayMap[$dayOfWeek][$weekOfMonth])){
										if ($thisMonthNthDayMap[$dayOfWeek][$weekOfMonth] == $day){
											isset($entryDate['start_time']) ? 
												($arr =& $dayHoursArray[$this->getHour($entryDate['start_time'])]) : 
												($arr =& $dayHoursArray['allday']);
											array_push($arr, $entry);
										}
									}
								}
							}
							break;
							
						case 'monthly':
							break;
					}
				}
				
			
			
		}
		return $dayHoursArray;

	}


	// returns a properly formatted calendar date entry link for use in various views
	function makeCalendarDateEntryLink($entry, $class = null){
		return "<a href='/calendar/entries/view/".$entry['Entry']['id']."' entryId=".$entry['Entry']['id']." name=entry>".$entry['Entry']['name']."</a>";
		
		$options = array('name' => 'entry',
				'popUpText' => $entry['Entry']['name'],
				'entryId' => $entry['Entry']['id']);
		if(isset($class))
			$options['class'] = $class;
		
		
		$htmlHelper = new HtmlHelper($this);
		return $htmlHelper->link($entry['Entry']['name'],
			array('escape' => false,
				'controller' => 'entries', 	
				'action' => 'view',
				$entry['Entry']['id']),
			$options);
	}

		
	// when fed a number, adds the English ordinal suffix. Works for any
	// number, even negatives
	function ordinal($number) {
		if ($number % 100 > 10 && $number %100 < 14):
			$suffix = "th";
		else:
			switch($number % 10) {
				case 0: $suffix = "th"; break;
				case 1: $suffix = "st"; break;
				case 2: $suffix = "nd"; break;
				case 3: $suffix = "rd"; break;
				default: $suffix = "th"; break;
			}
		endif;
		return "${number}$suffix";
	}


	function getDateType($entryDate){
		if(isset($entryDate['days_of_week']))
			$dateType = 'weekly';
		else{
			$dateType = 'onetime';
		
			if(isset($entryDate['repeating']))
				$dateType = 'yearly';
		
		}
		return $dateType;
	}



	// $date: data array from database
	// converts to a readable string eg, 'Every Tuesday', '1st and 3rd Sunday', etc.
	function dateAsString($date){
		
		$weekDays = array(1=>'Sunday', 2=>'Monday', 3=>'Tuesday', 4=>'Wednesday', 5=>'Thursday', 6=>'Friday', 7=>'Saturday');
		$type = $this->getDateType($date);
		if($type == "yearly" || $type == "onetime"){
			$d = new DateTime($date['start_date']);
			
			$str = '';
			if($date['repeating'])
				$str = 'Every ';					
			$str .= $d->format('F') . " " . $d->format('j').$d->format('S');
			$t = new DateTime($date['start_time']);
			if($t)
				$str .= ', '.$t->format('g').':'.$t->format('i').$t->format('a');
			$t = new DateTime($date['end_time']);
			if($t)
				$str .= '-'.$t->format('g').':'.$t->format('i').$t->format('a');
			
			return $str;
		}
		
		if($type == "monthly")
			return "monthly";
			
		if($type == "weekly"){
			
			$str = "";
			$arr = explode(',', $date['weeks_of_month']);
			if(count($arr) == 5)
				$str .= "Every ";
			else{
				foreach($arr as $key=>$daynum)
					$arr[$key] = $this->ordinal($daynum);
				$str .= implode($arr, ", ");
				$str .= " ";
			}
			
			$arr = explode(',', $date['days_of_week']);
			$arr2 = array();
			foreach($arr as $daynum){
				array_push($arr2, $weekDays[$daynum]);
			}
			$str .= implode($arr2, ", ");
			
			return $str;
		}
		
	
	}
	
	


	// creates a reverse lookup map for which date of month the "nth weekday" falls on (eg 3rd wednesday) 
	// $nthWeekdayOfMonthMap[weekdayNumber][nthWeekdayOfMonth]
	// $nthWeekdayOfMonthMap[1][3] is the date of the 3rd sunday of the month (which in january of 2012 is the 15th)
	// $nthWeekdayOfMonthMap[2][1] holds the date of the 1st monday, the 1st
	// etc
	
	function getNthWeekdayOfMonthMap($year, $month){

		// array to be populated
		$nthWeekdayOfMonthMap = array(1=>array(), 2=>array(), 3=>array(), 4=>array(), 5=>array(), 6=>array(), 7=>array());
		
		// counters array for days of week
		$daysOfWeek = array(1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1);
		
		// get weekday of first day of month
		$firstDay = new DateTime();
		$firstDay->setDate($year, $month, 1);
		$dayOfWeek = $firstDay->format('w') + 1;

		// get number of days in month
		$daysInMonth = $firstDay->format('t');
		
		
		//iterate through month days
		for($monthDay = 1; $monthDay <= $daysInMonth; $monthDay++){
			// add entry in map
			$nthWeekdayOfMonthMap[$dayOfWeek][$daysOfWeek[$dayOfWeek]] = $monthDay; 
			// adjust counters
			$daysOfWeek[$dayOfWeek]++; 
			$dayOfWeek = ($dayOfWeek == 7) ? 1 : ($dayOfWeek + 1); 
		}
		
		return $nthWeekdayOfMonthMap;
	}


	function initializeMonthArray($year, $month, &$arr){
		$daysInMonth = $this->getNumDaysInMonth($year, $month);
		for($i=1; $i<=$daysInMonth; $i++)
			$arr[$i] = array();
	}

	function getNumDaysInMonth($year, $month){
		$date = new DateTime();
		$date->setDate($year, $month, 1);
		return $date->format('t');
	}
		
	function mySQLDateStamp($year, $month, $day){
		$date = new DateTime();
		$date->setDate($year, $month, $day);
		return $date->format('Y-m-d');
	}
		
	function mySQLTimeStamp($hour, $min, $sec){
		if($min < 10) $min = "0" . $min; 
		if($sec < 10) $sec = "0" . $sec; 
		return "$hr:$min:$sec";
	}
	
	function getHour($time){
		$arr = explode(":", $time);
		return intval($arr[0]);
	}
	
	function getFirstDayOfWeek($date){
		$date = clone $date;
		$date->sub(new DateInterval("P".($date->format('w'))."D"));
		return $date;
	}
	
	function getFirstDayOfNextWeek($date){
		$date = clone $date;
		$date = $this->getFirstDayOfWeek($date);
		$date->add(new DateInterval("P7D"));
		return $date;
	}
	
	
	function getFirstDayOfPrevWeek($date){
		$date = clone $date;
		$date = $this->getFirstDayOfWeek($date);
		$date->sub(new DateInterval("P7D"));
		return $date;
	}
	
	
}



?>