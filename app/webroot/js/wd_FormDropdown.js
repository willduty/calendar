
/*
	creates a drop down which fills a span next to the title with the selected item text and sets value of hidden field within the dropdown
*/
$.fn.wd_FormDropdown = function(){
	
	var propsObj = arguments.length ? arguments[0] : null;
	var elem = $(this[0]);
	
	// show/hide functionality
	elem.find("li")
			.click(function(){$(this).find('ul').first().show().end().mouseleave(function(){$(this).hide();})})
			.mouseleave(function(){$(this).find('ul').hide();});
	// dropdown item selection 
	elem.find("li ul li").each(function(){
		$(this).click(function(e){
			e.stopPropagation();
			$(this).parents(".wd_FormDropdown")
				.find('.selected').html(e.target.innerHTML)
				.end()
				.find("input:hidden").val(e.target.getAttribute('value'))
				.end()
				.find("ul").hide();
			if(propsObj)
				propsObj.callback(e.target)
		});
	});	
}

