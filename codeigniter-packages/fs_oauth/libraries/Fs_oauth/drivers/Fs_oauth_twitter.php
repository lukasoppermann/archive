<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Twitter OAuth2 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

class Fs_oauth_twitter extends CI_Driver{
	
	
	public function url_request_token()
	{
		return 'https://api.twitter.com/oauth/request_token';
	}
	
	public function url_authenticate_token()
	{
		return 'https://api.twitter.com/oauth/authenticate?oauth_token=';
	}
	
	public function url_authorize()
	{
		return 'http://api.twitter.com/oauth/authorize';
	}

	public function url_access_token()
	{
		return 'http://api.twitter.com/oauth/access_token';
	}

	public function url_user_data()
	{
		return 'https://api.twitter.com/1/account/verify_credentials.json';
	}

	public function revoke()
	{
		$CI = &get_instance();
		// delete stored data
		$this->delete_stored('twitter');
		// redirect
		redirect(site_url(str_replace('/revoke','',$CI->uri->uri_string())));
	}

	public function get_user_info($token)
	{
		$CI = &get_instance();
		// load curl library
		$CI->load->library('fs_curl');
		// build url
		// fetch data
		$user = json_decode($CI->fs_curl->simple_get($token['url']), TRUE);
		if( is_array($user) )
		{
			// Create a response from the request
			$user = array_merge(array(
				'uid' 			=> $user['id'],
				'nickname' 	=> $user['screen_name'],
				'image' 		=> $user['profile_image_url'],
				'urls' 			=> array(
					'twitter' => 'http://twitter.com/'.$user['screen_name']
				)), $user
			);
		}
		// return user data
		return $user;
	}
	// post to twitter timeline
	public function post( $data = null )
	{
		if( isset($data['message']) )
		{
			$CI = &get_instance();
			// load curl library
			$CI->load->library('fs_curl');
			// init fs_oauth with right service
			$this->init('twitter');
			// define url
			$path = "https://api.twitter.com/1.1/statuses/update.json";
			// define standard params
			$parameters = array('oauth_consumer_key' => $this->client['id'],
			'oauth_nonce' 						=> md5(uniqid(rand(), TRUE)),
			'oauth_timestamp' 				=> time(),
			'oauth_signature_method' 	=> $this->client['signature'],
			'oauth_version'						=> '1.0');
			// get oauth token
			$access = $this->get_token('twitter');
			$parameters['oauth_token'] = $access['access_token'];
			// status message
			isset($data['message']) ? $parameters['status'] = substr($data['message'], 0, 120) : '';
			// link
			isset($data['link']) ? $parameters['status'] 	= $parameters['status'].' '.$data['link'] : '';
			// sign
			$signed = $this->sign(array('method' => 'POST', 'path' => $path, 'parameters' => $parameters));
			$status = $parameters['status'];
			unset($parameters['status']);
			// link picture
			// $img_length = 21;
			// if( isset($data['image']) )
			// {
			// 	$path = "https://api.twitter.com/1.1/statuses/update_with_media.json";
			// 	$signed = $this->sign(array('method' => 'POST', 'path' => $path, 'parameters' => $parameters));
			// 	$parameters['status'] = substr($data['message'],0, 99);
			// 	$parameters['media[]'] = "@{$data['image']}";
			// }
			// else
			// {
			// 	isset($data['message']) ? $parameters['status'] = substr($data['message'], 0, 120) : '';
			// 	$signed = $this->sign(array('method' => 'POST', 'path' => $path, 'parameters' => $parameters));
			// }
			// add signature to params
			$parameters['oauth_signature'] = $signed['signature'];
			// run cUrl
			$CI->fs_curl->create($path);
			$CI->fs_curl->http_header($this->client['post_header'], $this->get_header_string($parameters));
			$CI->fs_curl->post(array('status' => $status));
			$CI->fs_curl->execute();
		}
	}
}