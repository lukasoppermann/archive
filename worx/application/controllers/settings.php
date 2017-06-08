<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Settings extends MY_Controller {

	public function index( $page = null, $action = null )
	{
		// retrieve settings data
		$this->data['settings'] = db_select(config('system/current/db_prefix').config('db_data'), array('key' => 'settings'), array('json' => array('data'), 'index' => 'type'));
		// sort stores
		if( isset($this->data['settings']['store']) )
		{
			foreach($this->data['settings']['store'] as $store)
			{
				$_store[$store['position']] = $store;
			}
			$this->data['settings']['store'] = $_store;
		}
		//
		if( in_array($page, array('save', 'new_store', 'delete_store', 'sort_store', 'save_post_to', 'save_follow'))  )
		{
			$this->$page( $action );
		}
		else
		{
			// load assets
			css_add('sidebar, settings, fs.sortable');
			js_add('fs.sortable, fs.gui,settings');
			// get active page
			if( $page == null || !in_array($page, array('contact', 'google', 'twitter', 'facebook', 'mailchimp')) )
			{
				$page = 'contact';
			}
			// set active
			$active[$page] = ' active';
			// setup menu
			$this->data['settings_menu'] = '<div id="sidebar">
				<ul class="menu">
					<li><a class="item'.variable($active['contact']).'" href="'.base_url().'settings/contact">Contact Information</a></li>
					<li><a class="item'.variable($active['google']).'" href="'.base_url().'settings/google">Google</a></li>
					<li><a class="item'.variable($active['twitter']).'" href="'.base_url().'settings/twitter">Twitter</a></li>
					<li><a class="item'.variable($active['facebook']).'" href="'.base_url().'settings/facebook">Facebook</a></li>
					<li><a class="item'.variable($active['mailchimp']).'" href="'.base_url().'settings/mailchimp">Mailchimp</a></li>
				</ul></div>';
			// load content
			$this->data['content'] = $this->$page( $action );
			// load into template
			 view('settings/index', $this->data);
		}
	}
	// -----------------------------------
	// contact information
	function contact()
	{
		if( isset($this->data['settings']['store']) )
		{
			foreach( $this->data['settings']['store'] as $store )
			{
				!isset($store['store_nr']) ? $store['store_nr'] = '' : $store['store_nr'];
				$this->data['stores'][$store['position']] = $this->load->view('settings/store', $store, TRUE);
			}
			ksort($this->data['stores']);
			$this->data['stores'] = implode('',$this->data['stores']);
		}
		//
		return $this->load->view('settings/contact', $this->data, TRUE);
	}
	// -----------------------------------
	// google
	function google()
	{
		return $this->load->view('settings/google', $this->data, TRUE);
	}
	// -----------------------------------
	// twitter
	function twitter( $action = null )
	{
		if($action == 'revoke')
		{
			fs_oauth_revoke('twitter');
		}
		elseif( $action == 'connect' )
		{
			$user = fs_oauth_userdata('twitter');
			if( $user == FALSE )
			{
				// login and get user data
				fs_oauth_connect('twitter');
			}
			else
			{
				redirect(base_url().'settings/twitter');
			}
		}
		else
		{
			// try to get userdata
			$this->data['user'] = fs_oauth_userdata('twitter');
			if( isset($this->data['settings']['twitter']) )
			{
				$this->data['stored'] = $this->data['settings']['twitter'][0];
			}
		}
		return $this->load->view('settings/twitter', $this->data, TRUE);
	}
	// -----------------------------------
	// facebook
	function facebook( $action = null )
	{
		if($action == 'revoke')
		{
			fs_oauth_revoke('facebook');
		}
		elseif( $action == 'connect' )
		{
			$user = fs_oauth_userdata('facebook');
			if( !isset($user) || $user == FALSE )
			{
				// login and get user data
				fs_oauth_connect('facebook');
			}
			else
			{
				redirect(base_url().'settings/facebook');
			}
		}
		else
		{
			// try to get userdata
			$this->data['user'] = fs_oauth_userdata('facebook');
			if( isset($this->data['settings']['facebook']) )
			{
				$this->data['stored'] = $this->data['settings']['facebook'][0];
			}
			else
			{
				$this->data['stored'] = "";
			}
		}
		return $this->load->view('settings/facebook', $this->data, TRUE);
	}
	// -----------------------------------
	// mailchimp
	function mailchimp()
	{
		return "<form><label>This section is deactivated.</label></form>";
	}
	// -----------------------------------
	// save
	function save()
	{
		// get data
		$type = $this->input->post('type');
		// check for alphanum
		if( $this->input->post('alphanum') == 'alphanum' )
		{
			$data['data'][$this->input->post('key')] = strtolower(to_alphanum($this->input->post('value'), array(' ' => '-')));
		}
		else
		{
			$data['data'][$this->input->post('key')] = $this->input->post('value');
		}
		// check for required
		if( $this->input->post('required') == 'required' && $this->input->post('value') == "" )
		{
			echo json_encode(array('success'=>'error'));
			return false;
		}
		// check if id is submitted
		if($id = $this->input->post('id'))
		{
			// update db
			$updated = db_update(config('system/current/db_prefix').config('db_data'), array('id' => $id), $data, array('merge' => TRUE, 'json' => array('data')) );
		}
		else
		{
			// update db
			$updated = db_update(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => $type), $data, array('merge' => TRUE, 'json' => array('data')) );
		}
		if($updated == 0)
		{
			$fetch = db_select(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => $type));
			if( !isset($fetch) || (isset($fetch) && $fetch == FALSE) )
			{
				$data['key'] = 'settings';
				$data['type'] = $type;
				db_insert(config('system/current/db_prefix').config('db_data'), $data);
			}
		}
		//
		echo json_encode(array('success'=>'saved', 'updated' => $updated));
	}
	// -----------------------------------
	// new store
	function new_store()
	{
		// get data
		$data['key'] = 'settings';
		$data['type'] = 'store';
		// fetch stores from db
		$stores = db_select(config('system/current/db_prefix').config('db_data'), array('type' => 'store'), array('index' => 'id', 'index_single' => TRUE));
		// loop through stores
		foreach($stores as $id => $store)
		{
			if( isset($store['store_nr']) && $store['store_nr'] != '' )
			{
				$store_ids[] = trim($store['store_nr'],'0');
				unset($stores[$id]);
			}
		}
		// get first new id
		$store_id = isset($store_ids) ? max($store_ids) : 0;
		// loop through new stores
		foreach( $stores as $id => $store )
		{
			$store_id = $store_id+1;
			db_update(config('system/current/db_prefix').config('db_data'), array('id' => $id), array('data/store_nr' => str_pad($store_id, 3, "0", STR_PAD_LEFT)), array('merge' => TRUE, 'json' => array('data')) );
			// increase store id
		}
		// 
		if( isset($this->data['settings']['store']) )
		{
			$position = 1;
			// update store
			foreach($this->data['settings']['store'] as $store)
			{
				db_update(config('system/current/db_prefix').config('db_data'), array('id' => $store['id']), array('data/position' => $position), array('merge' => TRUE, 'json' => array('data')));
				++$position;
			}
			$data['data']['position'] = $position;
		}
		else
		{
			$data['data']['position'] = 1;
		}
		// prepare permalink
		if( $this->input->post('permalink') != "" )
		{
			$permalink = $this->input->post('permalink');
		}
		else
		{
			$permalink = $this->input->post('name');
		}
		$data['data']['permalink'] = strtolower(to_alphanum($permalink, array(' ' => '-')));
		//
		$data['data']['name'] = $this->input->post('name');
		$data['data']['email'] = $this->input->post('email');
		$data['data']['phone'] = $this->input->post('phone');
		$data['data']['number'] = $this->input->post('number');
		$data['data']['address'] = $this->input->post('address');
		$data['data']['additional_address'] = $this->input->post('additional_address');
		$data['data']['trading_hours'] = $this->input->post('trading_hours');
		$data['data']['store_nr'] = str_pad($store_id+1, 3, "0", STR_PAD_LEFT);
		//
		if($data['data']['name'] != "" && $data['data']['email'] != "" && $data['data']['phone'] != "" && $data['data']['address'] != "")
		{
			// insert db
			$id = db_insert(config('system/current/db_prefix').config('db_data'), $data, array('data'));
			echo json_encode(array('success'=>'true', 'id' => $id));
		}
		else
		{
			echo json_encode(array('success'=>'false'));
		}
	}
	// -----------------------------------
	// delete store
	function delete_store()
	{
		db_delete(config('system/current/db_prefix').config('db_data'), array('id' => $this->input->post('id')));
		echo json_encode(array('success'=>'true'));
	}
	// -----------------------------------
	// sort store
	function sort_store()
	{
		foreach( $this->input->post('items') as $item => $position )
		{
			db_update(config('system/current/db_prefix').config('db_data'), array('id' => $item), array('data/position' => $position), array('merge' => TRUE, 'json' => array('data')) );
		}
	}
	// -----------------------------------
	// save follow
	function save_follow()
	{
		db_update(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => $this->input->post('type')), array('data/follow' => $this->input->post('id')), TRUE, array('data'));
		echo json_encode(array('success' => true));
	}
	// -----------------------------------
	// save post
	function save_post_to()
	{
		$data = db_select(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => $this->input->post('type')), array('json' => array('data'), 'single' => TRUE, 'unstack' => FALSE));
		// replace post
		$data['data']['post'] = $this->input->post('ids');
		// update db
		db_update(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => $this->input->post('type')), $data, FALSE, array('data'));
		echo json_encode(array('success' => true));
	}
	// -----------------------------------
}

/* End of file settings.php */