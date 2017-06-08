<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Mailchimp OAuth2 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

class FS_OAuth_Mailchimp extends CI_Driver
{
	/**
	 * @var  string  the method to use when requesting tokens
	 */
	public function url_authorize()
	{
		return 'https://login.mailchimp.com/oauth2/authorize';
	}

	public function url_access_token()
	{
		return 'https://login.mailchimp.com/oauth2/token';
	}

	public function get_user_info($token)
	{
		// Create a response from the request
		return array(
			'uid' => $token
		);
	}
}
