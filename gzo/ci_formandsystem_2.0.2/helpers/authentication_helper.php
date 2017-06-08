<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Authentication Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/authentication
 */
// --------------------------------------------------------------------
/**
 * login - login with given data
 *
 * @param string
 * @return string
 */
function login($group = null)
{
	$CI =& get_instance();
	
	if(!$CI->authentication->login())
	{
		if(!user_access($group))
		{
			css_add('screen','login');
			return $CI->load->view('custom/login', array('url' => current_url()));
		}
	}
	else
	{
		if(!user_access($group))
		{
			css_add('screen','login');
			$CI->data['url'] = current_url();
			return $CI->load->view('custom/switch_user', $CI->data); 
		}
	}
}
// --------------------------------------------------------------------
/**
 * user_access - grant or deny access
 *
 * @param string
 * @return boolean
 */
function user_access($group = NULL)
{
	$CI =& get_instance();
	//
	!is_array($group) && strpos($group, ",") === 0 ? $group = explode(',',$group) : '';
	// get user group
	$user_group = $CI->authentication->get('group');
	// check for acceptable group
	if( $user_group == $group || 
		( is_array($group) && in_array($user_group, $group) ) || 
		( $group == 'x' && $user_group == false ) || 
		$group == null || 
		( ($group == '*' || (is_array($group) && $group[key($group)] == '*') ) && $user_group != null ) 
	)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}
// --------------------------------------------------------------------
/**
 * logout
 *
 * @return boolean
 */
function logout()
{
	$CI =& get_instance();
	$CI->authentication->logout();	
	//
	css_add('screen','login');
	//
	return $CI->load->view('custom/login', '', TRUE);
}
// --------------------------------------------------------------------
/**
 * salt - produces a salted string
 *
 * @param string
 * @param salt
 * @return string 
 */

function salt($string, $dynamic_salt = NULL)
{
	$static_salt = "f897c521";
	$static_salt = "string";
	$dynamic_salt = !empty($dynamic_salt) ? $dynamic_salt : 'eb8a3405';

	$middle = (int) (strlen($string) / 2);
	$salted_string = substr($string, 0, $middle).$dynamic_salt.substr($string, $middle).$static_salt;

	return $salted_string;
}
// --------------------------------------------------------------------
/**
 * sha256 - produces a sha256 hash
 *
 * @param string
 * @return hash
 */

function sha256($string)
{
	return hash('sha256', $string);
}
// --------------------------------------------------------------------
/**
 * sha512 - produces a sha512 hash
 *
 * @param string
 * @return hash
 */

function sha512($string)
{
	return hash('sha512', $string);
}
// --------------------------------------------------------------------
/**
 * user - get user values
 *
 * @param string
 * @return string
 */
function user($value = 'user_id')
{
	// set value to user_id if id requested
	if($value == 'id'){ $value = 'user_id'; }
	//
	$CI =& get_instance();
	return $CI->authentication->get($value);
}
/* End of file authentication_helper.php */
/* Location: ./system/helpers/authentication_helper.php */