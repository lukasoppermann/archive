<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * CodeIgniter MY_Controller Libraries
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Controller
 * @author		Lukas Oppermann - veare.net
 */
class MY_Controller extends CI_Controller {

	var $data, $cauth, $facebook;
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
		// get config
		$this->config->config_from_db('client_data');
		// set charset
		Header("Content-type: text/html;charset=UTF-8");
		// set header for browser to not cache stuff
		Header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); 
		Header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); 
		Header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		Header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		Header("Pragma: no-cache" ); // HTTP/1.0
		// --------------------------------------------------------------------	
		// fetch auth data
		$this->db->from('client_data');
		$this->db->where('key','auth');	
		$query = $this->db->get();	
		$array = $query->result_array();
		// check for tokens
		foreach($array as $k => $key)
		{
			if($key['type'] == 'twitter_secret')
			{
				$this->cauth['twitter']['oauth_token_secret'] = $key['value'];
			}
			elseif($key['type'] == 'twitter_token')
			{
				$this->cauth['twitter']['oauth_token'] = $key['value'];				
			}
			else
			{
				$this->cauth[$key['type']] = $key['value'];
			}
		}
		// set tokens if they exist
		if(isset($this->cauth['twitter']) && $this->cauth['twitter']['oauth_token_secret'] != null && $this->cauth['twitter']['oauth_token'] != null)
		{
			$this->tweet->set_tokens($this->cauth['twitter']);
		}
		// ---------------------------------
		// Facebook Connect	
		$this->load->library('facebook', array(
			'appId' => $this->config->item('facebook_api_key'), 
			'secret' => $this->config->item('facebook_secret_key'), 
			'cookie' => $this->config->item('facebook_cookie') ) );
		// Get User ID
		$user = $this->facebook->getUser();
		// try to log in
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
		// --------------------------------------------------------------------	
		// get group
		$this->cms->get_groups();
		// get user data
		$this->data['user']['data'] = user('user_data');
		// current page as var
		$_cur = explode('/',trim($this->cms->current(),'/'));
		$this->data['current'] = $_cur[0];
		// check login
        login(user('group'));
	}
// close class
}