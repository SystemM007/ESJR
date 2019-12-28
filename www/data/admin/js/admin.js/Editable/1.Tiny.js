Editable.Tiny = Class.create(Editable.Abstract, 
{
	tinySettings :{},
	
	initialize : function($super, objectID, options)
	{
		$super(objectID, options);
		
		this.tinySettings = Object.extend({
			mode : "exact",
			theme : "advanced",
			//language : "en",
			plugins : "contextmenu,fullscreen,inlinepopups,paste,safari,media",
			
			// http://wiki.moxiecode.com/index.php/TinyMCE:Control_reference	
			theme_advanced_buttons1 : "bold, italic, underline, strikethrough, sub, sup, separator, justifyleft, justifycenter, justifyright, justifyfull, separator, bullist, numlist, outdent, indent, separator, formatselect, styleselect, forecolor",
			theme_advanced_buttons2 : "undo, redo, separator, cut, copy, paste, pastetext, pasteword, removeformat, cleanup, separator, separator, link, unlink, anchor, image, media, charmap, separator, help, code, fullscreen ",
			theme_advanced_buttons3 : "",
			
			theme_advanced_blockformats : "p,h1,h2,h3",

			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_path_location : "none",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resize_horizontal : false,
			theme_advanced_resizing : true,

		//	height: '220',

			extended_valid_elements : "a[name|href|target|title|rel|onclick|style],img[class|src|alt=|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],span[class|align|style]",
			
			external_image_list_url : Admin.adminUri + "function/ImageList",
			external_link_list_url : Admin.adminUri + "function/LinkList",
			content_css : Admin.contentCss,
			theme_advanced_path : false,

			relative_urls : false,
			convert_urls : false, // true = default
			
			flash_wmode : "transparent",
			flash_quality : "high",
			flash_menu : "false",
			
			niets : null
		}, (options.tinySettings || {}) );
	},
	
	fullScreenContainer : null,
		
	Editor : {},
	
	getEditorContent : function()
	{
		try
		{
			tinyContent = this.Editor.getContent();
		}
		catch(e){Admin.c(e)};
		
		return tinyContent;
	},
	
	createEditorElm : function()
	{

	},
		
	convertElement : function()
	{
		
		try
		{
			this.Editor = new tinymce.Editor(this.originalElm.identify(), this.tinySettings);
			
			this.Editor.render();
	
		}
		catch(e){Admin.c(e)};
	},
	
	revertElement : function(cancel)
	{
		try
		{		
			if(cancel)
			{
				// inhoud uit oorspronkelijk element laden
				this.Editor.load();
			}
			else
			{
				// Editor Content wegschrijven naar div
				this.Editor.save();
			}
								
			this.Editor.remove();
		}
		
		catch(e){Admin.c(e)};
	}
});
