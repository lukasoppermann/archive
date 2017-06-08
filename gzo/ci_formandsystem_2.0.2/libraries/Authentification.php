<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Authentification Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Authentification
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/authentification
 */
class CI_Authentification {

	var $CI;
	var $user = NULL;
	
	//
	//
	//
	// All the old stuff
	//
	//
	//
	//
	public function CI_Authentification()
	{
		$this->CI =& get_instance();
		// Automatically load the css helper
		$this->CI->load->helper('authentification');

		log_message('debug', "Authentification Class Initialized");
		
		$this->user = array('id' => '1', 'group' => 1);
	}	
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @description	get value from current user
	 */
	function get($value = 'id')
	{
		return array_key_exists($value, $this->user) ? $this->user[$value] : FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * access
	 *
	 * @description	return true if access is granted
	 */
	function access($group = NULL)
	{
		if($group == $this->user['group'] || ($group == '0' && !isset($this->user['group']) ) || !isset($group) || ($group == '*' && isset($this->user['group'])) )
		{
			return TRUE;
		}
	}
	
}
// END Css Class

/* End of file Authentification.php */
/* Location: ./system/libraries/Authentification.php */