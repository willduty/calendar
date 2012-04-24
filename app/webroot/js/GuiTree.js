/*
	gui in tree format
	author wduty
	version 0.1
*/
function GuiTree(){
	
	this.setup = function(){

		var _this = this;
		$('[tool]').each(function(){
			
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
					
					tool.show();
						
					var _toolSwitch = this;
			
					//close others if grouped with other switches
					if(this.getAttribute('type') == 'radio'){
						var name = this.getAttribute('name').replace(/\[/g, '\\\[').replace(/\]/g, '\\\]')
						$('[name='+name+']').each(function(){	
			
							if(this.getAttribute('tool') != _toolSwitch.getAttribute('tool'))
								_this.closeTool(this);
						
						})
				
					}
					
					tool.find('[defaultChecked]').trigger(this.getAttribute('trigger') || 'click');	
					
					
				}
			})
		})
	}
	
	// elem = the 'switch' elem not the tool elem
	
	this.closeTool = function(elem){
		var _this = this;
		$('#'+elem.getAttribute('tool')).each(function(){
			
			$(this).hide()
				.find('input').each(function(){
					if(this.getAttribute('type') == 'radio' || this.getAttribute('type') == 'checkbox' )
						this.checked = false;
					else
						this.value = '';
				});
			
			// recurse
			$(this).find('[tool]').each(function(){
				_this.closeTool(this)
			})
		})
	}
	

}



