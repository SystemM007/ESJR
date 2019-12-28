// JavaScript Document

Upload.Input = Class.create({
	
	SwfUpload : {},
	button : null,
	multiple_files : false,
	cookies : "", // hier worden de cookies geplaats die moeten worden verzonden in plaats van
		// de cookies uit de browser
	
	initialize : function(id, options)
	{
		Upload.InputRegister[id] = this;
		
		this.multiple_files = options.multiple_files;
		delete options.multiple_files;
		
		Cookies = options.Cookies;
		delete options.Cookies;
		
		console.log("id", id);
		
		swfuOptions = $H({
			// kan worden overschreven
			file_types : "*.*", 
			file_types_description: "",
			file_size_limit : 0,
			file_queue_limit : 0,
			// post
			post_params : {
				"instanceId" : Admin.instanceId,
				"lifeId" : options["lifeId"],
				"action" : "upload",
				"Cookies" : Cookies,
			},
			
			// standaard
			upload_url : Admin.adminUri + "upload/",
			file_post_name : "Filedata",
			use_query_string : false,
			requeue_on_error : false,
			file_upload_limit : 0,
			flash_url : Admin.servicesUri + "swfupload-2.2-lvdg/swfupload.swf",
			debug : true,
			
			//swfupload_loaded_handler : this.swfuploadLoaded.bind(this),
			file_dialog_start_handler : this.fileDialogStart.bind(this),
			file_queued_handler : this.fileQueued.bind(this),
			file_queue_error_handler : this.fileQueueError.bind(this),
			file_dialog_complete_handler : this.fileDialogComplete.bind(this),
			upload_start_handler : this.uploadStart.bind(this),
			upload_progress_handler : this.uploadProgress.bind(this),
			upload_error_handler : this.uploadError.bind(this),
			upload_success_handler : this.uploadSuccess.bind(this),
			upload_complete_handler : this.uploadComplete.bind(this),
			//debug_handler : this.debug.bind(this), // niet nodig, SWFUploadLvdg stuurt debug info netjes naar firebug
			
			button_placeholder_id : id, 
			//button_image_url : "http://www.swfupload.org/button_sprite.png", 
			button_width : 160, 
			button_height : 25, 
			button_text : ("<span class='text'>" + options["buttontext"] + "</span>"), 
			button_text_style : ".text {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; margin-left:25px; margin-top:5px; }", 
			button_text_left_padding : 3, 
			button_text_top_padding : 2, 
			button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES, 
			button_disabled : false, 
			button_cursor : SWFUpload.CURSOR.HAND, 
			button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT, 
			
			custom_settings : {
				target : $("swfUploadContainer")
			}
			
		}).merge(options).toObject();
			
		delete id; delete options; delete Cookie;
		
		
		this.SwfUpload = new SWFUpload(swfuOptions);
	},
	
/*	buttonClick : function(event)
	{
		this.startUpload();	
	},
*/	
	openDialog : function()
	{	
		if(this.multiple_files)
		{
			this.SwfUpload.selectFiles();
		}
		else
		{
			this.SwfUpload.selectFile();
		}
	},
	
/*	swfuploadLoaded : function()
	{
	},
*/	
	fileDialogStart : function()
	{
		console.info("dialog start");
		document.fire("Upload.Input:fileDialogStart");	
	},
	
	fileQueued : function(File)
	{
		new Upload.File(this, File);
	},
	
	fileQueueError : function(File, errorCode, message)
	{
		Upload.FileRegister[File.id].queueError(errorCode, message);
	},
	
	fileDialogComplete : function(selected, queued)
	{	
		this.SwfUpload.startUpload();
		document.fire("Upload.Input:fileDialogComplete", {
			selected : selected,
			queued : queued
		});	
	},
	
	uploadStart : function(File)
	{
		Upload.FileRegister[File.id].uploadStart();
		document.fire("Upload.Input:uploadStart");	
	},
	
	uploadProgress : function(File, bytesComplete, bytesTotal)
	{
		Upload.FileRegister[File.id].uploadProgress( bytesComplete, bytesTotal);
	},
	
	uploadError : function(File, errorCode, message)
	{
		Upload.FileRegister[File.id].uploadError(errorCode, message);
	},
	
	uploadSuccess : function(File, serverData)
	{
		if(!serverData) console.error("Geen server data!");
		
		Upload.FileRegister[File.id].uploadSuccess(serverData);
		
		// hoi hoi hoi!
		new Prosessor(serverData.evalJSON());
	},
	
	uploadComplete : function(File)
	{
		Upload.FileRegister[File.id].uploadComplete();
		document.fire("Upload.Input:uploadFinished");
		
		if(this.SwfUpload.getStats().files_queued)
		{
			this.SwfUpload.startUpload();
		}
	},
	
	debug : function(message)
	{
		console.log(message);
	}
});