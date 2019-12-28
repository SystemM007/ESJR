Editable.Textarea = Class.create(Editable.AbstractText,
{
	createEditorElm : function()
	{
		try
		{
			
			elm = this.originalElm;
			
			attr = this.getStyleClassAttr(elm);
			
			this.editorElm = Builder.node("textarea", attr, elm.innerHTML);
		
			this.editorElm = $(this.editorElm);
		}
				
		catch(e){Admin.c(e)};
	}
});