EditableReg = {
	
	editables : $H(),
	
	register : function(obj)
	{
		this.editables.set(obj.id, obj);
	},
	
	get : function(id)
	{
		return this.editables.get(id);
	},
	
	getActive : function()
	{
		return this.editables.values().findAll(function(editable){ return editable.active; });
	},
	
	revertAll : function()
	{
		try
		{
			this.editables.values().invoke("revert", false);
		}
		catch(e){Admin.c(e)};
	},
	
	cancelAll : function()
	{
		try
		{
			this.editables.values().invoke("revert", true);
		}
		catch(e){Admin.c(e)};
	},
	
	getAllContent : function()
	{
		var allContent = $H();
		
		this.editables.each(function(pair)
		{
			id = pair.key;
			editable = pair.value;
			
			editableContent = editable.getContent();
			if(editableContent !== false) allContent.set(id, editableContent);
		});
	
		return allContent;
	},
	
	makeEditables : function(editables)
	{
		if(!editables)
		{
			return;
		}
		
		$A(editables).each( function(editable)
		{
			id = editable.id;
			options = editable.options;
								
			if(!(editType = options.editType))
			{
				throw new Error("editable.editType is niet gedefeniëerd");
			}				
			
			switch(options.editType)
			{
				case "Tiny" :
					editable = new Editable.Tiny(id, options);
				break;
				
				case "Text" :
					editable = new Editable.Textfield(id, options);
				break;
				
				case "Textarea" :
					editable = new Editable.Textarea(id, options);
				break;
				
				case "Password" :
					editable = new Editable.Textpasswd(id, options);
				break;
				
				case "Select" :
					editable = new Editable.Select(id, options);
				break;
				
				default : 
					throw new Error("Geprobeerd editable te maken met onbekend type: " + options.editType);
			}
			
			if(options.directConvert) editable.convert(true);

		 });
		
		giveFocus = $A(editables).find(function(editable){ return editable.options.giveFocus; });
	
		if(giveFocus)
		{
			giveFocusId = giveFocus.id;
			this.get(giveFocusId).convert();
		}
	},
	
	feedback : function(feedback)
	{
		if(!feedback)
		{
			return;
		}
		
		$H(feedback).each(function(pair)
		{
			id = pair.key;
			state = pair.value;
			
			try
			{
				if(!EditableReg.editables.get(id))
				{
					throw new Error("EditableReg.editables.get("+ Object.inspect(id) + ") werd niet gevonden bij het geven van feedback");
				}
				
				switch(state)
				{
					case "saved" :
						EditableReg.editables.get(id).revert(false);
					break;
					
					case "error" : 
						EditableReg.editables.get(id).markAsError();
					break;
					
					default:
						throw new Error("Onbekende feedback gegeven aan veld "+ id +", feedback: " + state );	
				}
			}
			catch(e){Admin.c(e)};
		});
	}
}