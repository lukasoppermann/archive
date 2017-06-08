<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| JS Class
|--------------------------------------------------------------------------
|
*/
// directories
$config['js']['dir'] = 'libs/js/';
$config['js']['cache_dir'] = 'libs/js/_cache/';
// compression settings
$config['js']['offset'] = 3600;
$config['js']['gzip'] = TRUE;
// regex
$config['js']['regex'] = array(
						'replace' => array(
							// '#\/\*.+?\*\/|\/\/.*(?=[\n\r])#' => '',
							// '#[\r\n|\r|\n|\t|\f]#' => ' ',
							// 	'#(\,|\;|\:|\{|\}|\s)[ ]+#' => '$1',
							// 	'#[ ]+(\,|\;|\:|\{|\}|px|\%)#' => '$1',
							// 	'#url\(\'[^http://||data:](\.?\.\/)*(.*?)\'\)#is' =>'url(\''.base_url().'$2\')'
						),
						// 'variables' => '#\[\$(.*)\]#' // produces an error
);
// define groups
$config['js']['groups'] = array('default');
// define tags
$config['js']['tags']['default'] = '<script type="text/javascript" src="[file]"></script>';
$config['js']['tags']['lines'] = 	'<script type="text/javascript">'."\n".'[file]'."\n".'</script>';
