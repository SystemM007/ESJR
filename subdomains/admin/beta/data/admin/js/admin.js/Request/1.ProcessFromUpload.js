Request.ProcessFromUpload = Class.create({

	afterReq : Prototype.emptyFunction,
	newPage : false,
	
	initialize : Request.Abstract.prototype.processResponseData
	
})