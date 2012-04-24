/*
	Timepicker
	version 0.1
	author wduty
*/

function TimePicker(elem){

	this.elem = elem;
	var _this = this;
	this.elem.onfocus = function(){_this.show();}
	this.elem.onclick = function(){_this.show();}
	
	// to close dropdown on outside click
	function outerClick(e){
		if($(e.target).parents('#TimePicker').length == 0 &&
			e.target != _this.elem){
				$('#TimePicker').hide();
				$(document.body).unbind('click', arguments.callee);
		}
	}
	
	
	// show the dropdown
	this.show = function(){

		var tp = $('#TimePicker').detach()
		$(document.body).append(tp)
		
		tp.css({'position':'absolute', 
			'left':$(elem).offset().left, 
			'top':$(elem).offset().top + $(elem).height()})
		.show()
		.bind('click', function(e){ 
			e.preventDefault();
			
			// put the clicked value in the text box
			if(e.target.innerHTML.indexOf('<') == -1){
				$(_this.elem).val(e.target.innerHTML)
				$('#TimePicker').hide();
								
				// unbind inner and outer listeners
				$(document.body).unbind('click', outerClick);
				$('#TimePicker').unbind('click', arguments.callee);
				
			}
		})
		
		$(document.body).bind('click', outerClick)
			
	}
	
	// get rid of dropdown if user decides to type in value
	this.elem.onkeypress = function(){
		$('#TimePicker').hide();
	}
	
}
