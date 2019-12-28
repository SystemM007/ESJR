Editable.Textpasswd= Class.create(Editable.AbstractText,
{
	
	afterInit : function()
	{
		this.originalElm.update("***********");
	},
	
	createEditorElm : function()
	{
		try
		{	
			attr = Object.extend(this.getStyleClassAttr(this.originalElm), {
				type : "password"
			});
			
			this.editorElm = Builder.node("input", attr, "");
								
			this.editorElm = $(this.editorElm);
			
		}
				
		catch(e){Admin.c(e)};
		
	},
	
	// OVERSCHRIJFT EditableText.getEditdorContent!
	// IS HIER EEN 	encodeURIComponent NODIG?
	getEditorContent : function()
	{	
		return hex_md5(this.editorElm.value);	
	},
	
	// OVERSCHRIJFT EditableText.revertElement!
	revertElement : function (cancel)
	{	
		try
		{
			this.editorElm.parentNode.replaceChild(this.originalElm, this.editorElm);
		}
		catch(e){Admin.c(e)};
	}

});