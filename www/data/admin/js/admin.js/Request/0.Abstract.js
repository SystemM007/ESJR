Request.Abstract = Class.create({
	/*
	* Events
		start
		start_newpage
		finish
	*/
	
	postQuery : $H(),

	newPage : false,
	loadEditables : false,
	
	// dit wordt geset door de children
	// - page
	// - core
	// - action
	// - history
	uriAddition : "",
	
	
	initialize : function(postQuery, options)
	{
		options = $H(options).toObject();
		if(options.newPage) this.newPage = true;
		if(options.loadEditables) this.loadEditables = true;
		if(options.afterReq) throw new Error("afterReq is afgekeurd");
		
		this.makePostQuery(postQuery);
		
		if(this.newPage && !this.loadEditables && EditableReg.getActive().length > 0)
		{
			if(!confirm("Weet u zeker dat u deze pagina wilt verlaten zonder op te slaan?")) return;
		}

		Admin.debug("Request /// Created", this);
		
		// in de rij plaatsen
		RequestReg.newRequest(this);
	},
	
	match : function (req)
	{
		if(! (req instanceof Request.Abstract))
		{
			console.log(req);
			throw new Error("Requests kunnen alleen worden gematched tegen instanties van Request.Abstract");
		}
		
		return (this.postQuery.toObject() == req.postQuery.toObject() && this.loadEditables == req.loadEditables && this.newPage == req.newPage);
	},
	
	makePostQuery : function(postQuery)
	{
		input = '';
		if(this.loadEditables)
		{
			input = EditableReg.getAllContent().toJSON();
		}
				
		this.postQuery = $H(postQuery).merge({
			"input" : input,
			"instanceId" : Admin.instanceId
		});
	},

	startRequest : function()
	{	
		this.startAjax();
		
		document.fire("Request:start");
		if(this.newPage) document.fire("Request:start_newpage");
	},

	
	startAjax : function(){
		
		ajaxOptions = {
			onSuccess : this.processResponse.bindAsEventListener(this),
			onException : this.processException.bindAsEventListener(this),
			onFailure : this.processFailure.bindAsEventListener(this),
			requestHeaders : {"Cache-Control" : "no-cache"},
			// heel vreemd. Ik denk een bug: er kan geen hash worden gegeven aan de parameters.
			parameters : this.postQuery.toObject()
		}
		
		try
		{
			new Ajax.Request(Admin.adminUri + this.uriAddition, ajaxOptions);
		}
		catch(e)
		{
			Admin.c(e)
		};
	},
	
	processResponse : function(transport)
	{
		if(! (data = transport.responseJSON) )
		{
			console.error(transport.responseText.stripTags());
		}
		else
		{
			new Prosessor(data, this.newPage);
		}
				
		document.fire("Request:finish");
	},
	
	processException : function(transport, exception)
	{	
		Admin.c(exception);
		document.fire("Request:finish");
	},
	
	processFailure : function(transport)
	{
		Admin.c( new Error(transport.responseText) );
		document.fire("Request:finish");
	}
});