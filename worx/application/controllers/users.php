<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Users extends MY_Controller {

	function __construct()
	{
		parent::__construct();		
	}

	public function index( $method = null )
	{
		if( method_exists($this,$method) )
		{
			$this->$method();
		}
		elseif( $method == 'get_user_data' )
		{
			echo $this->_get_user_data();
		}
		elseif( $method == null )
		{
			$this->overview();
		}
	}
	/* -----------------------------------
	* overview
	*
	* @description show all users
	*
	*/
	public function overview()
	{
		// load assets
		css_add('widgets, users');
		js_add('fs.base,fs.dialog,users');		
		// fetch users
		$users = db_select(config('db_user'), '' ,array( 'select' => 'id, status, email, user, group, data', 'json' => 'data'));
		// get user groups
		$groups = config('user/group');
		//
		if( user('rights') != null )
		{
			// create cards
			$cards[] = $this->load->view('user/add_user_card', '', TRUE);
			foreach( $users as $user)
			{
				if( array_key_exists($groups[$user['group']]['creator_right'], user('rights')) )
				{
					$cards[] = $this->load->view('user/user_card', $user, TRUE);
				}
			}
			// merge cards
			$this->data['user_cards'] = implode('',$cards);
			// load into template
			view('user/overview', $this->data);
		}
	}
	/* -----------------------------------
	* _get_user_data
	*
	* @description gets all the user data from db
	*
	*/
	public function _get_user_data()
	{
		$user 				= $this->fs_authentication->_get_user($this->input->post('id'));
		$user['groups'] 	= config('user/group');
		// prepare stores
		$stores = db_select(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => 'store'), array('json' => array('data')));
		if( isset($stores) && is_array($stores) )
		{
			foreach( $stores as $key => $store )
			{
				$user['stores'][$store['id']] = $store['name'];
			}
		}
		// get view
		echo $this->load->view('user/edit', $user, TRUE);
	}
	/* -----------------------------------
	* edit
	*
	* @description update user data in db
	*
	*/
	public function edit()
	{
		$user = $this->fs_authentication->_get_user($this->input->post('user'));
		// check if username already exists or is this user
		if( $user == null || $user['id'] == $this->input->post('id') )
		{
			$data['email'] 				= $this->input->post('email');
			$data['user'] 				= $this->input->post('user');
			$data['group'] 				= $this->input->post('group');
			$data['data']['store'] 		= $this->input->post('store');
			// prepare password
			if(trim($this->input->post('pass')) === trim($this->input->post('re_pass')) && trim($this->input->post('pass')) != '')
			{
				$password 					= create_password(trim($this->input->post('pass')));
				$data['password'] 			= $password['password'];
				$data['salt'] 				= $password['salt'];
			}
			elseif( trim($this->input->post('pass')) != null && trim($this->input->post('pass')) !== trim($this->input->post('re_pass')))
			{
				echo json_encode( array('error' => 'password') ); 
			}
			// prepare name
			$name = explode(" ",$this->input->post('name'));
			$data['data']['firstname'] 	= variable($name[0]);
			$data['data']['lastname'] 	= variable($name[1]);

			if( $this->input->post('id') != null )
			{
				if($this->input->post('pass') != '' && $this->validate_password(false) == 'FALSE')
				{
					echo json_encode( array('error' => 'password') ); 
				}
				else
				{
					// update db
					db_update(config('db_user'), array('id' => $this->input->post('id')), $data, array('merge' => TRUE, 'json' => array('data')) );
				}
			}
			else
			{
				if($this->input->post('pass') == null || $this->validate_password(false) == 'FALSE')
				{
					echo json_encode( array('error' => 'password') ); 
				}
				else
				{
					// insert into db
					$id = db_insert(config('db_user'), $data, array('data') );
					echo $id;
				}
			}
		}
		else
		{
			echo json_encode( array('error' => true));
		}
	}
	/* -----------------------------------
	* check_username
	*
	* @description checks if username exists already
	*
	*/
	function check_username( $id = null )
	{
		$user = $this->fs_authentication->_get_user($this->input->post('user'));
		if( (isset($user['id']) && $user['id'] != $this->input->post('id')) || $this->input->post('user') == '')
		{
			echo "FALSE";
		}
		else
		{
			echo "TRUE";
		}
	}
	/* -----------------------------------
	* check_email
	*
	* @description checks if email is valid
	*
	*/
	function check_email()
	{
		$this->load->helper('email');
		if( valid_email($this->input->post('email')) )
		{
			echo "TRUE";
		}
		else
		{
			echo "FALSE";
		}	
	}
	/* -----------------------------------
	* check_username
	*
	* @description checks if username exists already
	*
	*/
	function validate_password( $ajax = TRUE )
	{
		$pw = $this->input->post('pass');
		if( preg_match('/^[a-zA-Z0-9_]+$/',$pw) && strlen($pw) >= 6)
		{
			$return =  "TRUE";
		}
		else
		{
			$return =  "FALSE";
		}
		if($ajax == TRUE)
		{
			echo $return;
		}
		else
		{
			return $return;
		}
	}
	/* -----------------------------------
	* delete
	*
	* @description delete user
	*
	*/
	function delete()
	{
		db_delete(config('db_user'), array('id' => $this->input->post('id')));
	}
}

/* End of file users.php */