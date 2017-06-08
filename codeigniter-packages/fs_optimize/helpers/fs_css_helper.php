<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System css helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/css
 */
// ------------------------------------------------------------------------
/**
 * css - returns all assigned css files
 *
 * @param string files 'file, file2'
 * @param boolean
 * @return string
 */
function css($group = 'default', $compress = null, $data = null)
{
	$CI = &get_instance();
	// check for compression argument
	if( $compress !== TRUE && $compress !== FALSE )
	{
		$config = $CI->config->item('compression');
		$compress = $config['css']['compression'];
	}
	// return files
	return $CI->fs_optimize->css->get($group, $compress, FALSE, $data);
}
// ------------------------------------------------------------------------
/**
 * css_link - returns all assigned css files
 *
 * @param string files 'file, file2'
 * @param boolean
 * @return string
 */
 function css_link($group = 'default', $compress = null, $link = TRUE)
 {
 	$CI = &get_instance();
 	// check for compression argument
 	if( $compress !== TRUE && $compress !== FALSE )
 	{
		$config = $CI->config->item('compression');
		$compress = $config['css']['compression'];
 	}
 	// return files
 	return $CI->fs_optimize->css->get($group, $compress, $link);
 }
// ------------------------------------------------------------------------
/**
 * css_add - add css files to class
 *
 * @param string | array
 * @param string
 */
function css_add($files, $group = 'default')
{
	$CI = &get_instance();
	// assign files
	$CI->fs_optimize->css->add($files, $group, $before = false);
}
// ------------------------------------------------------------------------
/**
 * css_add_lines - add css to class
 *
 * @param string
 * @param string
 */
function css_add_lines($lines, $group = NULL, $before = false)
{
	$CI = &get_instance();
	// assign lines
	$CI->fs_optimize->css->add_lines($lines, $group, $before = false);
}
// ------------------------------------------------------------------------
/**
 * css_variables - add css var to class
 * @param array('var_name' => 'value')
 */
function css_variables($variables)
{
	$CI = &get_instance();
	// assign variables
	$CI->fs_optimize->css->variables($variables);
}
/* End of file css_helper.php */
/* Location: ./system/helpers/css_helper.php */