<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Tumblr OAuth1 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

class FS_OAuth_Tumblr extends CI_Driver{
	
	
	public function url_request_token()
	{
		return 'http://www.tumblr.com/oauth/request_token';
	}
	
	// public function url_authenticate_token()
	// {
	// 	return 'https://api.twitter.com/oauth/authenticate?oauth_token=';
	// }
	
	public function url_authorize()
	{
		return 'http://www.tumblr.com/oauth/authorize';
	}

	public function url_access_token()
	{
		return 'http://www.tumblr.com/oauth/access_token';
	}

	public function url_user_data()
	{
		return 'http://api.tumblr.com/v2/user/info';
	}

	public function revoke()
	{
		// $CI = &get_instance();
		// // delete stored data
		// $this->delete_stored('twitter');
		// // redirect
		// redirect(site_url(str_replace('/revoke','',$CI->uri->uri_string())));
	}

	public function get_user_info($token)
	{
		$CI = &get_instance();
		// load curl library
		$CI->load->library('fs_curl');
		// build url
		// fetch data
		$user = json_decode($CI->fs_curl->simple_get($token['url']), TRUE);
		$user = $user['response']['user'];
		// Create a response from the request
		$user = array(
			'uid' 				=> $user['name'],
			'likes' 			=> $user['likes'],
			'following'		=> $user['following'],
			'post_format'	=> $user['default_post_format'],
			'blogs' 			=> $user['blogs'],
			'image' 			=> 'http://api.tumblr.com/v2/blog/'.$user['name'].'.tumblr.com/avatar/512'
		);
				echo'<pre>';print_r($user);echo'</pre>';
		// return user data
		return $user;
	}
}