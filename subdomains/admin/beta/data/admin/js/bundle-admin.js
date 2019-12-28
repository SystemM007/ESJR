

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Admin.js
//

/*
* EVENTS
* page_close : bij sluiten van pagina
* page_ready : inhoud geze
* page_loaded : ingefade

* request_start : starten van request
* request_complete : duh
*/

var Admin = {

	authStr : "",
	instanceId : 0,
	adminUri : "",
	servicesUri : "",
	debugEnabled : false,
	
	submitAction : Prototype.emptyFunction,

	init : function ()
	{
		Form.init();
		Maximize.init();
		RequestReg.init();
		Upload.Interface.init();
		Win.init();
		
		new Request.Page("Login");
	},
	
	debug : function(message)
	{
		if(Admin.debugEnabled) console.info(message);
	},
	
	catchedError : function(e)
	{
		if(console && console.trace && console.error)
		{
			console.trace();
			console.error(e)
			return;
		}
		/*
		if(e && e.name && e.message & e.lineNumber)
		{
			str = e.name + "<br />";
			str += e.message.replace("\\n", "<br />").replace("\\\\", "\\") + "<br />";
			str += "line: " + e.lineNumber + "<br />";
		}
		else
		{
			str = "" + e;
		}
		if(e && e.fileName)
		{
			str += "filename: " + e.fileName + "<br />";
		}
		if(e && e.stack)
		{
			str += e.stack;
		}
		
		try
		{
			if(Win.state != 3)
			{
				if(Win.jumpToStatic() == 1)
				{
					Win.show();
				}
			}
			
			Win.setError(str, "Catched");
		}
		catch(e)
		{
			alert(str.stripTags());			
		};*/
	}
};
	
// alias
Admin.c = Admin.catchedError;

Event.observe(window, "load", Admin.init.bindAsEventListener(Admin));

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/ClickTable.js
//

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

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Editable.js
//

Editable = {};

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/EditableReg.js
//

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

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Form.js
//

Form = {

	submitAction : Prototype.emptyFunction,
	
	init : function()
	{
		$("AdminForm").observe("submit", this.onSubmit.bindAsEventListener(this));
		document.observe("admin:page_close", this.reset.bindAsEventListener(this));
		this.reset();
	},
	
	reset : function()
	{
		this.setOnSubmit(Prototype.emptyFunction);
	},
	
	setOnSubmit : function(submitAction)
	{
		this.submitAction = submitAction;
	},
	
	onSubmit : function(event)
	{
		event.stop();
		
		console.info("Form submit", this);
		
		this.submitAction();
	}
}

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Maximize.js
//

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


//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Prosessor.js
//

// JavaScript Document

Prosessor = Class.create({
	
	/*
	* Events
		- start
		- finished
		- finished_newpage
	*/
	
	initialize : function(data, newPage)
	{
		document.fire("Prosessor:start");
		if(newPage)	document.fire("Prosessor:start_newpage");
		
		try{
	
			EditableReg.feedback(data.editableFeedback);
			
			this.pageFields(data.pageFields);
			this.template(data.template);
			this.templateFields(data.templateFields);
			this.childList(data.childList);
		
			EditableReg.makeEditables(data.editables);
			
			$A(data.uploads).each(function(upload){new Upload.Input(upload.id, upload.options)} );
			
			if(data.evalJS)
			{
				eval(data.evalJS);
			}
			
			if(data.onSubmit)
			{
				Form.setOnSubmit(eval.bind(window, data.onSubmit));
			}
			
		}
		catch(e)
		{
			Admin.c(e)
		};
		
		document.fire("Prosessor:finished");
		if(newPage)	document.fire("Prosessor:finished_newpage");
	},
	
	pageFields : function (pageFields)
	{
		if(!pageFields)
		{
			return;
		}
		
		if(pageFields.error)
		{
			console.error("pageFields.error is buiten gebruik");
			console.error(pageFields.error);
		}
		
		if(pageFields.title)
		{
			$("titleText").update(pageFields.title);
		}
		
		if(pageFields.pageNumbers)
		{
			$("history").update(pageFields.pageNumbers);
		}
		
		if(pageFields.msg)
		{
			$("msg").update(pageFields.msg);
		}
		
		if(pageFields.buttons)
		{
			$("buttons").update(pageFields.buttons);
		}
		
		if(pageFields.tip)
		{
			Win.openTips();
			$("tips").update(pageFields.tip);
		}
	},
	
	template : function(template)
	{
		if(!template) return;

		if(Win.state != Win.STATE_HIDDEN)
		{
			throw new Error("template state != STATE_HIDDEN (1), state : "+ Win.state);
		};
		$("text").update(template);
	},
	
	templateFields : function (templateFields)
	{
		// belangrijk om goed te controleren. Wanneer in PHP dit een lege array was, 
		// wordt het in JSON [], in plaats van {}, daarom maakt Prototype er een Array van
		// wat nogal mis gaan als dat door een $H() heen wordt gehaald.

		if(!templateFields) return;
		
		$H(templateFields).each(function(pair)
		{
			field = pair.key;
			html = pair.value;
			
			if(!$(field)) throw new Error(new Error( "Template field '"+ field +"' werd niet gevonden!") );
		
			$(field).update(html);

			if(Win.state == Win.STATE_HIDDEN)
			{
				options = {
					startcolor: "#FFFFCC",
					restorecolor : false
				}
				$(field).highlight(options);
			};
		});
	},
	
	childList : function(childList)
	{
		$H(childList).each(function(list)
		{
			new ClickTable(list.key, list.value);
		});
	}
});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Request.js
//

Request = {};

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/RequestReg.js
//

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


//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Upload.js
//

var Upload = {
	
	InputRegister : [],
	
	FileRegister : []
	
};

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Win.js
//

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


//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/dragdropExtention.js
//

/*
Sortable.mark = function (dropon, position)
{
	dropon.addClassName("Sortable_drop-" + position);
}

Sortable.unmark = function(dropon)
{
	dropon.removeClassName("Sortable_drop-before").removeClassName("Sortable_drop-after");
}
*/

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/ClickTable/Row.js
//

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

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Editable/0.Abstract.js
//

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

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Editable/1.AbstractText.js
//

Editable.AbstractText = Class.create(Editable.Abstract, 
{
	createEditorElm : Prototype.emptyFunction(),
		
	getEditorContent : function()
	{	
		//return escape(this.editorElm.value);
		return this.editorElm.value;
	},
	
	convertElement : function(doNotFocus)
	{	
		try
		{	
			// helaas nog niet geheel fijlloos
			//Position.clone(this.originalElm, this.editorElm); 
			
			this.originalElm.replaceElement(this.editorElm);
							
			if(!doNotFocus) this.editorElm.focus();
			this.editorElm.select()

		}
		catch(e){Admin.c(e)};
	},
	
	revertElement : function (cancel)
	{	
		try
		{
			this.editorElm.parentNode.replaceChild(this.originalElm, this.editorElm);
			
			if(!cancel)	this.originalElm.innerHTML = this.editorElm.value.escapeHTML();
		}
		catch(e){Admin.c(e)};
	},
	
	getStyleClassAttr : function(elm)
	{	
		attr = new Object();
		
		className = elm.readAttribute("class");
		attr.className = className ? className : "";
		
		style = elm.readAttribute("style");
		attr.style = style ? style : "";
	
		return attr;
	}
});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Editable/1.Tiny.js
//

Editable.Tiny = Class.create(Editable.Abstract, 
{
	tinySettings :{},
	
	initialize : function($super, objectID, options)
	{
		$super(objectID, options);
		
		this.tinySettings = Object.extend({
			mode : "exact",
			theme : "advanced",
			//language : "en",
			plugins : "contextmenu,fullscreen,inlinepopups,paste,safari,media",
			
			// http://wiki.moxiecode.com/index.php/TinyMCE:Control_reference	
			theme_advanced_buttons1 : "bold, italic, underline, strikethrough, sub, sup, separator, justifyleft, justifycenter, justifyright, justifyfull, separator, bullist, numlist, outdent, indent, separator, formatselect, styleselect, forecolor",
			theme_advanced_buttons2 : "undo, redo, separator, cut, copy, paste, pastetext, pasteword, removeformat, cleanup, separator, separator, link, unlink, anchor, image, media, charmap, separator, help, code, fullscreen ",
			theme_advanced_buttons3 : "",
			
			theme_advanced_blockformats : "p,h1,h2,h3",

			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_path_location : "none",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resize_horizontal : false,
			theme_advanced_resizing : true,

		//	height: '220',

			extended_valid_elements : "a[name|href|target|title|rel|onclick|style],img[class|src|alt=|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],span[class|align|style]",
			
			external_image_list_url : Admin.adminUri + "function/ImageList",
			external_link_list_url : Admin.adminUri + "function/LinkList",
			content_css : Admin.contentCss,
			theme_advanced_path : false,

			relative_urls : false,
			convert_urls : false, // true = default
			
			flash_wmode : "transparent",
			flash_quality : "high",
			flash_menu : "false",
			
			niets : null
		}, (options.tinySettings || {}) );
	},
	
	fullScreenContainer : null,
		
	Editor : {},
	
	getEditorContent : function()
	{
		try
		{
			tinyContent = this.Editor.getContent();
		}
		catch(e){Admin.c(e)};
		
		return tinyContent;
	},
	
	createEditorElm : function()
	{

	},
		
	convertElement : function()
	{
		
		try
		{
			this.Editor = new tinymce.Editor(this.originalElm.identify(), this.tinySettings);
			
			this.Editor.render();
	
		}
		catch(e){Admin.c(e)};
	},
	
	revertElement : function(cancel)
	{
		try
		{		
			if(cancel)
			{
				// inhoud uit oorspronkelijk element laden
				this.Editor.load();
			}
			else
			{
				// Editor Content wegschrijven naar div
				this.Editor.save();
			}
								
			this.Editor.remove();
		}
		
		catch(e){Admin.c(e)};
	}
});


//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Editable/2.Select.js
//

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

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Editable/2.Textarea.js
//

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

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Editable/2.Textfield.js
//

Editable.Textfield = Class.create(Editable.AbstractText,
{
	createEditorElm : function()
	{
		
		try
		{	
			elm = this.originalElm;
			
			attr = Object.extend(this.getStyleClassAttr(elm), {
				type : "text",
				value : elm.innerHTML.unescapeHTML()
			});
			
			this.editorElm = Builder.node("input", attr);
								
			this.editorElm = $(this.editorElm);
			
		}
				
		catch(e){Admin.c(e)};
		
	}
});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Editable/2.Textpasswd.js
//

Editable.Textpasswd= Class.create(Editable.AbstractText,
{
	
	afterInit : function()
	{
		this.originalElm.update("***********");
	},
	
	createEditorElm : function()
	{
		try
		{	
			attr = Object.extend(this.getStyleClassAttr(this.originalElm), {
				type : "password"
			});
			
			this.editorElm = Builder.node("input", attr, "");
								
			this.editorElm = $(this.editorElm);
			
		}
				
		catch(e){Admin.c(e)};
		
	},
	
	// OVERSCHRIJFT EditableText.getEditdorContent!
	// IS HIER EEN 	encodeURIComponent NODIG?
	getEditorContent : function()
	{	
		return hex_md5(this.editorElm.value);	
	},
	
	// OVERSCHRIJFT EditableText.revertElement!
	revertElement : function (cancel)
	{	
		try
		{
			this.editorElm.parentNode.replaceChild(this.originalElm, this.editorElm);
		}
		catch(e){Admin.c(e)};
	}

});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Request/0.Abstract.js
//

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

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Request/1.Action.js
//

Request.Action= Class.create(Request.Abstract,
{
	uriAddition : "action/",

	initialize : function($super, lifeId, action, postData, options)
	{
		postQuery = $H({
			"lifeId" : lifeId,
			"action" : action,
			"query" : $H(postData).toJSON()
		});
		
		$super(postQuery, options);
	}
});


//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Request/1.Core.js
//

Request.Core= Class.create(Request.Abstract, {

	uriAddition : "core/",

	initialize : function($super, ID, postData, options)
	{
		postQuery = $H({
			"query" : $H(postData).toJSON(),
			"ID" : ID
		});
			
		pageOptions = $H(options).merge({newPage:true});
		
		$super(postQuery, pageOptions);
	}
});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Request/1.History.js
//

Request.History= Class.create(Request.Abstract, {

	uriAddition : "history/",

	initialize : function($super, offset, postData, options)
	{
		postQuery = $H({
			"query" : $H(postData).toJSON(),
			"offset" : offset
		});
			
		pageOptions = $H(options).merge({newPage:true});
		
		$super(postQuery, pageOptions);
	}
});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Request/1.Page.js
//

Request.Page= Class.create(Request.Abstract, {

	uriAddition : "page/",

	initialize : function($super, pageName, postData, options)
	{
		postQuery = $H({
			"query" : $H(postData).toJSON(),
			"page" : pageName
		});
			
		pageOptions = $H(options).merge({newPage:true});
		
		$super(postQuery, pageOptions);
	}
});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Request/1.ProcessFromUpload.js
//

Request.ProcessFromUpload = Class.create({

	afterReq : Prototype.emptyFunction,
	newPage : false,
	
	initialize : Request.Abstract.prototype.processResponseData
	
})

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Request/1.ServerAction.js
//

Request.ServerAction= Class.create(Request.Abstract,
{
	uriAddition : "serveraction/",

	initialize : function($super, lifeId, options)
	{
		postQuery = $H({
			"lifeId" : lifeId,
		});
		
		$super(postQuery, options);
	}
});


//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Upload/File.js
//

Upload.File = Class.create({
		
	Input : {},
	File : {},
	SwfUpload : {},
	
	progessLine : {},
	container : {},
	
	uploadStarted : false,
	
	initialize : function(Input, File)
	{
		this.Input = Input;
		this.File = File;
		this.SwfUpload = Input.SwfUpload;
		
		Upload.FileRegister[File.id] = this;
	
		this.createQueueHtml();
	},
	
	createQueueHtml : function()
	{
		cancel = new Element("span").addClassName("upload_cancel");
		cancel.observe("click", this.cancel.bindAsEventListener(this));
		
		uploadName = new Element("h2").update(this.File.name);
		content = new Element("div").update("<em>wachten...</em>");
		
		container = new Element("div");
		container.insert(cancel).insert(uploadName).insert(content);
		
		this.container = container;
		this.content = content;
		this.cancel = cancel;
		
		
		// ok, het is misschien wat overdreven, alles met evens, maar ook wel lekker...
		document.fire("Upload.File:createQueueHtml", {html : this.container});
		
		this.container.highlight();
	},
	
	cancel : function()
	{
		this.SwfUpload.cancelUpload(this.File.id);
		
		/*
		* als upload al wel gestart is wordt er automatisch
		* uploadComplete aangeroepen
		* LETOP: uploadError wordt sowieso aangeroepen
		* daarom hoeft er hier geen disappear of melding te worden gegeven
		* in die situatie
		*/
		if(!this.uploadStarted)
		{
			this.disappear(true);
		}
	},
	
	setError : function(msg)
	{
		this.content.update("<strong>" + msg + "</strong>");
	},
	
	disappear : function(doDelay)
	{
		new Effect.Parallel(
			[new Effect.BlindUp(this.container), new Effect.Fade(this.container)],
			{
				afterFinish : this.remove.bind(this),
				delay : doDelay ? 4 : 0
			}
		);
	},
	
	remove : function()
	{
		this.container.remove();
		
		Upload.FileRegister[this.File.id] = null;
		
	},
	
	/* ------------ deze functies worden aangeroepen vanuit de Input ----------- */
	
	queueError : function(errorCode, message)
	{
		switch (errorCode) 
		{
			case SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED:
				this.setError("Wachtrij vol");
			break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				this.setError("Leesfout");
			break;
	
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				this.setError("Te groot");
			break;
			
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				this.setError("Onjuist type");
			break;
			
			default:
				this.setError("Fout <br />" + message);
			break;
		}
		
		this.container.highlight();
		
		this.disappear(true); 
	},
	
	uploadError : function(errorCode, message)
	{
		switch (errorCode) {
			case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
				this.setError("Geannuleerd");
			break;
			
			case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
				this.setError("Gestopt");
			break;
			
			case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
				this.setError("Maximum bereikt");
			break;
			
			default:
				this.setError("Fout<br />" + message);
			break;
		}
		
		// GEEN DISSAPEAR!
		// hierna wordt vanzelf uploadComplete aangeroepen!
	},
	
	uploadStart : function()
	{
		this.uploadStarted = true;
		
		this.progressText = new Element("em").update("uploading...");
		
		progressBar = new Element("div").addClassName("upload_progressBar").update();
		progressLine = new Element("div").addClassName("upload_progressLine").update();
		
		this.content.update("").insert(this.progressText).insert(progressBar.insert(progressLine));
		this.progressLine = progressLine;
	},
	
	uploadProgress : function( bytesComplete, bytesTotal)
	{
		percent = Math.round(bytesComplete / bytesTotal * 100) + "%";
		this.progressLine.setStyle({width : percent});
		this.progressText.update("uploading..." + percent);
		
		delete percent;
	},
	
	uploadSuccess : function(serverData)
	{
		this.uploadProgress(1,1);
		this.cancel.remove();
		this.progressText.update("Upload voltooid");
	},
	
	uploadComplete : function()
	{
		this.disappear(true);
	}
});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Upload/Input.js
//

// JavaScript Document

Upload.Input = Class.create({
	
	SwfUpload : {},
	button : null,
	multiple_files : false,
	cookies : "", // hier worden de cookies geplaats die moeten worden verzonden in plaats van
		// de cookies uit de browser
	
	initialize : function(id, options)
	{
		Upload.InputRegister[id] = this;
		
		this.multiple_files = options.multiple_files;
		delete options.multiple_files;
		
		Cookies = options.Cookies;
		delete options.Cookies;
		
		console.log("id", id);
		
		swfuOptions = $H({
			// kan worden overschreven
			file_types : "*.*", 
			file_types_description: "",
			file_size_limit : 0,
			file_queue_limit : 0,
			// post
			post_params : {
				"instanceId" : Admin.instanceId,
				"lifeId" : options["lifeId"],
				"action" : "upload",
				"Cookies" : Cookies,
			},
			
			// standaard
			upload_url : Admin.adminUri + "upload/",
			file_post_name : "Filedata",
			use_query_string : false,
			requeue_on_error : false,
			file_upload_limit : 0,
			flash_url : Admin.servicesUri + "swfupload-2.2-lvdg/swfupload.swf",
			debug : true,
			
			//swfupload_loaded_handler : this.swfuploadLoaded.bind(this),
			file_dialog_start_handler : this.fileDialogStart.bind(this),
			file_queued_handler : this.fileQueued.bind(this),
			file_queue_error_handler : this.fileQueueError.bind(this),
			file_dialog_complete_handler : this.fileDialogComplete.bind(this),
			upload_start_handler : this.uploadStart.bind(this),
			upload_progress_handler : this.uploadProgress.bind(this),
			upload_error_handler : this.uploadError.bind(this),
			upload_success_handler : this.uploadSuccess.bind(this),
			upload_complete_handler : this.uploadComplete.bind(this),
			//debug_handler : this.debug.bind(this), // niet nodig, SWFUploadLvdg stuurt debug info netjes naar firebug
			
			button_placeholder_id : id, 
			//button_image_url : "http://www.swfupload.org/button_sprite.png", 
			button_width : 160, 
			button_height : 25, 
			button_text : ("<span class='text'>" + options["buttontext"] + "</span>"), 
			button_text_style : ".text {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; margin-left:25px; margin-top:5px; }", 
			button_text_left_padding : 3, 
			button_text_top_padding : 2, 
			button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES, 
			button_disabled : false, 
			button_cursor : SWFUpload.CURSOR.HAND, 
			button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT, 
			
			custom_settings : {
				target : $("swfUploadContainer")
			}
			
		}).merge(options).toObject();
			
		delete id; delete options; delete Cookie;
		
		
		this.SwfUpload = new SWFUpload(swfuOptions);
	},
	
/*	buttonClick : function(event)
	{
		this.startUpload();	
	},
*/	
	openDialog : function()
	{	
		if(this.multiple_files)
		{
			this.SwfUpload.selectFiles();
		}
		else
		{
			this.SwfUpload.selectFile();
		}
	},
	
/*	swfuploadLoaded : function()
	{
	},
*/	
	fileDialogStart : function()
	{
		console.info("dialog start");
		document.fire("Upload.Input:fileDialogStart");	
	},
	
	fileQueued : function(File)
	{
		new Upload.File(this, File);
	},
	
	fileQueueError : function(File, errorCode, message)
	{
		Upload.FileRegister[File.id].queueError(errorCode, message);
	},
	
	fileDialogComplete : function(selected, queued)
	{	
		this.SwfUpload.startUpload();
		document.fire("Upload.Input:fileDialogComplete", {
			selected : selected,
			queued : queued
		});	
	},
	
	uploadStart : function(File)
	{
		Upload.FileRegister[File.id].uploadStart();
		document.fire("Upload.Input:uploadStart");	
	},
	
	uploadProgress : function(File, bytesComplete, bytesTotal)
	{
		Upload.FileRegister[File.id].uploadProgress( bytesComplete, bytesTotal);
	},
	
	uploadError : function(File, errorCode, message)
	{
		Upload.FileRegister[File.id].uploadError(errorCode, message);
	},
	
	uploadSuccess : function(File, serverData)
	{
		if(!serverData) console.error("Geen server data!");
		
		Upload.FileRegister[File.id].uploadSuccess(serverData);
		
		// hoi hoi hoi!
		new Prosessor(serverData.evalJSON());
	},
	
	uploadComplete : function(File)
	{
		Upload.FileRegister[File.id].uploadComplete();
		document.fire("Upload.Input:uploadFinished");
		
		if(this.SwfUpload.getStats().files_queued)
		{
			this.SwfUpload.startUpload();
		}
	},
	
	debug : function(message)
	{
		console.log(message);
	}
});

//
// /Users/lvdgraaff/Workspace/Unwritten/unwritten-beta/admin/data/js/admin.js/Upload/Interface.js
//

Upload.Interface = {
	
	tipsOpen : false,
	
	queued : 0,
	
	init : function ()
	{
		this.openBound = this.open.bindAsEventListener(this);
		
		document.observe("Upload.Input:fileDialogStart", this.open.bindAsEventListener(this));
		document.observe("Upload.Input:fileDialogComplete", this.close.bindAsEventListener(this));
		
		document.observe("Upload.Input:uploadStart", this.uploadStart.bindAsEventListener(this));
		document.observe("Upload.Input:uploadFinished", this.uploadFinished.bindAsEventListener(this));
		document.observe("Upload.File:createQueueHtml", this.insert.bindAsEventListener(this));
		document.observe("Win:state_fading", this.reset.bindAsEventListener(this));
	},
	
	open : function()
	{
		if(!this.tipsOpen)
		{
			Win.openTips();
			$("tips").update("<h2>Uploads</h2>");
			this.tipsOpen = true;
		}
	},
	
	close : function (event)
	{
		if(!event.memo.queued)
		{
			Win.closeTips();
			$("tips").update();
			this.tipsOpen = false;
		}
		else
		{
			this.queued += event.memo.queued;
		}
	},
	
	reset : function()
	{
		this.tipsOpen = false;
	},
	
	uploadStart : function()
	{
		// openen tips is al gedaan bij open dialoog
	},
	
	uploadFinished : function()
	{
		this.queued--;
		
		if(this.queued == 0)
		{
			Win.closeTips();
			this.tipsOpen = false;
		}
	},
	
	insert : function(event)
	{
		$("tips").insert(event.memo.html);
	}
}