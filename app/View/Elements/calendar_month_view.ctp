
<table>

<?php
	
	$firstDayOfMonth = new DateTime("$month/1/$year");
	
	$monthName = $firstDayOfMonth->format('F'); 

	$dayList = array(1 => 'Sun', 2 => 'Mon', 3 => 'Tue', 4 => 'Wed', 5 => 'Thu', 6 => 'Fri', 7 => 'Sat'); 

		// calendar header
		$nextMonth = $month + 1 < 13 ? $month + 1 : 1;
		$prevMonth = $month - 1 > 0 ? $month - 1 : 12;

		$prevLink = $this->Html->link('<< prev', 
										array('controller'=>'entries', 
										'action'=>'index', 
										$view,
										($prevMonth == 12) ? $year - 1 : $year, 
										$prevMonth), 
										array('class'=>'prevnext'));
		$nextLink = $this->Html->link('next >>', 
										array('controller'=>'entries', 
										'action'=>'index', 
										$view,
										($nextMonth == 1) ? $year + 1 : $year, 
										$nextMonth), 
										array('class'=>'prevnext'));

		?>

			<!-- calendar header/nav -->
			<tr>
				<td colspan=7 style='border:1px solid black;'>
					<table class='calendarHdr'>
						<tr>	
							<td class='calendarHdr' style='background:black;'> <?php echo $prevLink; ?> </td>
							<td class='calendarHdr' style="color:white; font-weight:bold; background:black"> 
								<?php echo $monthName . " " . $year; ?> 
							</td>
							<td class='calendarHdr' style='background:black;'> <?php echo $nextLink; ?> </td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- week day names -->
			<tr>
				<?php foreach($dayList as $weekDay){ ?>
				<td style='width:100px; border:1px solid #555;' class='calendarSubHdr'><?php echo $weekDay; ?></td>
				<?php } ?>
			</tr>

		<?php 



		// calendar body
		
		$monthCtr = $month;
		
		
		// iterate current and also previous/subsquent months to fill overlap days		
		// array with the three arrays with actual dates
		$monthsArray = array(
			$this->Calendar->getMonthViewArray($entries, $month < 12 ? $year : $year - 1, $month > 1 ? $month - 1 : 12),
			$this->Calendar->getMonthViewArray($entries, $year, $month),
			$this->Calendar->getMonthViewArray($entries, $month < 12 ? $year : $year + 1, $month < 12 ? $month + 1 : 1)		
		);

		reset($monthsArray);
		
		$weekDayOfFirst = $firstDayOfMonth->format('w');
		$weekDayOfFirst++;
		if($weekDayOfFirst == 8)
			$weekDayOfFirst = 1;
		
		// do we need to show part of last month?
		// if $weekDayOfFirst is sunday then no: set $monthArray to current month. else to prev month 	
		if($weekDayOfFirst == 1){
			$monthArray = next($monthsArray); 
			$dayCtr = 1;
		}
		else{
			$monthCtr = $prevMonth;
			$monthNthDayMap = $this->Calendar->getNthWeekdayOfMonthMap(($prevMonth == 12) ? $year - 1 : $year, $prevMonth);
			$dayCtr = end($monthNthDayMap[1]); // last sunday of previous month
			$monthArray = current($monthsArray); 
		}

		$continueFlag = true;
		
		// loop over and over to make row for each week starting with week 
		// containing first day of month to week containing last day of month.
		while(true){
			
			// make row of days of week 
			echo "<tr>";
			
			foreach($dayList as $key => $weekDay){
				
				// if we're out of days for the month, switch to following month
				if(!isset($monthArray[$dayCtr])){
					$dayCtr = 1;
					$monthArray = next($monthsArray);
					$monthCtr = ($monthCtr == 12 ? 1 : $monthCtr + 1);
				}
				
				// set up cell style 
				$cellStyle = "";
				
				if(key($monthsArray) != 1) // gray out days not in month
					$cellStyle .= "outOfMonthCell";
				
				// finally, draw the calendar day cell
				echo "<td class='calendarCell $cellStyle' id='$year/$monthCtr/$dayCtr' name=calendarCell>";
				
				echo "<div class='monthCellHdr'>
					<table style='width:100%;'>
						<tr><td style='width:90%;'>" . $dayCtr . "</td>
						<td style='width:10%;'><span class=arrow>&raquo;</span></td></tr>
					</table>
					</div>"; // date of month numeral
				
				// entries for day
				echo "<div class='noOverflowDiv' style='width:100px; height:70px;'>";
				
				if(is_array($monthArray[$dayCtr])){
					foreach($monthArray[$dayCtr] as $entry){
						// show calendar date entry
						echo $this->Calendar->makeCalendarDateEntryLink($entry, null);
						echo "<br>";
					}
				}
				echo "</div></td>";
				
				// break weeks loop if day is saturday (7) and it's either the last day of current month or we are in nextmonth
				if($key == 7){
					if((!isset($monthArray[$dayCtr + 1]) && key($monthsArray) == 1) || key($monthsArray) == 2)
						$continueFlag = false;
				}
				
				$dayCtr++; // increment day of month counter
			}
			
			// make row of days of week
			echo "</tr>";
			
			if(!$continueFlag)
				break;			
		}
		?>
	</table>
