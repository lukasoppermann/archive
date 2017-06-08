<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Log Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/log
 */
class CI_FS_log {
	
	var $CI;
	private $log = null;
	
	public function __construct()
	{
		$this->CI =& get_instance();
		// Automatically load the log helper
		// $this->CI->load->helper('fs_log');
		// log initialization
		log_message('debug', 'FS Log Class Initialized');
	}
	// --------------------------------------------------------------------
	/**
	 * log
	 *
	 * @param string
	 * @param string
	 * @param array
	 * @description log message
	 */
	function log( $log_message = null, $type = null, $data = array() )
	{
		// add message
		($log_message != null) ? $data['data']['message'] = $log_message : '';
		// add type
		($type != null) ? $data['type'] = $type : '';
		// merge data
		$data = array_merge(array(
				'user_id' 	=> '',
				'system' 	=> '',
				'type' 		=> '',
				'entry_id' 	=> '',
				'data' 		=> null
			), $data);
		// insert log into db
		$this->_log($data);
	}
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @param int | array
	 * @param string
	 * @description	get value from current user
	 */
	function get( $params = array() , $limit = null )
	{
		if( is_int($params) )
		{
			return db_select( config('db_log'), array('id' => $params), array('select' => '*', 'json' => 'data', 'single' => TRUE) );
		}
		elseif( is_array($params) )
		{
			return db_select( config('db_log'), $params, array('select' => '*', 'limit' => $limit, 'json' => 'data', 'single' => FALSE, 'order' => 'date DESC') );
		}
	}
	// --------------------------------------------------------------------
	/**
	 * raw log - pipes to the utility _log method
	 *
	 * @param string
	 * @param key
	 * @description log message
	 */
	function raw_log( $data = array() )
	{
		// run _log
		$this->_log( $data );
	}
	// --------------------------------------------------------------------
	/**
	 * Utility Functions
	 *
	 * @description	not to be used outside this class
	 */
	function _log( $data = array() )
	{
		// merge data
		$data = array_merge(array(
				'user_ip' 	=> $_SERVER['REMOTE_ADDR'],
				'user_id' 	=> '',
				'system' 	=> '',
				'type' 		=> '',
				'entry_id' 	=> '',
				'data' 		=> null,
			), $data);	
		// merge data->data
		$data['data'] = array_merge(array(
				'time' => time()
			), $data['data']);
		// insert into db
		db_insert( config('db_log'), $data, array('data'));
	}
// end of class	
}
/* End of file fs_log.php */
/* Location: ./system/libraries/fs_log.php */