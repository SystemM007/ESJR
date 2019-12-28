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