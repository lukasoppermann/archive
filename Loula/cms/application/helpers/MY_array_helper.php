<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter MY_Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/array
 */

/**
 * Index Array - Changes the array key to be sorted by different array value
 *
 * @access	public
 * @param	array
 * @param	string
 * @return	array
 */	
function index_array($array, $index, $multi = FALSE)
{
	foreach($array as $key => $value)
	{
		if($multi == TRUE)
		{
			$new_array[$value[$index]][$key] = $value;
		}
		else
		{
			$new_array[$value[$index]] = $value;
		}
	}
	if(isset($new_array) && is_array($new_array) )
	{
		return $new_array;
	}
	else
	{
		return array(null);
	}
}