<table>
<?php
	
	echo "<tr><td style='background:#eee;'>";
		
		// begin week view table
		
		echo "<table style='height:500px; width:100%;' class=weektable >";
		
		// get first day of week relative to $today
		$dayOfWeek = $this->Calendar->getFirstDayOfWeek($today);
		
		
		// column headers for days of week
		$prevLink = $this->Calendar->getFirstDayOfPrevWeek($today);
			
		$prevLink = $this->Html->link('<< prev', 
										array('controller'=>'entries', 
										'week',
										$prevLink->format('Y'), 
										$prevLink->format('n'), 
										$prevLink->format('j')), 
										array('class'=>'prevnext'));

		$nextLink = $this->Calendar->getFirstDayOfNextWeek($today);
		$nextLink = $this->Html->link('next >>', 
										array('controller'=>'entries', 
										'week',
										$nextLink->format('Y'), 
										$nextLink->format('n'), 
										$nextLink->format('j')), 
										array('class'=>'prevnext'));
		
		// week view calender nav bar
		echo 
		"<tr>
			<td colspan=20 style='height:20px;'>
				<table class='calendarHdr' border=0>
					<tr>	
						<td class='calendarHdr' style='background:black;'> $prevLink </td>
						<td class='calendarHdr' style='color:white; font-weight:bold; background:black'>
							Week of ".$dayOfWeek->format('M j, Y')." </td>
						<td class='calendarHdr' style='background:black;'> $nextLink </td>
					</tr>
				</table>
			</td>
		</tr>";
		
		
		
		// week view calender day column headers
		echo "<tr>";
		
		$hdrArr = array();
		$daysArr = array();
		
		for($n=1; $n<8; $n++){
			$weekDay = $dayOfWeek;
			
			$currentDay = new DateTime();
		
			$class = $weekDay->format('YMd') == $currentDay->format('YMd') ? 
				'calendarSubHdrToday' : 'calendarSubHdr';
		
			array_push($hdrArr, "<td class=$class style='height:20px;' colspan=2>".
				$weekDay->Format('D, M j')."</td>");
			
			// for each day of week get hours array. index for each hour may be empty or contain entries
			$dayHoursArray = $this->Calendar->getDayViewArray($entries, $weekDay->format('Y'), 
				$weekDay->format('n'), $weekDay->format('j'));
			array_push($daysArr, $dayHoursArray);
			
			$weekDay->add(new DateInterval("P1D"));
			
		}
		echo implode("<td class=spacerCellWeekView></td>", $hdrArr); 
		
		echo "</tr>";
		
		
		
		// all-day events row
		$tds = array();
		$boolAllDay = false;
		foreach($daysArr as $d){
				
			$str = "<td colspan=2 class='allDayCellWeekView'>
					<div>All Day Entries:</div>";
							
			if(@count($d['allday'])){
				foreach($d['allday'] as $dayEntry){
					$str .= "<div class=noOverflowDiv style='width:120px'>" . 
						$this->Calendar->makeCalendarDateEntryLink($dayEntry);
					$str .=  "</div>";
				}
			}
			$str .= "</td>";
			array_push($tds, $str);
		}
		echo "<tr>".implode("<td class=spacerCellWeekView></td>", $tds)."</tr>";
			
		
	
		// week view calender hour columns
		echo "<tr>";

		$rows = array();		
		for($hr=0; $hr<13; $hr++){
			echo "<tr class=hourCellRow>";
			$tds = array();
			$dayOfWeek = $this->Calendar->getFirstDayOfWeek($today);
		
			foreach($daysArr as $d){
			
				$hour = $hr == 0 ? 12 : $hr;
				$id = $dayOfWeek->format('Y/n/j/') . $hour . "/00/am";
				$str = "<td class='hourCellWeekView' id=$id><div class=noOverflowDiv  style='width:61px'>$hour:00 am<br>";
				foreach($d[$hr] as $entry){
					$str .= $this->Calendar->makeCalendarDateEntryLink($entry) . "<br>";
				}
				$id = $dayOfWeek->format('Y/n/j/') . $hour . "/00/pm";
				$str .= "</div></td><td class=hourCellWeekView id=$id><div class=noOverflowDiv style='width:61px'>$hour:00 pm<br>";
				foreach($d[$hr+12] as $entry){
					$str .= $this->Calendar->makeCalendarDateEntryLink($entry). "<br>";
				}
				$str .= "</div></td>";
				array_push($tds, $str);
				
				$dayOfWeek->add(new DateInterval('P1D'));
			}
			echo implode("<td class=spacerCellWeekView></td>", $tds);
			echo "</tr>";
		}
		
		
		echo "</tr>";
	
		echo "</table>";
		// end week view table 
		
	echo "</td></tr>";
?>
</table>



