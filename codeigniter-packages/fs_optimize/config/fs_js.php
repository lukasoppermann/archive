<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| JS Class
|--------------------------------------------------------------------------
*/
// regex
$config['fs_js']['regex'] = array(
	'replace' => array(
		// '#\/\*.+?\*\/|\/\/.*(?=[\n\r])#' => '',
	)
);
// default tags DO NOT DELETE 
$config['fs_js']['tags']['default']	= '<script type="text/javascript" src="[file]" [data]></script>'; 
$config['fs_js']['tags']['lines'] 		= '<script type="text/javascript">'."\n".'[file]'."\n".'</script>';
// define your own tags