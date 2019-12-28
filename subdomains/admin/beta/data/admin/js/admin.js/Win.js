// JavaScript Document

Win = {
	
	/*
	* Events Win:
		state_hidden
		state_appearing
		state_active
		state_fading
		Win:tips_open
		Win:tips_close
	*/
	
	
	
	buttonsActive : false,
	tipsActive : false,
	
	STATE_NOT_INITIALIZED : 0,
	STATE_HIDDEN : 1,
	STATE_APPEARING : 2,
	STATE_ACTIVE : 3,
	STATE_FADING : 4,
	
	state : 0,

	init : function()
	{
		document.observe("Request:start_newpage", this.pageClose.bindAsEventListener(this));
		document.observe("Prosessor:start_newpage", this.pageReset.bindAsEventListener(this));
		document.observe("Prosessor:finished_newpage", this.pageLoad.bindAsEventListener(this));
		
		document.observe("Request:start", function(){ $("loadingIndicator").show(); });
		document.observe("Request:finish", function(){ $("loadingIndicator").hide(); });
		
		this.setState(this.STATE_HIDDEN);
		this.transformHide();
	},
	
	transformHide : function()
	{
		// alle elementen met klasse startNoDisp worden met prototype verborgen en ontdaan van de klasse
		// nodig omdat prototype geen elementen kan aanpassen die met een 
		// klasse zijn voorzien van een style
		$$(".startNoDisp").invoke("hide").invoke("removeClassName", "startNoDisp");
	},
	
	
	pageClose : function()
	{
		if(this.jumpToStatic() == this.STATE_ACTIVE) // is alleen niet zo bij aller eerste request
		{
			Admin.debug("Win /// Page close");
			this.setMsg("Loading...");
			this.hide();
		}
		else
		{
			Admin.debug("Win /// Page close SKIPPED " + this.state);
		}
	},
	
	pageReset : function()
	{
		if(this.jumpToStatic() == this.STATE_HIDDEN) // is als het goed is altijd zo
		{
			Admin.debug("Win /// Page reset");
			this.reset();
		}
		else
		{
			Admin.debug("Win /// Page reset SKIPPED " + this.state);
		}
	},
	
	pageLoad: function()
	{
		Admin.debug("Win /// Page load");
		this.show();
	},
	
	show : function()
	{
		if(this.state != this.STATE_HIDDEN)
		{
			throw new Error("Show werd aangeroepen terwijl de window status niet gelijk was aan STATE_HIDDEN (1) maar " + this.state, "Show");
			return false;
		}
		
		options = {
			duration: 0,
			beforeStart : this.setState.bind(this, this.STATE_APPEARING),
			afterFinish : this.setState.bind(this, this.STATE_ACTIVE),
			queue : { scope : "UnwrittenWin" }
		}
		$("content").appear(options);
	},
	
	hide : function()
	{
		if(this.state != this.STATE_ACTIVE) throw new Error("Hide werd aangeroepen terwijl de window status niet gelijk was aan STATE_ACTIVE (3) maar " + this.state);
		
		this.closeTips();
		
		options = {
			duration: 0.5,
			beforeStart : this.setState.bind(this, this.STATE_FADING),
			afterFinish : this.setState.bind(this, this.STATE_HIDDEN),
			queue : { scope : "UnwrittenWin" }
		}
		$("content").fade(options);
	},
	
	reset : function ()
	{
		if(this.state != this.STATE_HIDDEN && this.state != this.STATE_NOT_INITIALIZED) throw new Error("reset werd aangeroepen terwijl de window status niet gelijk was aan STATE_FADING (1) of STATE_NOT_INITIALIZED (0) maar " + this.state);
	
		EditableReg.revertAll();
		
		$("wrapper").scrollTop = 0;
		
		$("titleText", "msg", "text", "buttons", "history").invoke("update");
	},
	
	jumpToStatic : function()
	{	
		if(this.state == this.STATE_FADING)
		{
			Admin.debug("Win /// jump to HIDDEN");
			
			Effect.Queues.get("UnwrittenWin").invoke("cancel");
			$("content").hide();
			this.setState(this.STATE_HIDDEN);
		}
		
		if(this.state == this.STATE_APPEARING)
		{
			Admin.debug("Win /// jump to ACTIVE");
			
			Effect.Queues.get("UnwrittenWin").invoke("cancel");
			$("content").show();
			this.setState(this.STATE_ACTIVE);
		}
		
		return this.state;
	},
	
	setState : function(n)
	{
		this.state = n;
		
		switch(this.state)
		{
			case this.STATE_HIDDEN : document.fire("Win:state_hidden");	break;
			
			case this.STATE_APPEARING : document.fire("Win:state_appearing"); break;
			
			case this.STATE_ACTIVE : document.fire("Win:state_active"); break;
			
			case this.STATE_FADING: document.fire("Win:state_fading"); break;
		}
					
		Admin.debug("Win /// State: " + n);
	},
	
	
	openTips : function()
	{
		if(this.tipsActive) return;
		
		this.tipsActive = true;
			
		document.fire("Win:tips_open");
		
		if(this.state = this.STATE_ACTIVE) $("tips").appear();
		else $("tips").show();
	},
	
	closeTips : function()
	{
		if(!this.tipsActive) return;
		
		this.tipsActive = false;
				
		document.fire("Win:tips_close");
		
		if(this.state = this.STATE_ACTIVE) $("tips").fade();
		else $("tips").hide();
	},
	
	
	
	
	
	
	
	
	
	
	
	
	setMsg : function(str, noEffect)
	{
		$("msg").update(str);
		
		if(!noEffect){
			// letop! restore color en endcolor moeten worden gespecificeerd,
			// anders zal er bij dubbelklik niet worden teruggevallen naar de juiste kleur
			options = {
				startcolor: "#FFFFCC",
				endcolor : "#FFCC33",
				restorecolor : true
			}
			new Effect.Highlight("msg", options);
		}
	},
	
	setTip : function(str, title)
	{
		
		if(typeof(str) != "string")
		{
			throw new Error("Functie setTip aangeroepen zonder argumenten");
		}
		
		this.setTipCont(str, title)
		
		// effecten toepassen
		$("tips").setStyle({color : "#000000", fontWeight : "normal"});
		
		
		// jazeker, ook aanroepen bij een andere state dan 3, anders kan de achtergrondkleur verkeerd worden!
		options = {
			startcolor: "#FFFFCC", 
			endcolor: "#E8EEF7", 
			restorecolor: true, 
			duration: this.state == 3 ? 1.0 : 0.0
		}
		this.tipContHighlight(options);
	},

	setError : function(str, title){
		
		if(typeof(str) != "string")
		{
			throw new Error("Functie setError aangeroepen zonder argumenten");
		}
		
		if(console)
		{
			console.error(title + "\n" + str)
			return;
		}
		
		// oude errors toevoegen
		str += $("tips").innerHTML;
		
		this.setTipCont(str, title);
		
		// effecten toepassen
		$("tips").setStyle({color : "#FFFFFF", fontWeight : "bold"});
		
		// jazeker, ook aanroepen bij een andere state dan 3, anders kan de achtergrondkleur verkeerd worden!
		options = {
			startcolor: "#FFFFFF", 
			endcolor: "#FF0000", 
			restorecolor: true, 
			duration: this.state == 3 ? 1.0 : 0.0
		}

		this.tipContHighlight(options);
	},
	
	setTipCont : function(str, title)
	{	
		title = title ? "<h2>" + title + "</h2>" : "";
		
		if(typeof(str) != "string")
		{
			throw new Error("Functie setTipCont aangeroepen zonder argumenten", "Error in setTipCont");
		}
		
		if(str.length == 0 && !title)
		{
			this.closeTips();
		}
		else
		{
			this.openTips();
			$("tips").update(title + str);
		}
	},
	
	/*openTips : function()
	{
		this.tipsActive = true;
		$("tips").show();
		
		document.fire("Win:tips_active");
		Maximize.maximize(); // via event?
	},
	
	closeTips : function()
	{
		this.tipsActive = false;
		$("tips").hide();
				
		document.fire("Win:tips_passive");
		Maximize.maximize(); // vai event?
	},*/

	tipContHighlight : function(options)
	{	
		Effect.Queues.get("UnwrittenTip").invoke("cancel");
		
		options = $H(options).merge( { queue : { scope : "UnwrittenTip"}});
		
		$("tips").highlight(options);	
	}
}
