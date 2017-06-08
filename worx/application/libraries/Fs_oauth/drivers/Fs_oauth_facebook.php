<?php
/**
 * Facebook OAuth2 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

class FS_OAuth_Facebook extends CI_Driver
{
	
	public function url_authorize()
	{
		return 'https://www.facebook.com/dialog/oauth';
	}

	public function url_access_token()
	{
		return 'https://graph.facebook.com/oauth/access_token';
	}

	public function get_user_info($token)
	{
		$CI = &get_instance();
		// load curl library
		$CI->load->library('fs_curl');
		// try to access service
		try{
			// try to get data
			$user = json_decode($CI->fs_curl->simple_get('https://graph.facebook.com/me', array('access_token' => $token)));
			// if user exists
			if( isset($user) )
			{
				// get pages
				$_pages = json_decode($CI->fs_curl->simple_get('https://graph.facebook.com/me/accounts', array('access_token' => $token)), true);
				// loop through pages
				if( isset($_pages) )
				{
					foreach( $_pages['data'] as $page)
					{
						// check if page is not application
						if( $page['category'] != 'Application')
						{
							$pages[] = $page;
						}
					}
				}
			
				// Create a response from the request
				return array(
					'uid' => $user->id,
					'nickname' => isset($user->username) ? $user->username : null,
					'name' => $user->name,
					'first_name' => $user->first_name,
					'pages' => $pages,
					'last_name' => $user->last_name,
					'email' => isset($user->email) ? $user->email : null,
					'location' => isset($user->hometown->name) ? $user->hometown->name : null,
					'description' => isset($user->bio) ? $user->bio : null,
					'image' => 'https://graph.facebook.com/me/picture?type=normal&access_token='.$token,
					'urls' => array(
					  'facebook' => $user->link
					)
				);
			}
		}
		// catch exception
		catch(Exception $e)
		{
			// return false
			return FALSE;
		}
	}
	
	public function revoke()
	{
		$CI = &get_instance();
		// load curl library
		$CI->load->library('fs_curl');
		// revoke access
		$CI->fs_curl->simple_delete('https://graph.facebook.com/me/permissions', array('access_token' => $this->get_token('facebook')));
		// delete stored data
		$this->delete_stored('facebook');
		// redirect
		redirect(site_url(str_replace('/revoke','',$CI->uri->uri_string())));
	}
	
	public function post( $data = null )
	{
		if( isset($data['image']) || isset($data['message']) )
		{
			$CI = &get_instance();
			// load curl library
			$CI->load->library('fs_curl');
			// define parameters for post
			// status message
			isset($data['message']) ? $parameters['message'] = $data['message'] : '';
			// link
			isset($data['link']) ? $parameters['link'] = $data['link'] : '';
			// link-name 
			isset($data['name']) ? $parameters['name'] = $data['name'] : '';
			// link caption
			isset($data['caption']) ? $parameters['caption'] = $data['caption'] : '';
			// link picture
			isset($data['image']) ? $parameters['picture'] = $data['image'] : '';
			// access token needed to post
			$parameters['access_token'] = $this->get_token('facebook');
			// loop trough post ids and try to post
			foreach( $this->get('facebook', 'post') as $id)
			{
				//build and call our Graph API request
				try{
					$CI->fs_curl->simple_post('https://graph.facebook.com/'.$id.'/feed', $parameters);
				}
				catch( FacebookApiException $e)
				{
					log_message('error', 'Exception when trying to post to facebook: '.$e);
				}
				catch( Exception $e)
				{
					log_message('error', 'Exception when trying to post to facebook: '.$e);
				}
			}
		}
	}
	
	
}
