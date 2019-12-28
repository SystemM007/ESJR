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
