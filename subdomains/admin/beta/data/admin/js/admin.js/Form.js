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