<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Profile extends MY_Controller {

	public function index( $method = null, $page = null )
	{
		// load assets
		css_add('sidebar, settings');
		js_add('fs.gui,profile');
		// content
		$this->data['content'] = $this->general();
		// setup menu
		$this->data['settings_menu'] = '<div id="sidebar">
			<ul class="menu">
				<li><a class="item active" href="'.base_url().'profile">General Information</a></li>
			</ul></div>';
		// load method
		if(variable($method) != null)
		{
			$this->$method($page);
		}
		else
		{
	    	// load into template
        	view('profile/index', $this->data);
		}
	}
	// -----------------------------------
	// general information
	function general()
	{
		// get data
		$data = user('array');
		// show template
		return $this->load->view('profile/general', $data, TRUE);
	}
	// -----------------------------------
	// save info
	function save( $page = null )
	{
		// prepare db sekeletons
		$db_skeleton['general'] = array('email','old_password','name','new_password','re_password');
		//
		if( $page != null ) 
		{
			$data = db_prepare_data($db_skeleton[$page], FALSE);
		}
		// apply specific methods
		if( $page == 'general')
		{
			// validateion
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'email', 'trim|xss_clean|required|valid_email');
			$this->form_validation->set_rules('name', 'name', 'trim|xss_clean|required');
			$this->form_validation->set_rules('new_password', 'new_password', 'trim|xss_clean|matches[re_password]|min_length[8]');
			$this->form_validation->set_rules('re_password', 're_password', 'trim|xss_clean');
			$this->form_validation->set_rules('old_password', 'old_password', 'trim|xss_clean|required');
			// validate form
			if($this->form_validation->run() === TRUE)
			{
				// build name
				$name = explode(' ', $data['name']);
				$data['data']['firstname'] = ucfirst(strtolower(trim($name[0])));
				$data['data']['lastname'] = ucfirst(strtolower(trim($name[1])));
				if( $data['new_password'] != null && strlen($data['new_password']) > 7 )
				{
					$data['password'] = prep_password($data['new_password'], user('salt'));
				}
				// update db
				if( user('password') === prep_password($data['old_password'], user('salt')) )
				{
					// unset fields
					unset($data['new_password'], $data['old_password'], $data['re_password'], $data['name']);
					// update db
					db_update(config('db_user'), array('id' => user('id')), $data, TRUE, array('data'));
					// success
					echo json_encode(array("success" => 'true'));	
				}
				else
				{
					$errors['old_password'] = 'old_password';
					echo json_encode(array('errors' => $errors));
				}
			}
			else
			{
				foreach( array('email','name','new_password','old_password','re_password') as $type )
				{
					if( form_error($type) != null )
					{
						$errors[$type] = form_error($type);
					}
				}
				// check if old_pw is correct
				if( user('password') !== prep_password($data['old_password'], user('salt')) )
				{
					$errors['old_password'] = 'old_password';	
				}
				// return error
				echo json_encode(array('errors' => $errors));
			}
		}
	}
}

/* End of file profile.php */