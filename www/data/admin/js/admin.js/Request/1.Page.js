Request.Page= Class.create(Request.Abstract, {

	uriAddition : "page/",

	initialize : function($super, pageName, postData, options)
	{
		postQuery = $H({
			"query" : $H(postData).toJSON(),
			"page" : pageName
		});
			
		pageOptions = $H(options).merge({newPage:true});
		
		$super(postQuery, pageOptions);
	}
});