Editable.Select = Class.create(Editable.AbstractText,
{
	createEditorElm : function()
	{	
		try
		{	
			elm = this.originalElm;
			attr = this.getStyleClassAttr(elm);
			
			if(!(this.options.selectOptions))
			{
				throw new Error("editable.selectOptions is niet gedefenieerd");
			}
			
			//alert(options.selectOptions);
			
			this.editorElm = Builder.node("select", attr, this.options.selectOptions);
			this.editorElm = $(this.editorElm);
			this.editorElm.update(this.options.selectOptions);
		}
					
		catch(e){Admin.c(e)};
	},
	
	
	revertElement : function (cancel)
	{	
		try
		{
			this.editorElm.parentNode.replaceChild(this.originalElm, this.editorElm);
			
			if(!cancel)
			{
				value = this.editorElm.options[this.editorElm.selectedIndex].innerHTML;
				this.originalElm.innerHTML = value;
			}
		}
		catch(e){Admin.c(e)};
	}
	
});