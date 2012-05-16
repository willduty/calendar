	<tr>
			<td colspan=7 style='background-color:black; color:white; align:center;'>
				<table class='calendarHdr'><tr>	
					<td>All Calendar Entries</td>
				</tr></table>
			</td>
		</tr>
		<tr><td colspan=7  style='height:500px;'>
			<table style='width:100%;'>
			
			<tr>
				<td class=inactiveLink style="color:white; background:gray; width:30%">Name</td>
				<td class=inactiveLink style="color:white; background:gray; width:55%">Date</td>
				<td class=inactiveLink style="color:white; background:gray; width:15%">Category</td>
			</tr>
		
		<?php
		foreach($entries as $entry){
			echo '<tr>';
			
			echo '<td style="width:30%">';
			echo $this->Calendar->makeCalendarDateEntryLink($entry);
			echo '</td>';
			
			echo '<td style="width:55%" class=inactiveLink>';
			$dateStrings = array();
			foreach($entry['Date'] as $date){
				array_push($dateStrings, $this->Calendar->dateAsString($date));
			}
			echo implode(', ', $dateStrings);
			echo '</td>';
			
			echo '<td style="width:15%">';
			if(isset($entry['Entry']['category_id']))
				echo '<div class=inactiveLink>'.$categories[$entry['Entry']['category_id']].'</div>';
			echo '</td>';
			
			echo '</tr>';
		}
		echo '</table></td></tr>';
		?>