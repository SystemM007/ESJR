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