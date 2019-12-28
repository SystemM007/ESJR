ClickTable.Row = Class.create({
	
	row : "",
	Draggable : null,
	
	initialize : function(row, Draggable)
	{
		this.row = row;
		this.Draggable = Draggable;
			
		$A(this.row.cells).grep(new Selector(".clickableCell")).invoke("observe", "mouseup", this.click.bindAsEventListener(this));
	},
	
	click : function(event)
	{
		// werkt niet : (
		// if(! event.isLeftClick() ) return;
		
		/*
		* Dit heeft betekenis omdat wij row observen. De draggable observed daarentegen het document
		* Daarom wordt deze handler altijd aangeroepen VOORDAT een eventuele drag wordt beeindigd
		*/
		if(! (this.Draggable && this.Draggable.dragging) )
		{
			ID = this.row.id.split("_")[1];
			new Request.Core(ID);
		}
		else
		{
			//console.log("was dragging");
			// event.stop(); // zo zorg je dat een element wel kan worden gepakt maar niet gedropt.
		}
	
	}
});