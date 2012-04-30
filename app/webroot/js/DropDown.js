
function DropDown(elem, dropdownId){
	
	this.elem = elem;
	this.dropdown = $('#' + dropdownId);
	var _this = this;
	
	$(this.elem).bind("click" , function(e){
		
		var dropdown = _this.dropdown.detach()
		$(document.body).append(dropdown)
		
		dropdown.css({'position':'absolute', 
				'left':$(elem).offset().left, 
				'top':$(elem).offset().top + $(elem).height()})
			.show()
		
		$(document.body).bind('mousedown', function(e){
			
			if(e.target.innerHTML.indexOf('<') == -1 && $.contains(_this.dropdown.get(0), e.target)){
				$(_this.elem).val(e.target.innerHTML)
			}					
			_this.dropdown.hide();
			$(document.body).unbind('mousedown', arguments.callee);			
		})
			
	})
	
	// get rid of dropdown if user decides to type in value
	this.elem.onkeypress = function(){
		this.dropdown.hide();
	}
	
}
