<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * CodeIgniter MY_Model Libraries
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Model
 * @author		Lukas Oppermann - veare.net
 */
class MY_Model extends CI_Model {

	//php 5 constructor
	function __construct() 
 	{

	}
	
	function db_fetch($table, $db = array())
	{
		// merge data
		$db = array_merge(array('where' => array(), 'select' => '*', 'order' => null, 'order_dir' => 'ASC', 'limit' => null), $db);
		//
		$this->db->select(addslashes($db['select']));
		// where
		if(isset($db['where']) && count($db['where']) > 0)
		{
			$this->db->where($db['where']);
		}
		// order
		if(isset($db['order']))
		{
			$this->db->order_by($db['order'], $db['order_dir']);
		}
		// limit
		if(isset($db['limit']))
		{
			$this->db->limit($db['limit']);
		}
		// run query
		$_query = $this->db->get($table);
		// prep result: loop through rows
		foreach ($_query->result_array() as $row)
		{
			// loop through columns
			foreach($row as $key => $value)
			{
				// check if column is json
				$json = json_decode($value, TRUE);
				// if is json
				if( is_array($json) )
				{
					// unset column
					unset($row[$key]);
					// add decoded values to row !same keys in json will be overwritten
					$row = array_merge($json, $row);
				}
			}
			// add row to array
			$array[] = $row;
		}
		// if array exists 
		if( isset($array) )
		{
			return $array;
		}
		else
		{
			return FALSE;
		}
	}
// close class	
}