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
class CI_FS_authentication {

	var $CI;
	private $user = null;

	public function __construct()
	{
		$this->CI =& get_instance();
		// load assets
		$this->CI->load->library('session');
		$this->CI->load->helper(array('cookie','language','url','string'));
		// Automatically load the authentication helper
		$this->CI->load->helper('fs_authentication');
		// log initialization
		log_message('debug', 'FS Authentification Class Initialized');
		// run login to autologin if session or cookie isset
		$this->login();
	}	
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * @description	get value from current user
	 */
	function get( $key = 'id' )
	{
		// mapping user data
		$map['username'] = 'user';
		// check for array
		if( isset($this->user) && is_array($this->user) )
		{
			if( $key == 'array' )
			{
				return $this->user;
			}
			// check for key
			elseif( array_key_exists($key, $this->user) )
			{
				return $this->user[$key];
			}
			// check for key in map
			elseif( array_key_exists($key, $map) )
			{
				return $this->user[$map[$key]];			
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * login
	 *
	 * @description	login with provided data
	 */
	function login()
	{
		// check if user is logged in
		if( !isset($this->user['_id']) && !isset($this->user['user']) )
		{
			// if login form was submitted
			if($this->CI->input->post('fs_username') || $this->CI->input->post('fs_password'))
			{
				// clear user_blocked session
				set_cookie( array('user_blocked' => null) );
				delete_cookie('user_blocked', config('cookie_domain'), '/');
				// check if ip is blocked
				if( $this->_check_ip_attempts() !== FALSE)
				{
					// prepare form validation
					$this->CI->load->library('form_validation');
					// validate form
					if($this->CI->form_validation->run('login') === TRUE)
					{
						// check login data
						if($this->_check($this->CI->input->post('fs_username'), $this->CI->input->post('fs_password')) == FALSE)
						{
							// if login == FALSE
							return FALSE;
						}		
					}
					else
					{
						// validation FALSE
						return FALSE;
					}
				}
				else
				{
					// ip blocked
					return FALSE;
				}
			}
			// if login not submitted -> Autologin
			elseif( !$this->_check_session() && !$this->_check_cookie() )
			{
				// autologin failed
				return FALSE;
			}
			// if user keep_login = true, set cookie
			$this->_remember_login();
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
		// set log data
		$log = array('message' => lang('logout_successful'), 'username' => $this->user['user']);
		// log login attempt
		$this->CI->fs_log->raw_log(array('user_id' => $this->user['id'], 'type' => 2, 'data' => $log));
		// destroy session
		$this->CI->session->set_userdata(array('fs_user_id' => '', 'fs_token_name' => ''));
		$this->CI->session->sess_destroy();
		// delete cookie
		$array = array('session_name','fs_user_id','fs_token_name');
		// loop through cookie array
		foreach($array as $string)
		{
			delete_cookie($string, config('cookie_domain'), '/');
		}
		// redirect
		redirect( active_url(TRUE).config('menu_default/path'), 'refresh' );
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
	function restore( $user, $password, $retrieval_key, $reset = FALSE )
	{
		// check if user exists and is activated -> get user data
		if( $this->user = $this->_get_user($user) && $this->user['status'] === '1' )
		{
			// check key and time
			if( $this->user['retrieval_key'] === $retrieval_key && $this->user['retrieval_time'] >= time() )
			{
				// if user is blocked || forgot username try to login
				if( $reset !== TRUE && $reset !== 'TRUE' )
				{
					// check if password matches
					if( $this->user['password'] === prep_password($password, $this->user['salt']) )
					{
						// create token
						$this->_create_token();
						// update database with token
						db_update(config('db_user'), array('id' => $this->user['id']), array('token' => $this->user['token']));
						// set log data
						$log = array('message' => lang('retrieval_login_successful'), 'username' => $this->user['user']);
						// log login
						$this->CI->fs_log->raw_log(array('type' => 3, 'user_id' => $this->user['id'], 'data' => $log));			
						// clear failed login attempts
						$this->_clear_attempts();
						// return TRUE
						return TRUE;
					}
				}
				// user forgot password
				else
				{
					// create token
					$this->_create_token();
					// update database with token
					db_update(config('db_user'), array('id' => $this->user['id']), array('token' => $this->user['token'], 
					'password' => prep_password($password, $this->user['salt'])));
					// set log data
					$log = array('message' => lang('retrieval_password'), 'username' => $this->user['user']);
					// log login
					$this->CI->fs_log->raw_log(array('type' => 3, 'user_id' => $this->user['id'], 'data' => $log));			
					// clear failed login attempts
					$this->_clear_attempts();
					// return TRUE
					return TRUE;
				}
			}
			// set log data
			$log = array('message' => lang('retrieval_failed'), 'username' => $this->user['user']);
			// log login
			$this->CI->fs_log->raw_log(array('type' => 3, 'user_id' => $this->user['id'], 'data' => $log));
		}
		// return FALSE
		return FALSE;
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
	// --------------------------------------------------------------------
	/**
	 * remember login
	 *
	 * @description	set cookies to keep user logged in
	 * 
	 */
	function _remember_login( )
	{
		// check if user set keep login to true
		if( isset($this->user) && isset($this->user['keep_login']) && $this->user['keep_login'] != FALSE )
		{	
			// get user id
			$user_id = get_cookie('fs_user_id',TRUE);
			// check if userlogin is stored
			if( !isset($user_id) || get_cookie('fs_token_name',TRUE) != $this->CI->session->userdata('fs_token_name') )
			{
				// predefine cookie values
				$cookie = array(
					'expire' => config('keep_login_time'),
				    'path'   => '/',
				    'secure' => FALSE
		        );
				// define which cookies to set
				$cookie_array['session_name'] 	= $this->CI->session->userdata('session_id');
				$cookie_array['fs_user_id']		= $this->user['id'];
				$cookie_array['fs_token_name'] 	= $this->user['token'] = random_string('alnum', mt_rand(75, 100));
				// set cookies
				foreach($cookie_array as $name => $value)
				{
					$cookie['name'] = $name;
					$cookie['value'] = $value;
					set_cookie($cookie);
				}
				// update session
				$this->CI->session->set_userdata(array('fs_token_name' => $cookie_array['fs_token_name']));
				// update user
				$this->user['token'] = $cookie_array['fs_token_name'];
				// update database with token
				db_update( config('db_user'), array('id' => $this->user['id']), array('token' => $this->user['token']) );
			}
		}
	}
	// --------------------------------------------------------------------
	/**
	 * check session
	 *
	 * @description	try to login using session
	 * 
	 */
	function _check_session()
	{
		// get session
		$session_user_id = $this->CI->session->userdata('fs_user_id');
		$session_token = $this->CI->session->userdata('fs_token_name');
		// check session
		$session_user_id 	= ( isset($session_user_id) ? $session_user_id : FALSE);
		$session_token 		= ( isset($session_token) ? $session_token : FALSE );
		// check if session is set
		if( $session_user_id != null && $session_token != null )
		{
			// retrieve user-data from db
			$this->user = $this->_get_user($session_user_id, $session_token);
			// login successful
			return TRUE;
		}
		// session check failed
		return FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * check cookie
	 *
	 * @description	try to login using cookies
	 * 
	 */
	function _check_cookie()
	{
		// get cookies 
		$cookie_user_id = get_cookie('fs_user_id',TRUE);
		$cookie_token 	= get_cookie('fs_token_name',TRUE);
		// check if cookies are set
		if( $cookie_user_id != null && $cookie_token != null )
		{
			// retrieve user-data from db
			$this->user = $this->_get_user($cookie_user_id, $cookie_token);
			// set session
			$this->CI->session->set_userdata(array('fs_user_id' => $cookie_user_id, 'fs_token_name' => $cookie_token, 
				'time_log' => date('d.M.Y - H:i:s')));
			// login successful
			return TRUE;
		}
		// cookie check failed
		return FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * check login
	 *
	 * @description	compare username & password with db and set user variable
	 * 
	 */
	function _check($username, $password)
	{
		// check if user exist & password & retrieve user data
		if($this->user = $this->_check_password($username, $password) )
		{
			// check if user is active
			if($this->user['status'] === '1')
			{
				if( $this->_check_attempts() !== FALSE)
				{
					// create token
					$this->_create_token();
					// update database with token
					db_update(config('db_user'), array('id' => $this->user['id']), array('token' => $this->user['token']));
					// set log data
					$log = array('message' => lang('login_successful'), 'username' => $username);
					// log login
					$this->CI->fs_log->raw_log(array('type' => 2, 'user_id' => $this->user['id'], 'data' => $log));			
					// clear failed login attempts
					$this->_clear_attempts();
					// return TRUE
					return TRUE;
				}
				else
				{
					// delete user data from user variable
					unset($this->user);
					// return FALSE
					return FALSE;	
				}
			}
			// if user is blocked
			else
			{
				// delete user data from user variable
				unset($this->user);
				// set log data
				$log = array('message' => lang('error_deactive_user'), 'username' => $username);
				// log login attempt
				$this->CI->fs_log->raw_log(array('type' => 1, 'data' => $log));
				// set error message
				$this->CI->form_validation->add_message(lang('error_deactive_user'), 'username');
				// return FALSE
				return FALSE;
			}
		}
		// if user does not exists
		elseif( !$this->_get_user($username, 'all') )
		{
			// set log data
			$log = array('message' => lang('error_wrong_user'), 'username' => $username);
			// log login attempt
			$this->CI->fs_log->raw_log(array('type' => 1, 'data' => $log));
			// set error message
			$this->CI->form_validation->add_message(lang('error_wrong_user'), 'username');
			// return FALSE
			return FALSE;
		}
		// if user & password do not match
		else
		{
			// increase failed attempts
			$this->_increase_attempt($username);
			// increase password fail session
			$this->CI->form_validation->add_form_data('fs_password_fails', 1+$this->user['attempts']);
			// delete user data from user variable
			unset($this->user);
			// set log data
			$log = array('message' => lang('error_wrong_password'), 'username' => $username);
			// log login attempt
			$this->CI->fs_log->raw_log(array('type' => 1, 'data' => $log));				
			// set error message
			$this->CI->form_validation->add_message(lang('error_wrong_password'), 'password');
			// return FALSE
			return FALSE;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * _check_password
	 *
	 * @description	check if password fits to user and is correct
	 * 
	 */
	function _check_password( $username, $password )
	{
		// get user data
		$user = $this->_get_user($username, 'all');
		// compare passwords
		if(trim($user['password']) === prep_password($password, $user['salt']) )
		{	
			// unset password & salt from $user
			unset($user['password'], $user['salt']);
			// return user data
			return $user;
		}
		// check failed 
		return FALSE; 
	}
	// --------------------------------------------------------------------
	/**
	 * get user data
	 *
	 * @description	get all user data for given user from DB
	 * 
	 */
	function _get_user_data( $user = null )
	{
		// check if user exists
		if( $user != null )
		{
			// select user groups & user rights from db
			$user_data = index_array(db_select( config('db_prefix').config('db_data'), array('key' => 'user'), 
			array('select' => 'data, type')), 'type', TRUE);
			$user_data['group'] = index_array($user_data['group'], '_id');
			// add user group data
			$user['group'] = $user_data['group'][$user['group']];
			// add fitting group and rights to user
			foreach($user_data['right'] as $right)
			{
				if( in_array($right['_id'], $user_data['group'][$user['group']['_id']]) )
				{
					$user['rights'][$right['_id']] = $right;
				}
			}
			// check if user images are set
			if( isset($user['images']) && is_array($user['images']) )
			{
				$user['images'] != null && count($user['images']) > 0 ? $id = array('id' => $user['images']) : '';
				// get user image
				$db_images = db_select(config('db_files'), array( $id, 'status' => 1 ), 
				array('select' => 'id, filename, data', 'json' => 'data', 'single' => FALSE, 'index' => 'id'));
				// if images exits, assign to user
				if( $db_images != null )
				{
					$user['images'] = $db_images;
					// add profile picture
					$user['profile_image'] = $db_images[$user['profile_image']];
				}
			}
			
			// return user array
			return $user;
		}
		// if user does not exist
		else
		{
			return FALSE;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * get user
	 *
	 * @description	get all user data for given username from DB
	 * 
	 */
	function _get_user($user, $token = null)
	{
		// get user by name also if status != 1
		if( $token == 'all' )
		{
			return $this->_get_user_data( db_select( config('db_user'), array(array('user' => $user, 'email' => $user)), array('single' => TRUE) ) );
		}
		// get user by name		
		elseif( $token == null )
		{
			return $this->_get_user_data( db_select( config('db_user'), array(array('user' => $user, 'email' => $user), 'status' => 1), 
			array('single' => TRUE) ) );
		}
		// get user by token & id
		else
		{
			return $this->_get_user_data( db_select( config('db_user'), array('id' => $user, 'token' => $token, 'status' => 1), array('single' => TRUE) ) );
		}
	}
	// --------------------------------------------------------------------
	/**
	 * increase attempt
	 *
	 * @description	increase the attempt count for user
	 * 
	 */
	function _increase_attempt( $user )
	{
		db_update( config('db_user'), array(array('user' => $user, 'email' => $user)), 
					array('data/attempts' => 1+(isset($this->user['attempts']) ? $this->user['attempts'] : 0), 'data/attempt_time' => time() ), TRUE, 'data' );
	}
	// --------------------------------------------------------------------
	/**
	 * check attempts
	 *
	 * @description	check if attempt limit is reached
	 * 
	 */
	function _check_attempts( )
	{
		if( isset($this->user['attempts']) && $this->user['attempts'] >= config('attempts') )
		{
			if( ( $this->user['attempt_time'] + config('reset_attempts') ) > time() )
			{
				// set user block sessions
				$this->CI->form_validation->add_form_data('user_blocked', 'TRUE');
				// set error message
				$this->CI->form_validation->add_message(lang('error_temp_block'), 'username');
				// limit reached
				return FALSE;	
			}
			else
			{
				$this->_clear_attempts();
			}
		}
		elseif( ( (isset($this->user['attempt_time']) ? $this->user['attempt_time'] : 0) + config('reset_attempts') ) < time() )
		{
			$this->_clear_attempts();			
		}
		// return TRUE
		return TRUE;
	}
	// --------------------------------------------------------------------
	/**
	 * clear attempts
	 *
	 * @description	check if attempt limit is reached
	 * 
	 */
	function _clear_attempts( )
	{
		// set attempts to 0
		$this->user['attempts'] = 0;
		// update db
		db_update( config('db_user'), array('id' => $this->user['id']), array('data/attempts' => 0 ), TRUE, 'data' );
	}
	// --------------------------------------------------------------------
	/**
	 * create token
	 *
	 * @description	create user token and update session
	 * 
	 */	
	function _create_token()
	{
		// create token
		$this->user['token'] = random_string('alnum', mt_rand(75, 100));			
		// set session
		$this->CI->session->set_userdata(array(
			'fs_user_id' => $this->user['id'], 
			'fs_token_name' => $this->user['token'], 
			'time_log' => date('d.m.Y - H:i:s'))
		);
	}
	// --------------------------------------------------------------------
	/**
	 * check ip_attempts
	 *
	 * @description	check if attempt limit is reached by ip
	 * 
	 */
	function _check_ip_attempts( )
	{
		// get logs
		$logs = $this->CI->fs_log->get(array('type' => 1, 'user_ip' => $_SERVER['REMOTE_ADDR']), config('ip_lockout'));
		// check if enough fails are in log
		if( count($logs) >= config('ip_lockout') )
		{
			if( ($logs[key($logs)]['time'] + config('reset_attempts')) > time() )
			{
				// set error message
				$this->CI->form_validation->add_message(lang('error_ip_block'), 'manual');
				// return false
				return FALSE;
			}
		}
		// return true
		return TRUE;
	}
	// --------------------------------------------------------------------
	/**
	 * _validate_username
	 *
	 * @description	check if username exists in database
	 * 
	 */
	function _validate_user( $username )
	{
		if( !$this->_get_user($username) )
		{
			// user does not exist
			return FALSE;
		}
		// user exist in 
		return TRUE;
	}
	// --------------------------------------------------------------------
	/**
	 * _validate_password
	 *
	 * @description	check if password fulfills criteria
	 * 
	 */
	function _validate_password( $password, $length = '6', $type = 'alphanum', $chars = '-_+,.&$!#@' )
	{
		// check if letters are okay
		if( $type == 'alphanum' || $type == 'alpha' )
		{
			$regex[] = 'a-zA-Z';
		}
		// check if numbers are okay
		if( $type == 'alphanum' || $type == 'num' )
		{
			$regex[] = '0-9';
		}
		// check if special chars are set
		if( $chars != null && $chars != '' )
		{
			$regex[] = $chars;
		}
		// length
		if( $length != null && $length != '' )
		{
			$len = explode('-', $length);
			//
			$length = $len[0].','.(isset($len[1]) ? $len[1] : '');
		}
		// check password
		if( preg_match('/^['.implode('',$regex).']{'.$length.'}$/', $password) )
		{
			// user does not exist
			return FALSE;
		}
		// user exist in 
		return TRUE;
	}
	// --------------------------------------------------------------------
	/**
	 * _validate_retrieval
	 *
	 * @description	check retrieval key & time are valid
	 * 
	 */
	function _validate_retrieval( )
	{	
		// get retrival key & user from url
		foreach(explode('+',last_segment()) as $key => $value)
		{
			$value = explode(':',$value);
			$retrieval[$value[0]] = $value[1];
		}
		// check if user & key are set
		if( isset($retrieval['user']) && isset($retrieval['key']) )
		{
			if( $this->user['retrieval_key'] === $retrieval['key'] && $this->user['retrieval_key'] <= time() )
			{
				echo "TEST";
			}
		}
		// return FALSE
		return FALSE;
	}
// end of class
}
/* End of file fs_authentication.php */
/* Location: ./system/libraries/fs_authentication.php */