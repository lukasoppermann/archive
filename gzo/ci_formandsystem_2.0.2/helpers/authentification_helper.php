<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Metadata Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/authentification
 */

// --------------------------------------------------------------------
/**
 * salt - produces a salted string
 *
 * @param string
 * @param salt
 * @return string doctype
 */

function salt($string, $dynamic_salt = NULL)
{
	$static_salt = "f897c521";
	$dynamic_salt = !empty($dynamic_salt) ? $dynamic_salt : 'fb9a3505';

	$middle = (int) (strlen($string) / 2);
	$salted_string = substr($string, 0, $middle).$dynamic_salt.substr($string, $middle).$static_salt;

	return $salted_string;
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
	return $CI->authentification->get($value);
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
	return $CI->authentification->access($group);
}
/* End of file authentification_helper.php */
/* Location: ./system/helpers/authentification_helper.php */