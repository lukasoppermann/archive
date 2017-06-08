<?
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Ajax_upload extends MY_Controller {
	
	var $data = null;
	
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	// index 
	function index()
	{
		// echo '<link rel="stylesheet" type="text/css" href='.base_url().'/libs/css/uploader-0.0.1.css" media="screen" />';
		echo '<style>#file_uploadQueue div, #file_uploadQueue span, #file_uploadQueue p{
			display: none;
			font-size: 10px !important;
			font-family: arial !important;
			float: left !important;
			display: block;
		}
		#notice{
			padding: 5px 0;
			display: block;
			font-size: 10px;
			font-family: arial;	
		}</style>';
		echo '<script type="text/javascript" src="'.base_url().'/libs/js/jquery-1.5.1.min.js"></script>';
		echo '<script type="text/javascript" src="'.base_url().'/uploadify/swfobject.js"></script>';
		echo '<script type="text/javascript" src="'.base_url().'/uploadify/jquery.uploadify.v2.1.4.min.js"></script>';
		echo "<script type=\"text/javascript\">
		

		$(document).ready(function() {
			
			$('#file_upload').uploadify({
		    'uploader'  : '".base_url()."/uploadify/uploadify.swf',
		    'script'    : '".base_url()."/uploadify/uploadify.php',
		    'cancelImg' : '".base_url()."/uploadify/cancel.png',
		    'folder'    : '../../hygromatik/media/images',
		    'auto'      : true,
			'buttonText' : 'Datei hochladen',
		 	'fileExt'     : '*.jpg;*.gif;*.png',
		  	'fileDesc'    : 'Bilddatei',
			'onComplete' : function(event, ID, fileObj, response, data) {
		      	$('#notice').text(fileObj.name + ' erfolgreich hochgelanden.');
				$('#src').val('".$this->config->item('client_base_url')."media/images/' + fileObj.name);
		    },
			'onError' : function(event, ID, fileObj, response, data) {
		      $('#notice').text(errorObj.type + ' Error: ' + errorObj.info);
		    }		
		  });
		});
		</script>";
		// echo '<div id="fileUpload">You have a problem with your javascript</div>';
		echo '<input id="file_upload" name="file_upload" type="file" />';
		echo '<input type="hidden" name="src" id="src" value="">';
		echo '<div id="notice"></div>';
	}
	
	
}

