RequestReg = {

	queue : $A(),
	isRunning : false,
	runningRequest : null,
	
	init : function()
	{
		document.observe("Request:finish", this.requestCompleted.bindAsEventListener(this));
	},

	newRequest : function(request)
	{
		try
		{
			if(! (request instanceof Request.Abstract)) throw new Error("Aan RequestReg kunnen alleen instanties van Request.Abstract worden toegevoegd");
		
			if(this.runningRequest && this.runningRequest.match(request)) Admin.c( new Error("De gestarte actie wordt reeds uitgevoerd") );	
			
			if(this.queue.include(request)) throw new Error("De gestarte actie staat reeds in de rij!");	
		}
		catch(e)
		{
			Admin.c(e);
			return;
		}

		this.queue.push(request);
		
		if(!this.isRunning)
		{
			this.startNextRequest();
		}
	},
	
	startNextRequest : function()
	{
	
		if(this.isRunning)
		{
			Admin.c( new Error("startNextRequest is aangeroepen terwijl isRunning true was") );
		}
		
		nextRequest = this.queue.shift();
		
		//console.info("next request", this);
		
		if(nextRequest)
		{
			// volgende actie wordt gestart
			this.isRunning = true;
			this.runningRequest = nextRequest;
			this.runningRequest.startRequest();
		}
		else
		{
			// rij is leeg
			//console.info("no request left");	
			this.runningRequest = null;
		}
	},
	
	requestCompleted : function()
	{	
		//console.info("requestCompleted");
		this.isRunning = false;	
		this.startNextRequest();
	}
}
