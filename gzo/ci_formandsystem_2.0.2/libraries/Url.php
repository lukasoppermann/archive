<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Url Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Url
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/url
 */
class CI_Url {

	var $CI;
	var $parts;
	
	public function __construct($params = null)
	{
		// ----------------
		// define CI instance		
		$this->CI = &get_instance();
		// ----------------
		// load variable helper
		$this->CI->load->helper('variable');
		// ----------------
		// log class initialization
		log_message('debug', "Url Class Initialized");
		// ----------------
		// get parts 
		$this->parts = $this->CI->uri->segment_array();
		// ----------------
	}	
	// --------------------------------------------------------------------
	/**
	 * parts
	 *
	 * @param array
	 * @param array ('url_position' => 1, 'active_element' => 'name', 'return_element' => 'id', 'config_name' => 'system)
	 * @description	
	 */
	function part($data, $params = array())
	{
		// get element from url
		$element = $this->CI->uri->segment($params['url_position']);
		// define default value for element
		$default = is_index_array($data, 'default');
		$default = variable($default[1]);
		if( !is_array($default) )
		{
			$array = index_array($data, 'position');
			$default = $array[1];
		}
		// get current element
		$tmp = index_array($data, $params['active_element']);
		$array = ($element != FALSE && array_key_exists($element, $tmp)) ? $tmp[$element] : $default;
		// set config
		$this->CI->config->set_item($params['config_name'], $array[$params['active_element']]);
		$url_parts = $this->CI->config->slash_item('url_parts');
		$this->CI->config->set_item('url_parts', $url_parts.$array[$params['active_element']]);
		// return item
		return $array[$params['return_element']];
	}	
}
// END Url Class

/* End of file Url.php */
/* Location: ./system/libraries/Url.php */