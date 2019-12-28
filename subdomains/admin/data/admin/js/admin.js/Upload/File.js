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