Editable.AbstractText = Class.create(Editable.Abstract, 
{
	createEditorElm : Prototype.emptyFunction(),
		
	getEditorContent : function()
	{	
		//return escape(this.editorElm.value);
		return this.editorElm.value;
	},
	
	convertElement : function(doNotFocus)
	{	
		try
		{	
			// helaas nog niet geheel fijlloos
			//Position.clone(this.originalElm, this.editorElm); 
			
			this.originalElm.replaceElement(this.editorElm);
							
			if(!doNotFocus) this.editorElm.focus();
			this.editorElm.select()

		}
		catch(e){Admin.c(e)};
	},
	
	revertElement : function (cancel)
	{	
		try
		{
			this.editorElm.parentNode.replaceChild(this.originalElm, this.editorElm);
			
			if(!cancel)	this.originalElm.innerHTML = this.editorElm.value.escapeHTML();
		}
		catch(e){Admin.c(e)};
	},
	
	getStyleClassAttr : function(elm)
	{	
		attr = new Object();
		
		className = elm.readAttribute("class");
		attr.className = className ? className : "";
		
		style = elm.readAttribute("style");
		attr.style = style ? style : "";
	
		return attr;
	}
});