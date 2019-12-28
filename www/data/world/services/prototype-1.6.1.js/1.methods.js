// JavaScript Document

//http://www.prototypejs.org/api/element/addMethods

Element.addMethods(
{	
	replaceElement : function(element, replacement){
		
		element = $(element);
		
		element.parentNode.replaceChild(replacement, element);
		
		return Element.extend(replacement);
	},
	
	setDimensions : function(element, dimensionObject){
		
		element = $(element);
		
		if(typeof(dimensionObject.height) == "undefined") throw new Error("Dimension object heeft geen height eigenschap");
		if(typeof(dimensionObject.width) == "undefined") throw new Error("Dimension object heeft geen width eigenschap");
	
		
		dimensionObject.height += "px";
		dimensionObject.width += "px";
		
		element.setStyle(dimensionObject);
		
		return element;
	}
		
		
});