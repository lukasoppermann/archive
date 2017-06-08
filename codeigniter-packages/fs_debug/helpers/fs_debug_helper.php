<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System fs_debug Library
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Debug
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/fsdebug
 */

// ------------------------------------------------------------------------
/**
 * fs_log - adds item to log
 *
 * @param var
 */
 function fs_log($item = '', $comment = null, $from = null)
 {
 	// get CI instance
 	$CI = &get_instance();
 	// if not from is set
 	if($from == null)
    {
        // get file from which function is called
        $file = debug_backtrace();
        // get just file basename
        $from = basename($file[0]['file']);
    }
    // log information with current time
 	$CI->fs_debug->log($item, $comment, $from);
 }
// ------------------------------------------------------------------------
/**
* fs_show_log - show log
*
* @param var
*/
function fs_show_log()
{
	// get CI instance
	$CI = &get_instance();
	// return log information
	return $CI->fs_debug->show_log();
}
// ------------------------------------------------------------------------
/**
* fs_benchmark_init - init benchmark
*
* @param var
*/
function fs_benchmark_init( $state = TRUE )
{
	// get CI instance
	$CI = &get_instance();
	// return log information
	return $CI->fs_debug->benchmark_init( $state );
}
// ------------------------------------------------------------------------
/**
* fs_benchmark - show benchmark bar
*
* @param var
*/
function fs_benchmark()
{
	// get CI instance
	$CI = &get_instance();
	// return log information
	return $CI->fs_debug->benchmark();
}
// ------------------------------------------------------------------------
/**
* fs_debug_print_js - prints js defined inside fs_debug class
*
* @param var
*/
function fs_debug_print_js()
{
	// get CI instance
	$CI = &get_instance();
	// return log information
	return $CI->fs_debug->print_js();
}
// ------------------------------------------------------------------------
/**
* fs_debug_print_css - prints css defined inside fs_debug class
*
* @param var
*/
function fs_debug_print_css()
{
	// get CI instance
	$CI = &get_instance();
	// return log information
	return $CI->fs_debug->print_css();
}