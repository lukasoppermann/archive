<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Entries Class
 *
 * Gets Data for current entry and displays it
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Authentification
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/entries
 */

class CI_Entries {
	
	var $CI = NULL;
	var $params = NULL;
	var $output = NULL;

	public function __construct()
	{
		$this->CI = & get_instance();
		// prepare params
		$this->params = array(
				'db_table' => $this->CI->config->item('prefix').$this->CI->config->item('db_entries'),
				'template' => 'default'
			);
	}
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @description	get entry
	 * 
	 * @param	int 
	 * @param	array
	 * @return 	entries
	 */
	function get($menu_id = NULL, $data = array(NULL))
	{
		// prepare data
		$this->prepare($menu_id, $data);
		// return output
		return $this->output['output'];
	}
	// --------------------------------------------------------------------
	/**
	 * get_element
	 *
	 * @description	returns specific element
	 * 
	 * @param	string
	 * @return 	string|array
	 */
	function get_element($element = NULL, $menu_id = NULL)
	{
		// if element exists (only one entry was retrieved)
		if( array_key_exists($element, $this->output) )
		{
			return $this->output[$element];
		}
		// if element exists (only multiple entries were retrieved)
		elseif( isset($menu_id) && array_key_exists($menu_id, $this->output) && array_key_exists($element, $this->output[$menu_id]) )
		{
			return $this->output[$menu_id][$element];
		}
		elseif( $element == 'array' )
		{
			return $this->output;
		}
		// element does not exists
		else
		{
			return FALSE;
		}
	}
	/**
	 * prepare
	 *
	 * @description	prepares entry
	 * 
	 * @param	int 
	 * @param	array
	 * @return 	entries
	 */
	function prepare($menu_id = NULL, $data = array(NULL))
	{
		$template = isset($data['template']) ? $data['template'] : $this->params['template'];
			
		if( isset($menu_id) )
		{
			// retrieve entries from db
			$entries = get_db_data($this->params['db_table'], $db = array('where' => array('menu_id' => $menu_id), 'select' => '*'));
			// check if entry exists
			if($entries != FALSE)
			{
				// merge entries & data
				$data = array_merge($data, $entries[0]);
				// render entry
				$data['output'] = $this->CI->load->view($template, $data, TRUE);
			}
		}
		else
		{
			// retrieve entries from db
			$entries = get_db_data($this->params['db_table'], $db = array('select' => '*'));
			// check if entry exists
			if($entries != FALSE)
			{
				// loop through entries
				foreach($entries as $entry)
				{
					// merge entries & data
					$data[] = array_merge($data, $entry);
					// render entry
					$data[key($data)]['output'] = $this->CI->load->view($template, $data, TRUE);
					$data['output'] .= $this->CI->load->view($template, $data, TRUE);
				}
			}
		}
		// if entry does not exist return error
		if( empty($data['output']) || $entries == FALSE )
		{
			$data['output'] = $this->error();
		}
		// return entry or error
		$this->output = $data;
	}
	// --------------------------------------------------------------------
	/**
	 * error
	 *
	 * @description	returns error message
	 * 
	 * @param	string 
	 * @param	array
	 * @return 	error page
	 */
	function error($data = array(NULL))
	{
		// merge array
		$data = array_merge(array('type' => '404'), $data);
		//
		$data['headline'] = 'The page you are looking for does not exists.';
		$data['message'] = 'We are sorry, but the page you are looking for does not exists.';
		// return error page
		return $this->CI->load->view('error', $data, TRUE);
	}
}
// END CI_Entries class

/* End of file Entries.php */
/* Location: ./system/libraries/Entries.php */