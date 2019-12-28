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