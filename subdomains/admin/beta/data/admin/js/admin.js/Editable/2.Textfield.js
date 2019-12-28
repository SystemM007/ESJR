Editable.Textfield = Class.create(Editable.AbstractText,
{
	createEditorElm : function()
	{
		
		try
		{	
			elm = this.originalElm;
			
			attr = Object.extend(this.getStyleClassAttr(elm), {
				type : "text",
				value : elm.innerHTML.unescapeHTML()
			});
			
			this.editorElm = Builder.node("input", attr);
								
			this.editorElm = $(this.editorElm);
			
		}
				
		catch(e){Admin.c(e)};
		
	}
});