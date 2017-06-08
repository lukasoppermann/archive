<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($method)
	{
		if($method == 'page')
		{
			$parts = explode('/',current_path());
			$this->page(variable($parts[2]));
		}
		elseif($method == 'user')
		{
			$this->user();			
		}
		elseif($method == 'personal')
		{
			$this->personal();			
		}
		else
		{
			$this->personal();				
		}	
	}
	
	public function page($part)
	{
		$this->data['title'] = "Page Settings";
		//
		$this->data['page_name'] = config('page_name');
		$this->data['analytics'] = config('analytics');
		// ---------------------------------
		// Twitter Connect
		if($part == 'twitter')
		{
			$this->twitter();
		}
		elseif($part == 'disconnect-twitter')
		{
			$this->db->where('key','auth');
			
			foreach(array('twitter_token', 'twitter_secret') as $key)
			{
				$this->db->where('type',$key);
				$this->db->delete('client_data');
			}
		}

		// set tokens if they exist
		if(isset($this->cauth['twitter']) && $this->cauth['twitter']['oauth_token_secret'] != null && $this->cauth['twitter']['oauth_token'] != null)
		{
			$this->tweet->set_tokens($this->cauth['twitter']);
		}
		else
		{
			$this->tweet->set_tokens(array('oauth_token_secret' => '', 'oauth_token' => ''));
		}
		// try to login
		if ( !$this->tweet->logged_in() )
		{
			$this->data['twitter'] = '';
			$this->data['twitter_url'] = '/settings/page/twitter';
		}
		else
		{
			$this->data['twitter'] = ' active';	
			$this->data['twitter_url'] = '/settings/page/disconnect-twitter';		
		}
		// ---------------------------------
		// Facebook Connect	
		$this->load->library('facebook', array(
			'appId' => $this->config->item('facebook_api_key'), 
			'secret' => $this->config->item('facebook_secret_key'), 
			'cookie' => $this->config->item('facebook_cookie') ) );
		// Get User ID
		$user = $this->facebook->getUser();
		
		if( isset($user) && $user != 0) 
		{
			$this->facebook->setAccessToken(variable($this->cauth['fb_token']));
			$user = $this->facebook->getUser();
			if(isset($user) && $user != 0)
			{
				try {
					// Proceed knowing you have a logged in user who's authenticated.
					$user_profile = $this->facebook->api('/me');
				} 
				catch (FacebookApiException $e) 
				{
					$this->db->where('type','fb_token')->where('key','auth')->delete('client_data');
					$user = null;
				}
			}
		}
		
		if( isset($user_profile) )
		{
			$url = $this->facebook->getLogoutUrl(array('redirect_uri' => current_url()));
			$this->data['fb_button'] = "<div id='fb_logged_in'>Logged into Facebook as ".htmlspecialchars($user_profile['name'])." <a href=\"".$url."\">(Logout)</a></div>";
			//
			$fb = array('fb_token' => $this->facebook->getAccessToken());
			foreach($fb as $key => $val)
			{			
				$query = $this->db->from('client_data')->where('type',$key)->where('key','auth')->get();
				$res = $query->row_array();
				// Update
				if( count($res) > 0 ) 
				{
					$this->db->where('key','auth');	
					$this->db->where('type',$key);
					$this->db->update('client_data', array('value' => $val));
				}
				// or insert
				else
				{
					$this->db->insert('client_data', array('key' => 'auth', 'type' => $key, 'value' => $val));
				}
			}
		}
		else 
		{
			$this->data['fb_button'] = "<a href=\"".$this->facebook->getLoginUrl(
			                            array(
			                                'scope' => 'manage_pages,email,user_birthday,publish_stream', // app permissions
			                                'redirect_uri' => current_url() // URL where you want to redirect your users after a successful login
			                            ))."\">Connect with Facebook</a>";
		}
		// ---------------------------------
		view('form/settings_page', $this->data);
	}
	
	public function user()
	{
		view('form/settings_user', $this->data);
	}
	
	public function personal()
	{
		view('form/settings_personal', $this->data);
	}
	
	public function twitter()
	{
		//
		if ( !$this->tweet->logged_in() )
		{
			$this->tweet->set_callback(site_url('/settings/page/twitter'));
			$this->tweet->login();
		}
		else
		{
			$tokens = $this->tweet->get_tokens();
			$twitter = array('twitter_token' => $tokens['oauth_token'], 'twitter_secret' => $tokens['oauth_token_secret']);
			foreach($twitter as $key => $val)
			{
				$this->db->from('client_data')->where('type',$key)->where('key','auth');
			
				// Update DB
				$this->db->where('key','auth');	
				$this->db->where('type',$key);
			
				if ($this->db->count_all_results() == 0) 
				{
					$this->db->insert('client_data', array('key' => 'auth', 'type' => $key, 'value' => $val));
				}
				else
				{
					$this->db->where('key','auth');	
					$this->db->where('type',$key);
					$this->db->update('client_data', array('value' => $val));
				}
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */