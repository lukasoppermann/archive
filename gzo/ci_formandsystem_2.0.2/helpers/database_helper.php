<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System Database Helper
 *
 * @package		Form&System
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://formandsystem.com/database
 */

// --------------------------------------------------------------------
/**
 * get_db_data - retrieves DB data from db and returns array
 *
 * @param array
 * @param array
 * @return string
 */
function get_db_data($table, $db = array('where' => array(), 'select' => '*', 'order' => null, 'limit' => null), $params = null)
{
	$CI = &get_instance();
	
	$CI->db->select(addslashes($db['select']));
	$CI->db->from($table);
	
	if(isset($db['where']) && count($db['where']) > 0)
	{
		$CI->db->where($db['where']);
	}
	if(isset($db['order']))
	{
		$CI->db->order_by($db['order']);
	}
	if(isset($db['limit']))
	{
		$CI->db->limit($db['limit']);
	}
	
	$query = $CI->db->get();
					
	foreach ($query->result_array() as $row)
	{
		foreach($row as $key => $value)
		{
			$json = json_decode($value, TRUE);
			if(is_array($json))
			{
				unset($row[$key]);
				if(is_array($json) && is_array($row))
				{
					$row = array_merge($json, $row);
				}
			}
		}
		
		if( is_array($params['add']) )
		{
			foreach($params['add'] as $key => $value){
				if(preg_match('/\$\[(?P<val>\w+)\]/', $value, $matches))
				{
					$value = str_replace($matches[0], $row[$matches[1]], $value);
				}
				$row[$key] = $value;
			}
		}
		
		$array[] = $row;
	}
	
	if(isset($array))
	{
		return $array;
	}
	else
	{
		return FALSE;
	}
}
/* End of file database_helper.php */
/* Location: ./system/helpers/database_helper.php */