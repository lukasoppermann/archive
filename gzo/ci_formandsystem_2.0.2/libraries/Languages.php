<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Language Class
 *
 * @version		0.1
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Languages
 * @author		Lukas Oppermann - veare.net
 * @link		www.veare.net
 */

/**
*
*
*
*
* USE id from data instead of real id!!!
* Language helper with languages switch
*
*
*
*
*/
class CI_Languages {
	// -----------------
	// variables
	var $CI 		= null; // CodeIgniter Object
	var $languages	= array();
	var $abbr		= array();	
	var $current	= array();
	var $default	= array();
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct($params = null)
	{
		// ----------------
		// define CI instance		
		$this->CI = &get_instance();
		// ----------------
		// load db core class
		$this->CI->load->database();
		// ----------------
		// load variable helper
		$this->CI->load->helper('variable');
		// ----------------
		// get prefix
		$prefix = ( !empty($params['prefix']) ) ? $params['prefix'] : $this->CI->config->item('prefix');
		// ----------------
		// define db table
		$db_table = ( !empty($params['db_table']) ) ? $params['db_table'] : $prefix.$this->CI->config->item('db_data');
		// ----------------
		// log class initialization
		log_message('debug', "Languages Class Initialized for ".$db_table);
		// ----------------
		// retrieve data from db
		$this->CI->db->select('id, key, type, value');
		$this->CI->db->from($db_table);
		$this->CI->db->where('key', 'languages');
		
		$query = $this->CI->db->get();		
		foreach ($query->result() as $row)
		{
			$value 			= json_decode($row->value, TRUE);
			
			if($value['position'] == 1)
			{
				$default_abbr 	= $value['abbr'];
				$default_id 	= $value['id'];
				$this->CI->config->set_item('lang_default_abbr', $default_abbr);
				$this->CI->config->set_item('lang_default_id', $default_id);
			}
			
			$languages['abbr'][$value['id']] 		= $value['abbr'];
			$languages['id'][$value['abbr']] 		= $value['id'];		
			$languages['array'][$value['id']]		= $value;
		}
		// ----------------
		// define config
		$this->CI->config->set_item('languages', $languages);
		$this->languages = $languages;
		// ----------------
		// define current language
		$segment = $this->CI->config->item('url_lang');
		if(isset($segment))
		{
			$cur = $this->CI->uri->segment($this->CI->config->item('url_lang'));
		}
		else
		{
			$cur = $this->CI->uri->segment(1);			
		}
		if(isset($cur) && isset($languages['id'][$cur]))
		{
			$this->CI->config->set_item('lang_abbr', $cur);
			$this->CI->config->set_item('lang_id', $languages['id'][$cur]);
		}
		else
		{
			$this->CI->config->set_item('lang_abbr', $default_abbr);
			$this->CI->config->set_item('lang_id', $default_id);
		}
	}	
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @description	returns language by key_value
	 */
	function get($key = null, $type = 'array')
	{
		if(is_numeric($key))
		{
			if(!$lang = variable($this->languages['array'][$key]))
			{
				$lang = $this->languages['array'][$this->CI->config->item('lang_default_id')];
			}
		}
		elseif(strlen($key) == 2)
		{
			if(!$lang = variable($this->languages['array'][$this->languages['id'][$key]]))
			{
				$lang = $this->languages['array'][$this->CI->config->item('lang_default_id')];
			}
		}
		else
		{
			foreach($this->languages['array'] as $id => $language)
			{
				if($search = array_search($key, $language))
				{
					$lang = $this->languages['array'][$id];
					break;
				}
			}
			if(!isset($lang))
			{
				$lang = $this->languages['array'][$this->CI->config->item('lang_default_id')];
			}
		}
		// return array or element
		if(isset($lang[$type]))
		{
			return $lang[$type];
		}
		else
		{
			return $lang;
		}
	}
}
/* End of file Languages.php */
/* Location: ./libaries/Languages.php */