<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System Authentication Helpers
 *
 * @package			Form&System
 * @subpackage		Helpers
 * @category		Helpers
 * @author			Lukas Oppermann - veare.net
 * @link			http://doc.formandsystem.com/helpers/authentication
 * @depencies 		css & js helpers
 * 					view helpers
 * 					fs_log
 * 					
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
	// get CI Instance
	$CI =& get_instance();
	// check if auth lib is loaded
	if( class_exists('CI_fs_authentication') ) 
	{	
		// try to login
		if(!$CI->fs_authentication->login())
		{
			if( !user_access($group) )
			{
				js_add('fs.base, fs.login');
				css_add('widgets, login');
				$url 	= current_url();
				$users 	= $CI->load->view('login/new_user', '', TRUE);
				return view('login/login', array('url' => $url, 'users' => $users, 'load_user' => ' load-user'), TRUE);
			}
		}
		else
		{
			if( !user_access($group) )
			{
				js_add('fs.login');
				css_add('widgets, login');
				fs_log('Switch User');
				$url 	= current_url();
				$users 	= $CI->load->view('login/new_user', '', TRUE);
				return view('login/login', array('url' => $url, 'users' => $users, 'load_user' => ' load-user'), TRUE);
			}
		}
	}
	// if lib is not loaded return TRUE
	return TRUE;
}
// --------------------------------------------------------------------
/**
 * user_access - grant or deny access
 *
 * @param string
 * @return boolean
 */
function user_access($group = FALSE)
{
	// get CI Instance
	$CI =& get_instance();
	// check if auth lib is loaded
	if( class_exists('CI_fs_authentication') ) 
	{
		!is_array($group) && strpos($group, ",") === 0 ? $group = explode(',',$group) : '';
		// get user group
		$user_group = $CI->fs_authentication->get('group');
		// check for acceptable group
		if( $user_group['_id'] == $group || 
			( is_array($group) && in_array($user_group['_id'], $group) ) || 
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
	// if lib is not loaded return TRUE
	return TRUE;
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
	$CI->fs_authentication->logout();	
	//
	css_add('screen','login');
	//
	return $CI->load->view('login/login', '', TRUE);
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
	// create static & dynamic salt
	$static_salt = "ae61473e39e9bc104f5a1847b9791697";
	$dynamic_salt = !empty($dynamic_salt) ? $dynamic_salt : 'eb8a3405';
	// get middle of string
	$middle = (int) (strlen($string) / 2);
	// salt string
	$salted_string = substr($string, 0, $middle).$dynamic_salt.substr($string, $middle).$static_salt;
	// return salted string
	return $salted_string;
}
// --------------------------------------------------------------------
/**
 * sha256 - produces a sha256 hash
 *
 * @param string
 * @return hash
 */

function _sha256($string)
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

function _sha512($string)
{
	return hash('sha512', $string);
}
// --------------------------------------------------------------------
/**
 * create_password - creates password hash and salt
 *
 * @param string
 * @return hash
 */

function create_password($password)
{
	$output['salt'] 	= random_string('alnum', mt_rand(15, 25));
	$output['password'] = trim(_sha512(salt($password, $output['salt'])));
	return $output;
}
// --------------------------------------------------------------------
/**
 * prep_password - hashes and salts password
 *
 * @param string
 * @return hash
 */

function prep_password($password, $db_salt)
{
	return 	trim(_sha512(salt($password, $db_salt)));
}
// --------------------------------------------------------------------
/**
 * user - get user values
 *
 * @param string
 * @return string
 */
function user($value = 'id')
{
	$CI =& get_instance();
	// return value of user array
	return $CI->fs_authentication->get($value);
}
// --------------------------------------------------------------------
/**
 * auth
 *
 * @description authorizes user to do ajax action
 */
function _auth($path = null)
{
	$CI =& get_instance();
	// get group for page
	$group = $CI->fs_navigation->get_by('path', $path);
	foreach((array) $group as $key => $val)
	{
		if($val['type'] == 1)
		{
			$group = $val['group'];
		}
	}	
	// check if user has sufficent right
	return login($group);
}
/* End of file authentication_helper.php */
/* Location: ./system/helpers/authentication_helper.php */