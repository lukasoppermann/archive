<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Authentication Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Authentication
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/authentication
 */
class CI_Authentication {

	var $CI;
	private $user = null;

	public function CI_Authentication()
	{
		$this->CI =& get_instance();
		$this->user = array();
		// load assets
		$this->CI->load->library('session');
		$this->CI->load->helper(array('cookie'));
		// Automatically load the authentication helper
		$this->CI->load->helper('authentication');
		// log initialization
		log_message('debug', "Authentification Class Initialized");
		// run login to autologin if session or cookie isset
		$this->login();
	}	
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @description	get value from current user
	 */
	function get($value = 'user_id')
	{
		if($value == 'array' && isset($this->user) )
		{
			return $this->user;
		}
		return isset($this->user) && array_key_exists($value, $this->user) ? $this->user[$value] : FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * login
	 *
	 * @description	login with provided data
	 */
	function login()
	{
		if($this->CI->input->post('login'))
		{
			// prepare form validation
			$this->CI->load->library('form_validation');
			$this->CI->form_validation->set_error_delimiters('<div class="error"><p>', '</p></div>');
			$this->CI->form_validation->set_rules('username', 'lang:username', 'trim|xss_clean|required');
			$this->CI->form_validation->set_rules('password', 'lang:password', 'trim|xss_clean|required');
			// validate form
			if($this->CI->form_validation->run() === TRUE)
			{
				if($this->_check($this->CI->input->post('username'),$this->CI->input->post('password')))
				{
					$array['user_id'] 	= $this->user['user_id'];
					$array['user_data'] = $this->user['user_data'];
				}
				else
				{
					$this->CI->form_validation->add_error('Wrong User Password Combination.');
					return FALSE;										
				}		
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			// get session
			$user_id 	= $this->CI->session->userdata('user_id');
			$token 		= $this->CI->session->userdata('token_name');
			// check if session is correct
			if( $user_id != null && $token != null )
			{
				// get user ide of current user
				$array['user_id'] = $user_id;
				// retrieve user-data from db
				$this->CI->db->select('id, email, user, group, data');
				$this->CI->db->where('id', $array['user_id']);
				$this->CI->db->where('token', $token);
				$this->CI->db->from('users');
				//
				$query = $this->CI->db->get();		
				// if there is a match
				if($query->num_rows() > 0)
				{	
					$row = $query->row();
					// set config array
					$array['user_name'] 	= $row->user;		
					$array['user_email'] 	= $row->email;
					$array['group'] 		= $row->group;
					$array['user_data'] 	= json_decode($row->data, true);	
				}
				else
				{
					return FALSE;
				}
				// loop through array and assign config
				foreach($array as $key => $value)
				{	
					$this->user[$key] = $value;
				}
			}
			elseif(get_cookie('user_id',TRUE) != null )
			{
				// get user ide of current user
				$array['user_id'] = get_cookie('user_id',TRUE);
				$token = get_cookie('token_name', TRUE);
				// retrieve user-data from db
				$this->CI->db->select('id, status, email, user, group, data');
				$this->CI->db->where('id', $array['user_id']);
				$this->CI->db->where('token', $token);
				$this->CI->db->from('users');
				//
				$query = $this->CI->db->get();	
				// if there is a match
				if($query->num_rows() > 0)
				{	
					$row = $query->row();
					// set config array
					$array['user_name'] 	= $row->user;		
					$array['status'] 		= $row->status;	
					$array['user_email'] 	= $row->email;
					$array['group'] 		= $row->group;
					$array['user_data'] 	= json_decode($row->data, true);			
				}
				else
				{
					return FALSE;
				}
				// set session
				$this->CI->session->set_userdata(array('user_id' => $array['user_id'], 'token_name' => $token));
				// loop through array and assign config
				foreach($array as $key => $value)
				{	
					$this->user[$key] = $value;
				}	
			}
			else
			{
				return FALSE;
			}
		}
		// if user keep_login = true, set cookie
		if(boolean($array['user_data']['keep_login']) != FALSE)
		{
			$user_id = variable(get_cookie('user_id',TRUE));
			if(!isset($user_id) || get_cookie('token_name',TRUE) != $this->CI->session->userdata('token_name'))
			{
				// predefine cookie values
				$cookie = array(
					'expire' => '86500',
				    'path'   => '/',
				    'secure' => FALSE
		        );
				// define which cookies to set
				$cookie_array['session_name'] 	= $this->CI->session->userdata('session_id');
				$cookie_array['user_id']		= $array['user_id'];
				$cookie_array['token_name'] 	= random_string('alnum', mt_rand(75, 100));			
				// set cookies
				foreach($cookie_array as $name => $value)
				{
					$cookie['name'] = $name;
					$cookie['value'] = $value;
					set_cookie($cookie);					
				}
				// update session
				$this->CI->session->set_userdata(array('token_name' => $cookie_array['token_name']));
				// update database with token
				$data = array('token' => $cookie_array['token_name']);
				$where = "id = ".$cookie_array['user_id'];
				$query = $this->CI->db->update_string('users', $data, $where);
				//
				$this->CI->db->query($query);
			}
		}
		// return True if logged in
		return TRUE;
	}
	// --------------------------------------------------------------------
	/**
	 * logout
	 *
	 * @description	logout
	 */
	function logout()
	{
		// destroy session
		// See http://codeigniter.com/forums/viewreply/662369/ as the reason for the next line
		$this->CI->session->set_userdata(array('user_id' => '', 'token' => ''));
		$this->CI->session->sess_destroy();
		// delete cookie
		$array = array('session_name','user_id','token_name');
		// loop through cookie array
		foreach($array as $string)
		{
			delete_cookie($string, config('domain'), '/');
		}
		redirect(base_url(TRUE).'dashboard', 'refresh');
		// return TRUE
		return TRUE;
	}
	// --------------------------------------------------------------------
	/**
	 * register
	 *
	 * @description	register new user
	 */
	function register()
	{
		
	}
	// --------------------------------------------------------------------
	/**
	 * restore
	 *
	 * @description	setup new password with email verification
	 */
	function restore()
	{
		
	}
	// --------------------------------------------------------------------
	/**
	 * change
	 *
	 * @description	change any user data
	 */
	function change()
	{
		
	}
	
	// --------------------------------------------------------------------
	/**
	 * Utility Functions
	 *
	 * @description	not to be used outside this class
	 */
	function prep_password($password, $db_salt)
	{
		return sha512(salt($password, $db_salt));
	}
	// --------------------------------------------------------------------
	/**
	 * check login
	 *
	 * @description	compare username & password with db
	 * 
	 */
	function _check($username, $password)
	{
		// retrieve user data by username
		$this->CI->db->select('id, status, password, salt, email, user, group, data');
		$this->CI->db->where('user', $username);
		$this->CI->db->where('status', '1');
		$this->CI->db->from('users');
		//
		$query = $this->CI->db->get();		
		// if there is a match
		if($query->num_rows() > 0)
		{	
			$row = $query->row();
			// set config array
			$array['user_id'] 		= $row->id;
			$array['user_name'] 	= $row->user;
			$array['status'] 		= $row->status;			
			$array['user_email'] 	= $row->email;
			$array['group'] 		= $row->group;
			$array['user_data'] 	= json_decode($row->data, true);
			$db_password			= $row->password;
			$db_salt				= $row->salt;			
		}
		else
		{
			return FALSE;
		}
		// if user exists, compare passwords
		if( trim($db_password) === trim( $this->prep_password($password, $db_salt) ) )
		{	
			// crete token
			$token 	= random_string('alnum', mt_rand(75, 100));			
			// set session
			$this->CI->session->set_userdata(array('user_id' => $array['user_id'], 'token_name' => $token));
			// update database with token
			$data = array('token' => $token);
			$where = "id = ".$array['user_id'];
			$query = $this->CI->db->update_string('users', $data, $where);
			//
			$this->CI->db->query($query);
			// loop through array and assign config
			foreach($array as $key => $value)
			{	
				$this->user[$key] = $value;
			}
			// return TRUE
			return TRUE;
		}
		else
		{
			// update database with token
			$data = array('token' => "");
			$where = "id = ".$array['user_id'];
			$query = $this->CI->db->update_string('users', $data, $where);
			//
			$this->CI->db->query($query);
			//
			return FALSE;
		}
	}
}
/* End of file Authentication.php */
/* Location: ./system/libraries/Authentication.php */