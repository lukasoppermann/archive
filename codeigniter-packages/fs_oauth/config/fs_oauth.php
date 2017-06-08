<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Configuration for fs_oAuth driver
|--------------------------------------------------------------------------
|
| drivers
*/
$config['drivers'] = array(
	'Fs_oauth_twitter', 
	'Fs_oauth_facebook', 
	'Fs_oauth_github', 
	'Fs_oauth_google', 
	'Fs_oauth_soundcloud', 
	'Fs_oauth_mailchimp', 
	'Fs_oauth_paypal',
	'Fs_oauth_tumblr',	
);
/*
|--------------------------------------------------------------------------
| oAuth ids & Secrets
|--------------------------------------------------------------------------
|
| The secret data from the application to use the oAuth tool with a specific service
|
*/
// github
$config['client']['github'] = array(
	'id' 		=> '', 
	'secret' => ''
);
// twitter
$config['client']['twitter'] = array(
	'id' 							=> '', 
	'secret' 					=> '',
	'oauth_version' 	=>	'1',
	'uid_name'				=> 'user_id'
);
// twitter
$config['client']['tumblr'] = array(
	'id' 							=> '', 
	'secret' 					=> '',
	'oauth_version' 	=>	'1',
	'method' 					=> 'POST'
);
// soundcloud
$config['client']['soundcloud'] = array(
	'id' 		=> '', 
	'secret' => '',
	'method' => 'POST'
);
// mailchimp
$config['client']['mailchimp'] = array(
	'id' 		=> '', 
	'secret' => '',
	'method' => 'POST'	
);
// facebook
$config['client']['facebook'] = array(
	'id' 				=> '', 
	'secret' 		=> '',
	'expire_name'	=> 'expires',
	'scope' 			=> array('email', 'read_stream', 'publish_stream', 'manage_pages'),
	'follow_name' => 'uid'
);
// paypal
$config['client']['paypal'] = array(
	'id' 		=> '', 
	'secret' => '',
	'method' => 'POST',
	'scope'  => 'https://identity.x.com/xidentity/resources/profile/me',
	'callback' => 'http://localhost/worx/dashboard'
);