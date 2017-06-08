<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Config Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/variable
 */

// --------------------------------------------------------------------
/**
 * variable - checks if variable exists, if true, return
 *
 * @param var &$var
 * @return $var | NULL
 */

function variable( &$var, $default = NULL, $before = null, $after = null )
{
	// check if var is emoty
	if( (!isset($var) && !is_array($var)) || empty($var) )
	{
		// if default is set
		if( isset($default) && $default != false && $default != null )
		{
			// return default
			return $before.$default.$after;
		}
		// return FALSE
		return FALSE;
	}
	// is array
	elseif( is_array($var) )
	{
		// if array is empty
		if( count($var) <= 0  )
		{
			if( isset($default) && $default != false )
			{
				return $default;
			}
			return FALSE;
		}
		// if array is not empty
		else
		{
			return $var;
		}
	}
	// if variable is not array and not empty
	else
	{
		
		return $before.$var.$after;
	}
}
// --------------------------------------------------------------------
/**
 * boolean - checks boolean returns TRUE || FALSE
 *
 * @param boolean || true false string
 * @return TRUE/FALSE
 */
function boolean( &$boolean )
{
	$false = array('FALSE','false','0','null','NULL');
	if( in_array($boolean, $false) || $boolean == null || $boolean == false || !isset($boolean) )
	{
		return FALSE;
	}
	return TRUE;
}

/* End of file variable_helper.php */
/* Location: ./system/helpers/variable_helper.php */