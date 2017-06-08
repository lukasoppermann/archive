<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System Routing Helper
 *
 * @package		Form&System
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/routing
 */
// --------------------------------------------------------------------
/**
 * fs_write_route - writes routing file from array
 *
 * @param string
 * @param array
 * @return boolean | string
 */
function fs_write_route( $routes, $filename = 'fs_dynamic_routes', $dir = null )
{
	// check for dir, if FALSE set automatically 
	if( $dir == null || $dir == false )
	{
		$dir = APPPATH.'config/';
	}
	// check if dir is writable
	if( !is_writable( $dir ) == TRUE )
	{
		log_message('error', 'Function fs_write_route: '.$dir.' is not writable.');
		return FALSE;
	}
	// check if filename is given
	if( $filename == null )
	{
		log_message('error', 'Function fs_write_route: Filename missing.');
		return FALSE;
	}
	// check if arguments are given
	if( !isset($routes) || !is_array($routes) )
	{
		log_message('error', 'Function fs_write_route: Routes are missing or not correctly specified as an array.');
		return FALSE;
	}
	// loop through array
	foreach( $routes as $to => $from )
	{
		$file[] = "\t// Routes to ".$to;
		if( is_array($from) )
		{
			foreach($from as $f)
			{
				$file[] = "\t".'$route[\''.$f.'\'] = "'.$to.'";';	
			}
		}
		else
		{
			$file[] = "\t".'$route[\''.$from.'\'] = "'.$to.'";';
		}
	}
	// prepare file
	$filename 		= rtrim($dir,'/').'/'.$filename.".php";
	// check if file exists
	if( file_exists($filename) )
	{
		// set permissions
		chmod($filename, 0755);
		// delete file
		unlink($filename);
	}
	// create content for file
	$file_content 	= '<?php  if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');'."\n// Dynamic Routes created via Form&System \n\n";
	// check if data is present
	if( isset($file) && is_array($file) )
	{
		$file_content .= implode("\n",$file);
	}
	// write file
	$file_handle = fopen($filename, 'w');
	fwrite($file_handle, $file_content);
	fclose($file_handle);
	// set permissions
	chmod($filename, 0755);
	// return true if file is correctly created
	if( is_readable($filename) )
	{
		return TRUE;
	}
}
/* End of file routing_helper.php */
/* Location: ./system/helpers/routing_helper.php */