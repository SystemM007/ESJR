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