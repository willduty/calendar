



function formatDateObj(date){
	var str = '';
	if(date.days_of_week){
		if(date.weeks_pattern == 'every_week')
			str += 'Every ';
		if(date.weeks_pattern == 'nth_week')
			str += 'Every Other ';
			
		else if(date.weeks_pattern == 'nth_weekdays_of_month'){
			var arr = date.weeks_of_month.split(',');
			for(var i in arr)
				arr[i] = ordinal(arr[i]);
			if(arr.length == 1)
				str += arr[0] + " ";
			else 
				str += arr.join(' & ') + " ";
		}
		
		var arr = date.days_of_week.split(',');
		for(var i in arr){
			arr[i] = dayOfWeekName(parseInt(arr[i]))
		}
		str += arr.join(", ");
	}
	if(date.start_date)
		str += date.start_date;
	if(date.end_date)
		str += " thru " + date.end_date;
	
	var start = getClockTime(date.start_time)
	var end = getClockTime(date.end_time)
	
	if(start && end) 
		str +=  ", " + start + "-" + end;
	else if(start && !end)
		str += ", " + start;
	else if(!start && end)
		str += ', [start time unspecified]' + "-" + end + "<br>";
	
	function getClockTime(time){
		var now  = new Date();
		try{var t = time.split(":")}catch(e){return null};
		now.setHours(t[0], t[1], t[2], 0);
		var hour   = now.getHours();
		var minute = now.getMinutes();
		var second = now.getSeconds();
		var ap = "AM";
		if (hour   > 11) { ap = "PM";             }
		if (hour   > 12) { hour = hour - 12;      }
		if (hour   == 0) { hour = 12;             }
		if (hour   < 10) { hour   = hour;   }
		if (minute < 10) { minute = "0" + minute; }
		if (second < 10) { second = "0" + second; }
		var timeString = hour + ':' + minute + (parseInt(second) ? (':' + second) : "") + ap;
		return timeString;
	} 
	
	return str;
}






function dayOfWeekName(num){
	switch(num){
		case 1: return 'Sunday'; 
		case 2: return 'Monday'; 
		case 3: return 'Tuesday'; 
		case 4: return 'Wednesday'; 
		case 5: return 'Thursday'; 
		case 6: return 'Friday'; 
		case 7: return 'Saturday'; 
	}
}



function ordinal(number) {
	var suffix = '';
	if (number % 100 > 10 && number %100 < 14)
		suffix = "th";
	else
		switch(number % 10) {
			case 0: suffix = "th"; break;
			case 1: suffix = "st"; break;
			case 2: suffix = "nd"; break;
			case 3: suffix = "rd"; break;
			default: suffix = "th"; break;
		}
	return number + suffix;	
}

