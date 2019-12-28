Editable.Abstract = Class.create({
	
	active : false,
	
	id : null,
	options : {},
	outlineElm : null,
	originalElm : null,
	editorElm : null,
		
	originalContent : "",
	
	getEditorContent : Prototype.emptyFunction,
	createEditorElm : Prototype.emptyFunction,
	convertElement : Prototype.emptyFunction,
	revertElement : Prototype.emptyFunction,
	afterInit : Prototype.emptyFunction,
	
	initialize : function(objectID, options)
	{
		
		try
		{
			if(!(this.id = objectID)) throw new Error("objectID is niet gedefeniëerd");
			if(!(this.options = options)) throw new Error("options is niet gedefeniëerd");
			if(!$(this.id)) throw new Error(objectID + " is geen HTML element (type: " + editType + ")");
		}
		catch(e){Admin.catchedError(e)};
		
		this.createOutlineElm();
		this.createOrignalElm();

		this.createEditorElm();
		
		this.afterInit();
		
		EditableReg.register(this);
		
	},
	
	getID : function()
	{
		return this.id
	},
	
	setActive : function(active)
	{
		this.active = active;
	},
	
	createOutlineElm : function ()
	{
		this.outlineElm = $(this.id);
		
		this.outlineElm.addClassName("editable").addClassName("editable_" + this.options["editType"]);
		
		this.outlineElm.observe("click", this.convert.bindAsEventListener(this));
	},
	
	createOrignalElm : function()
	{
		this.originalElm = this.outlineElm.down();
		this.originalContent = this.originalElm.innerHTML;
	},
	
	convert : function(doNotFocus)
	{
		doNotFocus = doNotFocus == true;
		
		options = {
			duration:.3,
			afterFinish : this.startConvertElement.bind(this, doNotFocus)
		}
		new Effect.Highlight(this.id, options);
		
	},
	
	startConvertElement : function(doNotFocus)
	{
		if(this.active)	return;
		
		this.convertElement(doNotFocus);
		
		this.setActive(true);
		
		this.outlineElm.addClassName("active");		
	},
	
	revert : function(cancel){

		if(!this.active) return;
		
		this.outlineElm.removeClassName("error");
		this.outlineElm.removeClassName("active");
				
		this.revertElement(cancel);
		
		// zorgen dat ook de inhoud van de editor weer wordt gereset
		if(cancel)	this.createEditorElm();
		
		this.setActive(false);
		
		options = {
			startcolor: cancel ? "#000000" : "#91FF84",
			duration:1
		}
		this.outlineElm.highlight(options);
	},
	
	markAsError : function()
	{
		if(!this.active) return;
		
		if(!this.editorElm) return;
	
		options = {
			startcolor: "#FF0000",
			duration:3
		}
		
		this.outlineElm.highlight(options);
		this.outlineElm.addClassName("error");
	},
	
	getContent : function()
	{	
		if(!this.active) return false;
		
		return this.getEditorContent();
	}
});