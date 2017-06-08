<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System View helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/view
 */

// ------------------------------------------------------------------------
/**
 * view - returns view within template
 *
 * @param string
 * @param array
 * @param boolean
 * @return string
 */
function view($view = null, $data = array(NULL), $return = FALSE)
{
	$CI = &get_instance();
	// put view in template
	if($view != "")
	{
		$page = $CI->load->view($view, $data, TRUE);
		$data['page'] = isset($page) ? $page : FALSE;
	}
	
	// if return is FALSE return view
	if($return == FALSE)
	{
		return $CI->load->view('template', $data);
	}
	// if return TRUE return array
	else
	{
		return $CI->load->view('template', $data, TRUE);		
	}
}
/* End of file view_helper.php */
/* Location: ./system/helpers/view_helper.php */