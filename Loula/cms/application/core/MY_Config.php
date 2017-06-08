<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * CodeIgniter MY_Config Libraries
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Config
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/core/config
 */
class MY_Config extends CI_Config {

	var $CI;
	
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	// --------------------------------------------------------------------
	/**
	 * config from db
	 */
	function config_from_db($db = null)
	{
		// check for db name or use default
		!isset($db) ? $db = $this->item('prefix').$this->item('db_data') : '';
		// get CI instance
		$this->CI =& get_instance();
		// load database library
		$this->CI->load->database();
		// get data from db
		$this->CI->db->select('id, key, type, value');
		$this->CI->db->from($db);
		$this->CI->db->where('key', 'settings');
		//
		$query = $this->CI->db->get();	
		// set $array to collect $row->types
		$array = array();
		// run through each item
		foreach ($query->result() as $row)
		{
			// if value is a json-string
			if(is_array($values = json_decode($row->value, TRUE)))
			{
				// if json is split into languages
				if(array_key_exists($this->item('lang_id'), $values))
				{
					// set config item
					$this->set_item($row->type, $values[$this->item('lang_id')]);
				}
				// if json is NOT split into languages
				else
				{
					// if type is NOT in array
					if( !in_array($row->type, $array) )
					{
						// set config item
						$this->set_item($row->type, $values);
						// add type to array
						$array[] = $row->type;
					}
					// if type is in array
					else
					{
						// get data for type
						$value = $this->item($row->type);
						// reset to get first id
						reset($value);
						// if data is array
						if( is_array($value[key($value)]) )
						{
							$value[] = $values;
						}
						// if data is NOT an array
						else
						{
							// nedd this to not have doubling effect
							$val = $value;
							unset($value);
							// add data and new data to array
							$value[] = $val;
							$value[] = $values;
						}
						// set config item
						$this->set_item($row->type, $value);
					}
				}
			}
			// if value is a normal string
			else
			{
				// if type is NOT in array
				if( !in_array($row->type, $array) )
				{
					// set config item
					$this->set_item($row->type, $row->value);
					// add type to array
					$array[] = $row->type;
				}
				// if type is in array
				else
				{
					// get data for type
					$value = $this->item($row->type);
					// if data is array
					if( is_array($value[key($value)]) )
					{
						// add to array
						$value[] = $row->value;
					}
					// if data is NOT an array
					else
					{
						// nedd this to not have doubling effect
						$val = $value;
						unset($value);
						// add data and new data to array
						$value[] = $val;
						$value[] = $row->value;
					}
					// set config item
					$this->set_item($row->type, $value);					
				}
			}
		}
	}
// --------------------------------------------------------------------
/**
 * unslash_item - returns config item without slash
 *
 * @param string 
 * @return string
 */
	function unslash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return FALSE;
		}

		return rtrim($this->config[$item], '/');
	}
	
}
/* End of file MY_Config.php */
/* Location: ./application/core/MY_Config.php */