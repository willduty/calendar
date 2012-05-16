/*
	gui in tree format
	author wduty
	version 0.1
*/
function GuiTree(options){
	
	this.onClass = 'guiTreeOn';
	this.grayedClass = 'guiTreeGrayed';
	
	if(typeof options != 'undefined'){
		this.onClass = options.onClass || this.onClass;
		this.grayedClass = options.grayedClass || this.grayedClass;
	}
		
	this.setup = function(){

		var _this = this;
		
		$('[tool]').each(function(){
			var toolSwitch = this;
			var trigger = this.getAttribute('trigger') || 'click';
			
			$(this).bind(trigger, function(){
					_this.openTool(toolSwitch)
			})
		})
	}
	
	
	this.openTool = function(toolSwitch){
		if(typeof toolSwitch == 'string'){
			toolSwitch = $('#'+toolSwitch).get(0)
			console.log(toolSwitch)
		}
		
		var tool = null;
		if(toolSwitch.getAttribute('tool_token'))
			tool = $(this.holding[toolSwitch.getAttribute('tool_token')]);
		else
			tool = $('#'+toolSwitch.getAttribute('tool'));
		
		
		// if lone checkbox, switch on/off
		if(toolSwitch.getAttribute('type') == 'checkbox' &&
			(toolSwitch.getAttribute('name') == null || 
				$('[name='+toolSwitch.getAttribute('name')+']').length == 1)){
			if(toolSwitch.checked)
				tool.toggle();
			else
				this.closeTool(toolSwitch);
		}
		// all other types, show tool
		else{
		
			// if tool elem not in dom, retrieve it from 
			// holding obj and reinsert at placeholder 		
			if(0 == tool.parents().length){
					var token = toolSwitch.getAttribute('tool_token');
					var placeholder = $('#'+token);
					$(this.holding[token]).insertAfter(placeholder);
			
					// clear all placeholder stuff
					toolSwitch.removeAttribute('tool_token');
					placeholder.remove();
					delete this.holding[token];
			}
			
			tool.show();
		
			_this = this;
			
			//close others if grouped with other switches
			if(toolSwitch.getAttribute('type') == 'radio'){
				
				toolSwitch.checked = true;
				toolSwitch.nextSibling.className = this.onClass;
				var name = toolSwitch.getAttribute('name').replace(/\[/g, '\\\[').replace(/\]/g, '\\\]')
				
				// if all radios have same tool don't close on selects
				var test = $.map($('[name='+name+'][type=radio]'), function(elem, idx){
					return elem.getAttribute('tool');
				})
				var closeOthers = test.getUnique().length == 1 ? false : true;
				
				// go through each toolswitch and close/grayout as appropriate
				$('[name='+name+'][type=radio]').each(function(){	
					if(this != toolSwitch){
						this.nextSibling.className = _this.grayedClass
						if(closeOthers)
							_this.closeTool(this);
					}
				})
		
			}
			
			tool.find('[defaultChecked]').trigger(toolSwitch.getAttribute('trigger') || 'click');	
				
		}
	
	}
	
	
	
	this.openThrough = function(arr){
		
		$('#'+toolId)
		
		
	}
	
	
	
	this.holding = {};
	
	// elem = the 'switch' elem not the tool elem
	this.closeTool = function(elem){
		console.log('close')
			
		// clear all values in descendant
		this.clearDescendents(elem);
	
		// take tool elem out of dom entirely, store in holding obj
		// replace with placeholder elem with token attr to be findable later	
		if(!elem.hasAttribute('tool_token'))
		{
			var token = Math.random().toString().split(".")[1]; 
			var ph = document.createElement('placeholder');
			ph.id = token;
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


// from http://stackoverflow.com/questions/1960473/unique-values-in-an-array
Array.prototype.getUnique = function(){
   var u = {}, a = [];
   for(var i = 0, l = this.length; i < l; ++i){
      if(this[i] in u)
         continue;
      a.push(this[i]);
      u[this[i]] = 1;
   }
   return a;
}