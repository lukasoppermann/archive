<?php

class Ajax extends MY_Controller {
	
	public function index()
	{
		
	}
	// ------------------------------------------
	// Entry Method
	public function entry($method)
	{
		if($method == 'save')
		{
			$this->entry_save($this->input->post('id'));
		}
		elseif($method == 'delete')
		{
			$this->entry_delete($this->input->post('id'));
		}
		elseif($method == 'trash')
		{
			$this->entry_trash($this->input->post('id'));
		}
		elseif($method == 'hero')
		{
			$this->entry_hero($this->input->post('id'));
		}
		elseif($method == 'pos')
		{
			$this->entry_pos();
		}
	}
	// ------------------------------------------
	// Media Method
	public function media($method)
	{
		if($method == 'upload')
		{
			$this->media_upload($this->input->get('id'));
		}
		elseif($method == 'delete')
		{
			echo $this->media_delete($this->input->post('id'));
		}
		elseif($method == 'edit')
		{
			$this->media_edit($this->input->post('id'));
		}
	}
	// ------------------------------------------
	// Data Method
	function data($method, $type = null)
	{
		if($method == 'add')
		{
			$this->data_add();
		}
		elseif($method == 'edit')
		{
			$this->data_edit();
		}
		elseif($method == 'delete')
		{
			$this->data_delete();			
		}
		elseif($method == 'get')
		{
			$this->data_get($type);			
		}
	}
	// ------------------------------------------
	// Settings Method
	public function settings($method)
	{
		if($method == 'save')
		{
			$this->settings_save($this->input->post('page'));
		}
		elseif($method == 'twitter')
		{
			$this->twitter_connect();
		}
	}
	// ------------------------------------------------------------------------------------------------------------------------------
	// Functions - Entry
	// ------------------------------------------
	// Entry Save
	function entry_hero($id)
	{
		// update database
		$data = array(
			'menu_id' => variable($this->input->post('menu_id'))
		);

		$this->db->where('id', $id);
		$this->db->update('client_entries', $data);
	}
	// ------------------------------------------
	// Entry Save
	function entry_pos(  )
	{
		$products = $this->input->post('products');
		// loop through wheres
		foreach( $products as $id => $pos )
		{
			$this->db->or_where('id', $id);
		}
		// fetch
		$result = $this->db->get('client_entries')->result_array();
		// edit products
		foreach( $result as $item )
		{
			// change position
			$item['data'] = json_decode($item['data'], TRUE);
			$item['data']['position'] = $products[$item['id']];
			$item['data'] = json_encode($item['data']);
			// update
			$this->db->where('id', $item['id']);
			$this->db->update('client_entries', array('data' => $item['data']));
		}
	}
	// ------------------------------------------
	// Entry Save
	function entry_save($id)
	{
		// fetch data from POST
		$new_data['last_saved'] 		= date('d/m/Y, h:i a');
		// ---------------------------------------------------------------------------
		// get store data
		$stores = $this->input->post('store');
		if(isset($stores) && is_array($stores))
		{
			foreach($stores as $store)
			{
				$new_data['store'][] = $store;					
			}
		}
		else
		{
			$new_data['store'] = array();
		}
		// ---------------------------------------------------------------------------
		// check publication date
		$pdate	= explode('/', variable($this->input->post('datepicker')) );
		if( count($pdate) > 1 && checkdate($pdate[1], $pdate[0], $pdate[2]) === true )
		{
			$new_data['publication_date']	= variable($this->input->post('datepicker'));
			// check if sale is active
			if( mktime(0,0,0, $pdate[1], $pdate[0], $pdate[2]) < time() )
			{
				// set sale to true
				$social_post = 'false';
			}
		}
		else
		{
			$new_data['publication_date'] = null;
		}
		// ---------------------------------------------------------------------------
		// check if sales is set
		if( $new_data['sales_start'] = $this->input->post('sales_start') )
		{
			$_s = explode('/', variable($new_data['sales_start']) );
			if( count($pdate) > 1 && checkdate($_s[1], $_s[0], $_s[2]) !== true )
			{
				$new_data['sales_start'] = '';
			}
			else
			{
				$new_data['sales_end'] = $this->input->post('sales_end');
				$_s = explode('/', variable($new_data['sales_end']) );
				if( count($pdate) > 1 && checkdate($_s[1], $_s[0], $_s[2]) !== true )
				{
					$new_data['sales_end'] = '';
				}
			}
		}
		else
		{
			$new_data['sales_end'] = $new_data['sales_start'] = '';
		}
		// ---------------------------------------------------------------------------
		// twitter & facebook update
		$new_data['twitter']			= variable($this->input->post('twitter'));
		$new_data['facebook']			= variable($this->input->post('facebook'));
		// create url
		if($new_data['twitter'] != null || $new_data['facebook'] != null)
		{
			// news page
			if( $this->input->post('type') == '1' )
			{
				$url = 'home';
			}
			// product
			else
			{
				// check for store
				if( isset($new_data['store']) && count($new_data['store']) > 0)
				{
					$store = $new_data['store'][key($new_data['store'])];
				}
				// check for sale
				if( $new_data['sales_start'] != null )
				{
					$store = 'sale';
				}
				//
				if( isset($store) && $store != null )
				{
					// assign url
					$url = $store.'/#'.$id;
				}
				else
				{
					$url = 'home';
				}
			}
			$url = config('client_base').'/'.$url;
			// assign data
			if( $new_data['twitter'] == 'twitter' )
			{
				$new_data['tweet']				= $this->input->post('tweet').' '.tiny_url($url);
			}
			if( $new_data['facebook'] == 'facebook' )
			{
				$new_data['fb_update']			= $this->input->post('fb_update').' '.tiny_url($url);
			}
		}
		// ---------------------------------------------------------------------------
		// 
		$new_data['news']				= variable($this->input->post('news'));		
		$new_data['news_update']		= variable($this->input->post('news_update'));
		$new_data['meta_title']			= $this->input->post('meta_title');
		$new_data['product_type']		= $this->input->post('product_type');
		$new_data['designer']			= $this->input->post('designer');
		$new_data['price']				= number_format($this->input->post('price'), 2, '.','');
		$new_data['sales_price']		= number_format($this->input->post('sales_price'), 2, '.','');		
		$new_data['product_code']		= $this->input->post('product_code');
		$new_data['product_stock']		= $this->input->post('product_stock');		
		$new_data['sizes']				= explode(',',$this->input->post('product_sizes'));
		$new_data['sizes'] 				= array_map('trim', $new_data['sizes']);
		// retrieve from database
		$this->db->select('*');
		$this->db->where('id', $id);
		$this->db->from('client_entries');
		$query = $this->db->get();
		$row = $query->row_array();
		//---------------------------------------
		$_data = json_decode($row['data'],TRUE);
		if( is_array($_data) )
		{
			$data['data'] = $_data;
		}
		else
		{
			$data['data'] = array('');
		}
		// ------------------------------------------------------------------
		// TWITTER post
		//
		// if status is published -> tweet
		if($this->input->post('status') == 1 && $new_data['twitter'] == 'twitter' && $new_data['tweet'] != null)
		{
			if($new_data['publication_date'] == null || $social_post != 'false' )
			{
				// set twitter to false
				$new_data['twitter'] = '';			
				$data['data']['twitter'] = '';
				$return['twitter'] = false;
				// tweet
				$this->tweet->call('post', 'statuses/update', array('status' => $new_data['tweet']));
			}
		}
		// ------------------------------------------------------------------
		// retrieve images from database
		if( isset($data['data']['images']) )
		{
			$this->db->select('*');
			// get wheres
			foreach($data['data']['images'] as $key)
			{
				$this->db->or_where('id', $key);
			}
			$this->db->from('client_files');
			$images = $this->db->get()->result_array();
			// get hero or first
 			$fb_image = $images[key($images)];
			//
			foreach($images as $key => $values)
			{
				if( $values['key'] == 'hero' )
				{
					$fb_image = $images[$key];
				}
			}
			$fb_image['data'] = json_decode($fb_image['data'], TRUE);
			
			$fb_img_url = config('client_base').'/media/images/'.$fb_image['data']['filename'];
			$fb_caption = $fb_image['data']['label'];
		}
		// ------------------------------------------------------------------
		// FACEBOOK post
		//
		// if status facebook is published -> send status
		if($this->input->post('status') == 1 && $new_data['facebook'] == 'facebook')
		{
			if($new_data['publication_date'] == null || $social_post != 'false' )
			{
				// ---------------------------------
				// Facebook Connect	
				$this->load->library('facebook', array(
					'appId' => $this->config->item('facebook_api_key'), 
					'secret' => $this->config->item('facebook_secret_key'), 
					'cookie' => $this->config->item('facebook_cookie') ) );

				// try to login to fb 
				// Get User ID
				$this->facebook->setAccessToken(variable($this->cauth['fb_token']));
				$user = $this->facebook->getUser();
				// // user exists post
			
				if( isset($user) && $user != 0 )
				{
					try {
						// Proceed knowing you have a logged in user who's authenticated.
						$user_profile = $this->facebook->api('/me');
					} 
					catch (FacebookApiException $e) 
					{
						$user = null;	
						$key = $this->db->select('value')->where('type','fb_token')->where('key','auth')->from('client_data');
						$this->facebook->setAccessToken($key);
						$user = $this->facebook->getUser();
						$user_profile = $this->facebook->api('/me');
					}
					
					// if user exists
					if( isset($user_profile) && $user_profile != null )
					{
						$parameters = array(
						   'message' 		=> $this->input->post('fb_update'),	
						   'link' 			=> $url,
						   'name' 			=> $this->input->post('title'),
						);
						if( isset($fb_img_url) )
						{
							$parameters['picture'] = $fb_img_url;
						}
						if( isset($fb_caption) )
						{
							$parameters['caption'] = $fb_caption;
						}
						//add the access token to it
						// $parameters['access_token'] = $this->cauth['fb_token'];
						
						$result = $this->facebook->api("/me/accounts");
						foreach($result["data"] as $page)
						{
							if($page["id"] == $this->config->item('facebook_page')) 
							{
								$parameters['access_token'] = $page["access_token"];
							}
						}
						// build and call our Graph API request
							$newpost = $this->facebook->api(
							   $this->config->item('facebook_page').'/feed',
							   'POST',
							   $parameters
							);
					}
					
					// Unset facebook
					$new_data['facebook'] = '';
					$data['data']['facebook'] = '';
					$return['facebook'] = false;
				}
			}
		}
		//
		$data['data'] = array_merge($data['data'], $new_data);
		$json_data = json_encode(array_filter($data['data']));
		// update database
		$data = array(
					'title' => $this->input->post('title'),
					'type' 	=> ($this->input->post('type') == null ? '3' : $this->input->post('type') ),
					'text' 	=>  $this->input->post('text'),
					'status' => ($this->input->post('status') == null ? '1' : $this->input->post('status')),
					'data' => $json_data
				);
		$this->db->flush_cache();
		$this->db->where('id', $id);
		$this->db->update('client_entries', $data);
		// return success
		$return['succes'] = 'true';
		echo json_encode($return);
	}
	// ------------------------------------------
	// Entry Delete
	function entry_delete($id)
	{
		// retrieve data from db
		$this->db->select('id, data');
		$this->db->from('client_entries');
		$this->db->where('id', $id);
		$query = $this->db->get();
		//
		$result = $query->row_array();
		// decode data
		if(is_array(json_decode($result['data'],TRUE)))
		{
			$result['data'] = json_decode($result['data'],TRUE);
		}
		else
		{
			$result['data'] = array('');
		}
		if((!isset($result['title']) || $result['title'] == 0) && (!isset($result['text']) || $result['text'] == 0) )
		{
			$_data = $result['data'];
			$_data = array_filter($_data);
			if(count($_data) == 0)
			{
				$this->entry_trash($id);
			}
		}
		// merge data
		$result['status'] = 3;
		// encode to json
		$result['data'] = json_encode($result['data']);
		// update db
		$this->db->where('id', $id);
		$this->db->update('client_entries', $result);
		// send json response
		echo json_encode(array('succes' => 'true', 'id' => $id));
	}
	// ------------------------------------------
	// Entry Trash (delete forever)
	function entry_trash($id = null)
	{
		if( is_int($id) )
		{
			$this->db->where('id', $id);
			// delete
			$this->db->delete('client_entries');
		}
		else
		{
			$this->db->select('id, status');
			$this->db->from('client_entries');
			$query = $this->db->get();
			//
			$c = 0;
			foreach ($query->result_array() as $row)
			{
				if($row['status'] == 3)
				{
					$c++;
					$this->db->or_where('id', $row['id']);
				}
			}
			if($c > 0)
			{
				// delete
				$this->db->delete('client_entries');
			}
		}
		$this->db->query('ALTER TABLE `client_entries` AUTO_INCREMENT = 1');
		echo json_encode(array('success' => true, 'id' => $id));
	}
	// ------------------------------------------------------------------------------------------------------------------------------
	// Functions - Settings
	// ------------------------------------------
	// Settings Save
	function settings_save($page)
	{
		if($page == 'page')
		{
			$form_data = array('page_name','analytics');
			foreach($form_data as $k => $key)
			{
				$val = $this->input->post($key);
				if(variable($val) != null )
				{
					$this->db->from('client_data');
					$this->db->where('type', $key);
					$this->db->where('key','settings');
					$results = $this->db->count_all_results();					
					if ($results == 0) 
					{
						$this->db->insert('client_data', array('key' => 'settings', 'type' => $key, 'value' => $val));
					}
					else
					{
						// Update DB
						$this->db->where('key','settings');	
						$this->db->where('type',$key);
						$this->db->update('client_data', array('value' => $val));
					}
				}
				$val = null;
			}
		}
		// ----------------------------------------------------------------------------------------------------------------
		// PERSONAL
		elseif($page == 'personal')
		{	
			// get user id
			$id = $this->input->post('user_id');
			// get user data from db
			$this->db->from('users');
			$this->db->where('id', $id);
			$query = $this->db->get();
			$user = $query->result_array();
			$user = $user[0];
			$user['data'] = json_decode($user['data'], TRUE);
			// validateion
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<div class="error"><p>', '</p></div>');
			$this->form_validation->set_rules('email', 'email', 'trim|xss_clean|required|valid_email');
			$this->form_validation->set_rules('password', 'password', 'trim|xss_clean|matches[repassword]');
			$this->form_validation->set_rules('repassword', 'password', 'trim|xss_clean|');
			// validate form
			if($this->form_validation->run() === TRUE)
			{
				// update author name
				$author_name = explode(' ', trim($this->input->post('author_name')));
				(variable($author_name[0]) != null) ? $user['data']['firstname'] = $author_name[0] : '';
				(variable($author_name[1]) != null) ? $user['data']['lastname'] = $author_name[1] : '';
				// update keep login
				$user['data']['keep_login'] = $this->input->post('keep_login');
				// update email
				$user['email'] = $this->input->post('email');
				// update password
				if(variable($this->input->post('password')) != null)
				{				
					$password = $this->authentication->prep_password($this->input->post('password'), $user['salt']);
					$user['password'] = $password;
				}
				// json encode data
				$user['data'] = json_encode($user['data']);
				// update db
				$this->db->where('id',$id);	
				$this->db->update('users', $user);
				// success
				echo json_encode(array("success" => '<div class="success">Your information has been updated.</div>'));
			}
			else
			{
				echo json_encode(array("error" => validation_errors()));
			}
		}
		// ----------------------------------------------------------------------------------------------------------------
		// USER
		elseif($page == 'user')
		{	

			// check if username exists in db
			$this->db->from('users');
			$this->db->where('user', $this->input->post('username'));
			//
			if($this->db->count_all_results() == 0)
			{
				// validateion
				$this->load->library('form_validation');
				$this->form_validation->set_error_delimiters('<div class="error"><p>', '</p></div>');
				$this->form_validation->set_rules('email', 'email', 'trim|xss_clean|required|valid_email');
				$this->form_validation->set_rules('password', 'password', 'trim|required|xss_clean|matches[repassword]');
				$this->form_validation->set_rules('repassword', 'password', 'trim|required|xss_clean|');
				// validate form
				if($this->form_validation->run() === TRUE)
				{
					// update keep login
					$user['data']['keep_login'] = true;
					// group
					$user['group'] = $this->input->post('group');
					// user
					$user['user'] = $this->input->post('username');
					// update email
					$user['email'] = $this->input->post('email');
					// update password
					$user['salt'] = random_string('alnum', mt_rand(16, 32));
					$user['password'] = trim($this->authentication->prep_password($this->input->post('password'), $user['salt']));
					// json encode data
					$user['data'] = json_encode($user['data']);
					// update db
					$this->db->insert('users', $user);
					// success
					echo json_encode(array("success" => '<div class="success">Your information has been updated.</div>'));
				}
				else
				{
					echo json_encode(array("error" => validation_errors()));
				}
			}
			else
			{
				echo json_encode(array("error" => '<div class="error"><p>This username already exists.</p></div>'));
			}
		}
	}
	// ------------------------------------------------------------------------------------------------------------------------------
	// Functions - Data
	// ------------------------------------------
	// Data Add
	function data_add()
	{
		// data for both
		$data['key'] 	= 'product';
		$data['value']['label'] 	= $this->input->post('label');
		$data['value']['tag']		= strtolower(str_replace(array(' ', '&'), array('-','+'), replace_accents($this->input->post('label'))));
		$data['value']['position']	= $this->input->post('position');
		// add product type
		if($this->input->post('type') == 'product-type')
		{
			$data['type'] 	= 'type';
			// create sizes
			if( $this->input->post('sizes') != '' && $this->input->post('sizes') != 'Default sizes separated by ","' )
			{
				$data['value']['sizes'] 	= explode(',',$this->input->post('sizes'));
				foreach($data['value']['sizes'] as $key => $val)
				{
					$data['value']['sizes'][$key] = trim($val);
				}
			}
		}
		// add designer
		else
		{
			$data['type'] 	= 'designer';
		}
		// return data
		$return = $data['value'];
		$return['success'] = 'true';
		// check for updating others
		if( $this->input->post('position') != $this->input->post('last_pos'))
		{
			// fetch other entries
			$this->db->select('id, value');
			$this->db->where('type', $data['type']);
			$items = $this->db->get('client_data')->result_array();
			// loop through items
			foreach($items as $item)
			{
				$item['value'] = json_decode($item['value'], TRUE);
				// check if not the edited item
				if($item['id'] != $this->input->post('id'))
				{
					if($item['value']['position'] >= $this->input->post('position'))
					{
						$item['value']['position']++;
						// json encode
						$item['value'] = json_encode($item['value']);
						// update item
						$this->db->where('id', $item['id']);
						$this->db->update('client_data', array('value' => $item['value']));
					}
				}
			}
		}
		// insert new item
		if(isset($data))
		{
			// json encode data
			$data['value'] = json_encode($data['value']);
			// insert item
			$this->db->insert('client_data', $data);
			// get return id
			$return['id'] = $this->db->insert_id();
			// send data
			echo json_encode($return); 
		}
	}
	// ------------------------------------------
	// Data get
	function data_get($type)
	{
		$this->load->model('content_model');
		
		if($type == 'product-type')
		{
			$type = 'type';
		}
		
		echo json_encode(array(
			'edit' => $this->content_model->cat_select_edit('product_type_edit', $type, array('label','sizes','tag','position')), 
			'normal' => $this->content_model->cat_select('product_type', $type)
		));
	}
	// ------------------------------------------
	// Data Add
	function data_edit()
	{
		// data for both
		$data['key'] 				= 'product';
		$data['value']['label'] 	= $this->input->post('label');
		$data['value']['tag']		= strtolower(str_replace(array(' ', '&'), array('-','+'), replace_accents($this->input->post('label'))));
		$data['value']['position']	= $this->input->post('position');
		// add product type
		if($this->input->post('type') == 'product-type')
		{
			$data['type'] 	= 'type';
			// create sizes
			if( $this->input->post('sizes') != '' && $this->input->post('sizes') != 'Default sizes separated by ","' )
			{
				$data['value']['sizes'] 	= explode(',',$this->input->post('sizes'));
				foreach($data['value']['sizes'] as $key => $val)
				{
					$data['value']['sizes'][$key] = trim($val);
				}
			}
		}
		// add designer
		else
		{
			$data['type'] 	= 'designer';
		}
		// get changed field
		$this->db->select('value');
		$this->db->where('id', $this->input->post('id'));
		$result = $this->db->get('client_data')->row_array();
		$result = json_decode($result['value'], TRUE);
		// new position
		$new_position = $this->input->post('position');
		// check if position has been changed
		if($result['position'] != $new_position)
		{
			// check for forwards or backwards
			if($result['position'] > $data['value']['position'])
			{
				$move = 'front';
			}
			else
			{
				$data['value']['position']--;
				$move = 'back';
			}
			// fetch other entries
			$this->db->select('id, value');
			$this->db->where('type', $data['type']);
			$items = $this->db->get('client_data')->result_array();
			foreach($items as $item)
			{
				$item['value'] = json_decode($item['value'], TRUE);
				// check if not the edited item
				if($item['id'] != $this->input->post('id'))
				{
					if($move == 'front' && $item['value']['position'] >= $new_position && $item['value']['position'] <= $result['position'])
					{
						$item['value']['position']++;
						// json encode
						$item['value'] = json_encode($item['value']);
						// update item
						$this->db->where('id', $item['id']);
						$this->db->update('client_data', array('value' => $item['value']));
					}
					elseif($move == 'back' && $item['value']['position'] > $result['position'] && $item['value']['position'] < $new_position)
					{
						$item['value']['position']--;							
						// json encode
						$item['value'] = json_encode($item['value']);
						// update item
						$this->db->where('id', $item['id']);
						$this->db->update('client_data', array('value' => $item['value']));
					}
				}
			}
		}
		// ----------------------------------------------
		// create return json
		$return = json_encode(array('success' => 'true', 'label' =>  $data['value']['label']));
		// merge date and json_encode
		$data['value'] = json_encode(array_merge($result, $data['value']));
		// return data
		if(isset($data))
		{
			$this->db->where('id', $this->input->post('id'));
			$this->db->update('client_data', $data);
			echo $return; 
		}
	}
	// ------------------------------------------
	// Data delete
	function data_delete()
	{
		$this->db->where('id', $this->input->post('id'));
		$this->db->delete('client_data');
		$this->db->query('Alter table client_data Auto_increment = 1'); 
		// check if others need to be updated
		if($this->input->post('position') != $this->input->post('last_pos'))
		{
			// get type
			$type = $this->input->post('type');
			if($type == 'product-type')
			{
				$type = 'type';
			}
			//
			$this->db->select('id, value');
			$this->db->where('type', $type);
			$items = $this->db->get('client_data')->result_array();
			foreach($items as $item)
			{
				$item['value'] = json_decode($item['value'], TRUE);
				// check if not the edited item
				if($item['id'] != $this->input->post('id'))
				{
					if($item['value']['position'] > $this->input->post('position'))
					{
						$item['value']['position']--;							
						// json encode
						$item['value'] = json_encode($item['value']);
						// update item
						$this->db->where('id', $item['id']);
						$this->db->update('client_data', array('value' => $item['value']));
					}
				}
			}
		}
	}
	// ------------------------------------------------------------------------------------------------------------------------------
	// Functions - Media
	// ------------------------------------------
	// Media Upload
	function media_upload($id)
	{
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array('jpeg','png','gif','jpg');
		// max file size in bytes
		$sizeLimit = 8 * 1024 * 1024;
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload($this->input->get('dir'));
		// update entry
		// retrieve data from db
		$this->db->select('id, data');
		$this->db->from('client_entries');
		$this->db->where('id', $id);
		$query = $this->db->get();
		//
		$entry = $query->row_array();
		// decode data
		if(is_array(json_decode($entry['data'],TRUE)))
		{
			$entry['data'] = json_decode($entry['data'],TRUE);
		}
		else
		{
			$entry['data'] = array('');
		}
		// merge data
		$entry['data']['images'][] = $result['id'];
		// encode to json
		$entry['data'] = json_encode($entry['data']);
		// update db
		$this->db->where('id', $id);
		$this->db->update('client_entries', $entry);
		// to pass data through iframe you will need to encode all html tags
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}
	// ------------------------------------------
	// Media edit
	private function toBytes($str)
	{
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
	// ------------------------------------------
	// Media edit
	function media_edit($id = null)
	{
		// retrieve data from db
		$this->db->select('*');
		$this->db->from('client_files');
		$this->db->where('id', $id);
		$query = $this->db->get();
		//
		$file = $query->row_array();
		$file['data'] = json_decode($file['data'], TRUE);
		// prepare update
		foreach($this->input->post('update') as $key => $val)
		{
			if($key == 'data')
			{
				$file[$key] = array_merge($file[$key], $val);
			}
			else
			{
				$file[$key] = variable($val);
			}
		}
		// prepare data
		$file['data'] = json_encode($file['data']);
		// update db
		$this->db->where('id', $id);
		$this->db->update('client_files', $file);
	}
	// ------------------------------------------
	// Media delete
	function media_delete($id)
	{
		// retrieve data from db
		$this->db->select('id, data');
		$this->db->from('client_entries');
		$this->db->where('id', $id);
		$query = $this->db->get();
		//
		$entry = $query->row_array();
		// decode data
		if(is_array(json_decode($entry['data'],TRUE)))
		{
			$entry['data'] = json_decode($entry['data'],TRUE);
		}
		else
		{
			$entry['data'] = array('');
		}
		// find key
		$_key = array_search($this->input->post('img_id'), $entry['data']['images']);
		unset($entry['data']['images'][$_key]);
		// delete image
		$this->media_trash($this->input->post('img_id'));
		// prepare data
		$entry['data'] = json_encode($entry['data']);
		// update db
		$this->db->where('id', $id);
		$this->db->update('client_entries', $entry);
		// return true
		return json_encode(array('success' => true));
	}	
	// ------------------------------------------
	// Media trash
	function media_trash($id)
	{
		// get all file paths incl. thumbs
		$this->db->select('*');
		$this->db->from('client_files');
		$this->db->where('id', $id);
		$query = $this->db->get();
		$file = $query->row_array();
		// decode data
		$file['data'] = json_decode($file['data'], TRUE);
		// loop thorugh paths and unlink
		$paths = array('cms_full_path','cms_thumb_150');
		foreach($paths as $path)
		{
			if(isset($file['data'][$path]))
			{
				// delete file
				unlink($file['data'][$path]);
			}
		}
		// delete from db
		$this->db->where('id', $id);
		$this->db->delete('client_files');
		// return true
		return json_encode(array('success' => true));
	}
		
// END of Controller
}
// ------------------------------------------
// qqUploadeder Functions
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) 
	{    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize())
		{            
            return false;
        }
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
    
	function getName()
	{
        return $_GET['qqfile'];
    }
    
	function getSize()
	{
        if (isset($_SERVER["CONTENT_LENGTH"]))
		{
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}
/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm{  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path))
		{
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader extends MY_Controller  {
    private $allowedExtensions = array();
    private $sizeLimit = 2621440; // 10485760
    private $file;
	private $CI;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){  
		$this->CI =& get_instance();
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();       
		
        if(isset($_GET['qqfile']))
		{
            $this->file = new qqUploadedFileXhr();
        }elseif(isset($_FILES['qqfile']))
		{
            $this->file = new qqUploadedFileForm();
        }
		else
		{
            $this->file = false; 
        }
    }

    private function checkServerSettings()
	{        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit)
		{
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }

    private function toBytes($str)
	{
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE)
	{
        if (!is_writable($uploadDirectory))
		{
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file)
		{
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0)
		{
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) 
		{
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $result['label'] = $result['filename'] = $filename = str_replace(array(' ', '&'), array('-','+'), replace_accents($_GET['filename']));
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions))
        {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
		// if replace file is false -> do not replace files
        if(!$replaceOldFile)
        {
			// don't overwrite previous files that were uploaded
			while(file_exists($uploadDirectory . $filename . '.' . $ext))
			{
				$result['filename'] = $filename .= '-'.rand(10, 99);
			}
		}
		// try to upload file
		if ($this->file->save($uploadDirectory . $filename . '.' . $ext))
		{
    		$result['path'] = $filename . '.' . $ext;
			$up_dir = str_replace('../','',$uploadDirectory);
			$result['full_path'] = $_GET['dir'].$result['path'];
			$result['size'] = $size;
			// ------------------------------------------------
			// creating thumbs and more
			// file
			$file_base = $_GET['dir'].$filename;
			// set configs for lib
			$size = getimagesize($_GET['dir'].$filename.'.'.$ext);
			// check dimensions
			$width 	= $size[0];
			$height = $size[1];
			if($width >= $height)
			{
				$new_h = 150;
				$new_w = $width/($height/$new_h);
			}
			else
			{
				$new_w = 150;
				$new_h = $height/($width/$new_w);
			}
			//
			$config['image_library'] = 'gd2';
			$config['source_image'] = $file_base.'.'.$ext;
			$config['new_image'] = $file_base.'_resized.'.$ext;
			$config['maintain_ratio'] = TRUE;
			$config['height'] = $new_h;
			$config['width'] = $new_w;
			// load config to lib
			$this->CI->load->library('image_lib', $config);
			// resize
			$this->CI->image_lib->resize();

			// -------------------------------
			// new settings for thumb 150
			$config['source_image'] = $file_base.'_resized.'.$ext;			
			$config['new_image'] = $file_base.'_thumb_150.'.$ext;
			$config['maintain_ratio'] = FALSE;
			$config['quality'] = 80;			
			$config['height'] = 150;
			$config['width'] = 150;
			$config['x_axis'] = '30';
			// apply settings
			$this->CI->image_lib->initialize($config); 
			// crop
			$this->CI->image_lib->crop();
			// -------------------------------
			if($width >= $height)
			{
				$new_h = 180;
				$new_w = $width/($height/$new_h);
			}
			else
			{
				$new_w = 180;
				$new_h = $height/($width/$new_w);
			}
			//
			$config['source_image'] = $file_base.'.'.$ext;
			$config['new_image'] = $file_base.'_resized_two.'.$ext;
			$config['maintain_ratio'] = TRUE;
			$config['height'] = $new_h;
			$config['width'] = $new_w;
			// apply settings
			$this->CI->image_lib->initialize($config); 
			// crop
			$this->CI->image_lib->resize();
			// -------------------------------
			// new settings for thumb 180
			$config['source_image'] = $file_base.'_resized_two.'.$ext;			
			$config['new_image'] = $file_base.'_thumb_180.'.$ext;
			$config['maintain_ratio'] = FALSE;
			$config['quality'] = 90;
			$config['height'] = 180;
			$config['width'] = 180;
			$config['x_axis'] = '35';
			// apply settings
			$this->CI->image_lib->initialize($config); 
			// crop
			$this->CI->image_lib->crop();
			// -------------------------------
			// new settings for thumb 350
			$config['source_image'] = $file_base.'.'.$ext;			
			$config['new_image'] = $file_base.'_thumb_350.'.$ext;
			$config['maintain_ratio'] = FALSE;
			$config['quality'] = 90;
			$config['height'] = 350;
			$config['width'] = 300;
			$config['x_axis'] = '60';
			// apply settings
			$this->CI->image_lib->initialize($config); 
			// crop
			$this->CI->image_lib->crop();
			// ------------------------------------------------
			// upload to DB
			$img['type'] = 'image';
			// data
			$img['data']['dir'] = $up_dir;
			$img['data']['filename'] = $filename.'.'.$ext;
			$img['data']['filename_no_ext'] = $filename;
			$img['data']['ext'] = $ext;
			$img['data']['label'] = $result['label'];
			$img['data']['alt'] = $filename;
			$img['data']['file_path'] = $result['path'];
			$img['data']['thumb_150'] = $filename.'_thumb_150.'.$ext;								
			$img['data']['thumb_180'] = $filename.'_thumb_180.'.$ext;	
			$img['data']['thumb_350'] = $filename.'_thumb_350.'.$ext;
			$img['data']['full_path'] = $up_dir.$result['path'];
			$img['data']['thumb_150_path'] = $up_dir.$filename.'_thumb_150.'.$ext;
			$img['data']['cms_full_path'] = $uploadDirectory.$result['path'];
			$img['data']['cms_thumb_150'] = $uploadDirectory.$filename.'_thumb_150.'.$ext;
			// prepare data
			$img['data'] = json_encode($img['data']);
			// insert into DB
			$this->CI->db->insert('client_files', $img);
			// if db entry successful
			if($this->CI->db->insert_id() != null) 
			{
				// return ID & success = true
				$result['thumb_150'] = $filename.'_thumb_150.'.$ext;
				$result['id'] = $this->CI->db->insert_id();
				$result['success'] = true;
			}
			// if db error
			else
			{
				// return error
				$result['error'] = 'Could not save uploaded file. The upload was cancelled, or server error encountered';             
			}
		}
		else
		{
			$result['error'] = 'Could not save uploaded file. The upload was cancelled, or server error encountered';
		}
		// return results
		return $result;
	}    
}
// END qquploader
// ------------------------------------------