<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| CSS Class
|--------------------------------------------------------------------------
*/
// regex
$config['fs_css']['regex'] = array(
	'variables' => '#\$([a-zA-Z0-9_-]+)#',
	// leave replace as last element to do the cleanup
	'replace' => array(
		'#\s0px#' => ' 0 ',
		'#[\;]{2,}#' => ';',
		'#[\r\n|\r|\n|\t|\f]#' => ' ',
		'#/\*(.*?)\*/#s' => '',
		'#(\,|\;|\:|\{|\}|\s)[ ]+#' => '$1',
		'#[ ]+(\,|\;|\:|\{|\}|px|\%)#' => '$1',
		'#url\([\'||"]?[^http://||data:](\.?\.\/)*(.*?)[\'||"]?\)#is' =>'url(\''.base_url().'$2\')',
	)
);
// default tags DO NOT REMOVE
$config['fs_css']['tags']['default']	= 	'<link rel="stylesheet" href="[file]" type="text/css" media="screen" [data]/>';
$config['fs_css']['tags']['print']		= 	'<link rel="stylesheet" href="[file]" type="text/css" media="print" [data]/>';
$config['fs_css']['tags']['lines'] 	= 	'<style type="text/css">'."\n\t".'[file]'."\n\t".'</style>';
// define your own tags