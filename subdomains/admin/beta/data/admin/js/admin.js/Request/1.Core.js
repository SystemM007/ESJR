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