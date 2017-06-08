<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006 - 2012 EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */

// ------------------------------------------------------------------------

/**
 * CSS Driver 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/driver/optimize/css
 */

class Fs_optimize_css extends CI_Driver {

	function __construct()
	{
		// log init
		log_message('debug', "Fs_optimize_css driver Initialized");
	}
	// --------------------------------------------------------------------
	/**
	 * __call
	 *
	 * run on each call of fn of this driver
	 */		
	public function __call($method, $args = array())
	{
		// set current driver
		$this->_parent->init('css', $this->driver_init('css'));
		// init parent call
		if (in_array($method, $this->_methods))
		{
			return call_user_func_array(array($this->_parent, $method), $args);
		}
		$trace = debug_backtrace();
		_exception_handler(E_ERROR, "No such method '{$method}'", $trace[1]['file'], $trace[1]['line']);
		exit;
	}
	// --------------------------------------------------------------------
	/**
	 * init
	 *
	 * initializing driver
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function driver_init( $driver )
	{
		$this->CI->config->load('fs_'.$driver);
		$compression 						= $this->CI->config->item('compression');
		
		$params['dir']					= $this->CI->config->item('dir_'.$driver);
		$params['cache_dir'] 		= $this->CI->config->item('dir_css_cache');
		$params['tags'] 				= $this->CI->config->item('tags', 'fs_css');
		$params['regex'] 				= $this->CI->config->item('regex', 'fs_css');
		$params['gzip'] 				= $compression[$driver]['gzip'];
		$params['expire'] 			= $compression[$driver]['expire'];
		$params['content_type'] = 'text/css';
		$params['ext'] 					= 'css';
		// return params
		return $params;
	}
	// --------------------------------------------------------------------
	/**
	 * output
	 *
	 * fn which runs on output it is saved to file
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function output( $output )
	{
		// return output
		return $output;
	}
}