// JavaScript Document

Maximize = {

	init : function()
	{
		// init maximize var
		this.maximizeBound = this.maximize.bindAsEventListener(this);
	
		// observe tips
		Event.observe(document, "Win:tips_open", this.maximizeBound);
		Event.observe(document, "Win:tips_close", this.maximizeBound);
		
		// observe resize wordt gedaan ná de maximize!
	
		// maximaliseer scherm
		this.maximize();
	},
	
	maximize : function ()
	{	
		// deactiveer Event
		Event.stopObserving(window, "resize", this.maximizeBound);
		
		// hoogte
		
		// hoogte die nodig is voor alles behalve de contentCont
		restHeight = 107;
			
		bodyHeight = $$("body")[0].getHeight();
		
		height = bodyHeight - restHeight;

		$("wrapper").setStyle({"height" : height + "px"});
		
		// breedte
		restWidth = 10;
		tipsWidth = 208;
		
		if(Win.tipsActive) restWidth += tipsWidth;
		
		bodyWidth = $$("body")[0].getWidth();
		
		width = bodyWidth - restWidth;

		$("content").setStyle({"width" : width + "px"});
		
		Event.observe(window, "resize", this.maximizeBound);
		
		
	}
}
