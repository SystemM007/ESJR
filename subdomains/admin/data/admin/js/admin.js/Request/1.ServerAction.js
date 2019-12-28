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
