<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter View Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/variable
 */

// --------------------------------------------------------------------
/**
 * view - renders view inside template
 *
 * @param var &$var
 * @return $var | NULL
 */
 function view($template, $data = null)
 {
	// get CI instance
	$CI = &get_instance();
	// check if view already loaded
	if($CI->config->item('view_loaded') != 'done')
	{
		$CI->config->set_item('view_loaded', 'done');
		//
		$data['page'] = $CI->load->view($template, $data, TRUE);
		// load default stuff
		$data['nav'] = $CI->cms->nav();
		// render view
		$CI->load->view('template', $data);
	}
 }