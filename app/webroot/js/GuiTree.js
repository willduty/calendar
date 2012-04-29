/*
	gui in tree format
	author wduty
	version 0.1
*/
function GuiTree(){
	
	this.setup = function(){

		var _this = this;
		$('[tool]').each(function(){
			var toolSwitch = this;
			var trigger = this.getAttribute('trigger') || 'click';
			var tool = $('#'+this.getAttribute('tool'));
			
			
			$(this).bind(trigger, function(){
				// if lone checkbox, switch on/off
				if(this.getAttribute('type') == 'checkbox' &&
					(this.getAttribute('name') == null || 
						$('[name='+this.getAttribute('name')+']').length == 1)){
					if(this.checked)
						tool.toggle();
					else
						_this.closeTool(this);
				}
				// all other types, show tool
				else{
				
					// if tool elem not in dom, retrieve it from 
					// holding obj and reinsert at placeholder 
					if(0 == $(tool).parents().length){
							var token = toolSwitch.getAttribute('tool_token');
							var placeholder = $('#'+token);
							$(_this.holding[token]).insertAfter(placeholder);
					
							// clear all placeholder stuff
							toolSwitch.removeAttribute('tool_token');
							placeholder.remove();
							delete _this.holding[token];
					}
					
					tool.show();
					
					var _toolSwitch = this;
			
					//close others if grouped with other switches
					if(this.getAttribute('type') == 'radio'){
						var name = this.getAttribute('name').replace(/\[/g, '\\\[').replace(/\]/g, '\\\]')
						$('[name='+name+']').each(function(){		
							if(this.getAttribute('tool') != _toolSwitch.getAttribute('tool')){
								_this.closeTool(this);
							}
						})
				
					}
					
					tool.find('[defaultChecked]').trigger(this.getAttribute('trigger') || 'click');	
					
					
				}
			})
		})
	}
	
	
	
	this.holding = {};
	
	// elem = the 'switch' elem not the tool elem
	this.closeTool = function(elem){
		
		// clear all values in descendant
		this.clearDescendents(elem);
	
		// take tool elem out of dom entirely store in holding obj
		// and replace with placeholder elem with token attr to be 
		// able to reinsert later
		
		if(!elem.hasAttribute('tool_token'))
		{
			var token = Math.random().toString().split(".")[1];
			var ph = document.createElement('placeholder');
			ph.id = token ;
			elem.setAttribute('tool_token', token);
			$(ph).insertAfter(document.getElementById(elem.getAttribute('tool')));
			var p = $('#'+elem.getAttribute('tool')).parent();
			this.holding[token] = $('#'+elem.getAttribute('tool')).detach();
		}
		
	}
	
	this.clearDescendents = function(elem){
		
		var _this = this;
		$('#'+elem.getAttribute('tool')).each(function(){
			
			$(this).hide()
				.find('input').each(function(){
					if(this.getAttribute('type') == 'radio' || this.getAttribute('type') == 'checkbox' )
						this.checked = false;
					else
						this.value = '';
				});
			
			// recurse // todo: do without extraneous recursions
			$(this).find('[tool]').each(function(){
				_this.clearDescendents(this)
			})
		})
	}

}



