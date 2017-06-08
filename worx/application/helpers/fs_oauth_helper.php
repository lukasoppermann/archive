<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter FS_oAuth Helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link			http://doc.formandsystem.com/helpers/fs_oauth
 */

// ------------------------------------------------------------------------
/**
 * fs_oauth_connect - connect to service using request or access key
 *
 * @param string
 * @return array
 */
function fs_oauth_connect( $service )
{
	$CI = &get_instance();
	// loading oAuth driver
	$CI->load->driver('Fs_oauth');
	// initializing given driver
	$CI->fs_oauth->init($service);
	// check if access_code is stored
	if ( $user = $CI->fs_oauth->get_user_info() )
	{
		// return user data
		return $user;
	}
	// if code is not returned by url
	elseif ( ! $CI->input->get('code') && ! $CI->input->get('oauth_verifier') )
	{
		// aquire code through authentication
		$CI->fs_oauth->authorize();
	}
	// code is given
	else
	{
		$CI->input->get('code') ? $code = $CI->input->get('code') : $code = $CI->input->get('oauth_verifier');
		// trying to get token with code
		try{
			// try go get code
			$token = $CI->fs_oauth->access($code);
			// get more data and update db
			$data = fs_oauth_userdata( $service );
			$user_data['follow'] = $data[$CI->fs_oauth->client['follow_name']];
			// update token in database
			db_update_insert(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => $CI->fs_oauth->current_driver), 
			array('data' => $user_data), array('merge' => TRUE, 'json' => array('data')) );
			// redirect so that code does not stay in url
			redirect(site_url($CI->uri->uri_string()));
		}
		// if fails, get exception
		catch (Exception $e)
		{
			// display error
			show_error('That didnt work: '.$e);
		}
	}
}
// ------------------------------------------------------------------------
/**
 * fs_oauth_revoke - revoke to service
 *
 * @param string
 * @param array
 * @return array
 */
function fs_oauth_revoke( $service = null )
{
	if($service != null && $service != '' )
	{
		$CI = &get_instance();
		// loading oAuth driver
		$CI->load->driver('Fs_oauth');
		// initializing given driver
		$CI->fs_oauth->init($service);
		// revoke access
		$CI->fs_oauth->{$service}->revoke();
	}
	// if not
	return FALSE;	
}
// ------------------------------------------------------------------------
/**
 * fs_oauth_userdata - get userdata from service if key exists
 *
 * @param string
 * @param array
 * @return array
 */
function fs_oauth_userdata( $service = null )
{
	$CI = &get_instance();
	// loading oAuth driver
	$CI->load->driver('Fs_oauth');
	// initializing given driver
	$CI->fs_oauth->init($service);
	// check if access_code is stored
	if ( $user = $CI->fs_oauth->get_user_info() )
	{
		// return user data
		return $user;
	}
	// else return FALSE
	else
	{
		return FALSE;
	}
}
// ------------------------------------------------------------------------
/**
 * fs_oauth_post - post to service
 *
 * @param string
 * @param array
 * @return array
 */
function fs_oauth_post( $service = null, $data = null)
{
	if($service != null )
	{
		$CI = &get_instance();
		// loading oAuth driver
		$CI->load->driver('Fs_oauth');
		// initializing given driver
		$CI->fs_oauth->init($service);
		// post
		if( $data != null && method_exists($CI->fs_oauth->{$service}, 'post') === TRUE )
		{
			return $CI->fs_oauth->{$service}->post($data);
		}
	}
	// if not
	return FALSE;
}