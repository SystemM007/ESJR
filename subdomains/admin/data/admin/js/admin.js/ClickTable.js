ClickTable = Class.create({
	
	id : "",
	options : {},
	
	initialize : function(id, options)
	{
		if(!id) throw new Error("Geen id in ClickTable");
		if(!options.lifeId) throw new Error("Geen lifeId in ClickTable");
		if(!options.action) throw new Error("Geen action in ClickTable");
		
		this.id = id;
		this.options = Object.extend({
			"sortable" : false
		}, options);
		
		if(this.options.sortable) this.makeSortable();
		else this.makeClickable();
	},
	
	makeSortable: function()
	{
		Position.includeScrollOffsets = true;
	
		Sortable.create(this.id, {
			tag : "tr",
			ghosting : true,
			onUpdate : this.orderChange.bind(this),
			scroll : "content",
			scrollSensitivity : 100
			
		});
		
		/*
		* we gaan er nu vanuit dat Sortable.sortables.adminList.draggables
		* en $A($(options.id).rows) corresponderende elementen hebben
		* dus de rij op index i correspondeert met draggable op index i
		*/
		rows = $A($(this.id).rows);
		draggables = Sortable.sortables[this.id].draggables;
		
		for(i=0; i<rows.length; i++)
		{
			new ClickTable.Row(rows[i], draggables[i]);
		}
	},
	
	// niet nodig wanneer sorteerbaar gemaakt!
	makeClickable : function()
	{
		rows = $A($(this.id).rows);
		
		for(i=0; i<rows.length; i++)
		{
			new ClickTable.Row(rows[i], null);
		}
	},
	
	orderChange : function(Container)
	{
		new Request.Action(this.options.lifeId, this.options.action, {"childListOrder" :Sortable.sequence(this.id).toJSON()});
	}
});