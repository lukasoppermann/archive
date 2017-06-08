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
	'id' 		=> 'a5ebec8a2196e6dae721', 
	'secret' => 'a5faa3ac1594ffa2f97ec86501265d7e7e2cd045'
);
// twitter
$config['client']['twitter'] = array(
	'id' 							=> 'fdcz2C0tMFV50St7JoLIRg', 
	'secret' 					=> '1LoUe6dJ4bQjJJmN5mhK6D6arAdM3R46NkofpFjA8s',
	'oauth_version' 	=>	'1',
	'uid_name'				=> 'user_id'
);
// twitter
$config['client']['tumblr'] = array(
	'id' 							=> 'tyTUbOqVyQIGqNXkmL8mzE03slsCItyo0GI6Jiaub2LD5ZzZVr', 
	'secret' 					=> 'TuMORkFIEnR0xvfvBYhCIun8dcGgVmFvrb7ZNlDsw8RZhvXqGz',
	'oauth_version' 	=>	'1',
	'method' 					=> 'POST'
);
// soundcloud
$config['client']['soundcloud'] = array(
	'id' 		=> 'ed9c38a1b6302a116bed78a612188e9f', 
	'secret' => '60a21f6e8f5b37698bece2e765703ff2',
	'method' => 'POST'
);
// mailchimp
$config['client']['mailchimp'] = array(
	'id' 		=> '313165264017', 
	'secret' => '6758c8e1376e84670addc5204821788b',
	'method' => 'POST'	
);
// facebook
$config['client']['facebook'] = array(
	'id' 				=> '435151796533349', 
	'secret' 		=> '177b27423db51f9e2bb6b2d67ac28dbc',
	'expire_name'	=> 'expires',
	'scope' 			=> array('email', 'read_stream', 'publish_stream', 'manage_pages'),
	'follow_name' => 'uid'
);
// paypal
$config['client']['paypal'] = array(
	'id' 		=> '86FP6GBFMHJGMBRP', 
	'secret' => 'AAC5kfzKNsA.RJR0p21pHNko1fUkATlpnqRxEGT3KHiu3oJD58odiv8r',
	'method' => 'POST',
	'scope'  => 'https://identity.x.com/xidentity/resources/profile/me',
	'callback' => 'http://localhost/worx/dashboard'
);