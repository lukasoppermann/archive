<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Meta Data Class config
|--------------------------------------------------------------------------
|
| Link Tags for different favorite icon types
*/
// defaults for browser
$config['favorite_tags']['browser'] 	= '<link rel="shortcut icon" href="[file]" type="image/x-icon" />';
$config['favorite_tags']['microsoft']	= '<link rel="icon" href="[file]" type="image/vnd.microsoft.icon" />';
// iphone and ipad
$config['favorite_tags']['iphone'] 		= '<link rel="apple-touch-icon-precomposed" href="[file]" />';
$config['favorite_tags']['iphone@2x'] 	= '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="[file]" />';
$config['favorite_tags']['ipad'] 		= '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="[file]" />';	
$config['favorite_tags']['ipad@2x'] 	= '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="[file]" />';

/*
| Extension to be used as a suffix for favorite icon image if only one name is given, e.g. favicon
| would be turned into e.g. favicon-iphone.png
*/
// defaults for browser
$config['favorite_images']['browser']		= '';
$config['favorite_images']['microsoft']		= '';
// iphone and ipad
$config['favorite_images']['iphone'] 		= '-iphone';
$config['favorite_images']['ipad'] 			= '-ipad';
$config['favorite_images']['iphone@2x']	= '-iphone@2x';
$config['favorite_images']['ipad@2x']		= '-ipad@2x';

/* End of file fs_meta.php */
/* Location: ./application/config/fs_meta.php */