<?php
 /**
  * FS_OAuth (2.0)
  *
  * @package		CodeIgniter
  * @subpackage	Libraries
  * @category		oAuth
  * @author			Lukas Oppermann - veare.net
  * @link			http://doc.formandsystem.com/libraries/oauth
  */
class Fs_oauth extends CI_Driver_Library {
	
	// CI instance
	public $CI;
	// valid drivers
	protected $valid_drivers;
	// current driver
	public $current_driver;
	// client information
	public $client;
	// client information
	public $client_data;
	// construct function
	function __construct()
	{
		// get CI instance
		$this->CI =&get_instance();
		// load config file
		$config = $this->CI->config->load('fs_oauth');
		// load helper
		$this->CI->load->helper('fs_oauth');
		// add fs_curl package
		$this->CI->add_package_path('fs_curl');
		// get valid drivers
		$this->valid_drivers = $this->CI->config->item('drivers');
		// get client id and secrets
		$this->client_data = $this->CI->config->item('client');
		// set log message
		log_message('debug', 'FS_OAuth Driver constructed');
	}
	// --------------------------------------------------------------------
	/**
	 * init
	 *
	 * initialize driver
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	function init($driver = null, $options = null)
	{
		// check if driver is valid
		if( in_array('Fs_oauth_'.$driver, $this->valid_drivers) )
		{
			// set current driver
			$this->current_driver 	= $driver;
			// set current client data and merge with default
			$this->client = array_merge( 
				array(
					'scope' 									=> '',
					'scope_seperator' 				=> ',',
					'method'									=> 'GET',
					'post_header' 						=> 'Authorization',
					'callback_name'						=> 'redirect_uri',
					'redirect_uri' 						=> isset($this->client_data[$driver]['callback']) ? $this->client_data[$driver]['callback'] : site_url($this->CI->uri->uri_string()),
					'response_type' 					=> 'code',
					'approval_prompt' 				=> 'force',// - google force-recheck
					'grant_type' 							=> 'authorization_code',
					'uid_name'								=> 'uid',
					'expire_name'							=> 'expires_in',
					'oauth_version' 					=> '2',
					'signature' 							=> 'HMAC-SHA1',
					'follow_name'							=> 'nickname'
				)
				,$this->client_data[$driver]);
			// get service data from db
			$token = $this->get_token($this->current_driver);
			( isset($token['access_secret']) ? $this->client['oauth_token_secret'] = $token['access_secret'] : '');
			( isset($token['access_token']) ? $this->client['oauth_token'] = $token['access_token']: '');
			// load assets for oAuth 1
			if( $this->client['oauth_version'] != '2' )
			{
				$this->CI->load->helper('cookie');
				$this->CI->load->library('fs_curl');
			}
			// set log message
			log_message('debug', 'FS_OAuth Driver initialized for '.$driver);
		}
		else
		{
			$this->exception('Required option not provided: valid driver fs_oauth_'.$driver.' is invalid');
		}
		// check if key is given
		if (empty($this->client['id']))
		{
			$this->exception('Required option not provided: id');
		}
	}
	// --------------------------------------------------------------------
	/**
	 * authorize
	 *
	 * get an authorization code from provider
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	public function authorize($options = array())
	{
		// create state
		$options['state'] = md5(uniqid(rand(), TRUE));
		// if oAuth 2 is used
		if( $this->client['oauth_version'] == '2' )
		{
			// set state session
			$this->CI->session->set_userdata('state', $options['state']);
			// set params
			$params = array(
				'client_id' 										=> $this->client['id'],
				$this->client['callback_name'] 	=> isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->client['redirect_uri'],
				'state' 												=> $options['state'],
				'scope'													=> is_array($this->client['scope']) ? implode($this->client['scope_seperator'], $this->client['scope']) : $this->client['scope'],
				'response_type' 								=> $this->client['response_type'],
				'approval_prompt' 							=> $this->client['approval_prompt']
			);
			// create url and send user of to get authorized
			redirect($this->{$this->current_driver}->url_authorize().'?'.http_build_query($params));
		}
		// ------------------------------
		// if oAuth 1 is used
		else
		{
			// set parameters
			$this->params = array(
				'oauth_consumer_key'						=> $this->client['id'],
				'oauth_nonce' 									=> $options['state'],
				'oauth_timestamp' 							=> time(),
				'oauth_signature_method' 				=> $this->client['signature'],
				'oauth_version'									=> '1.0'
			);
			// set signature
			$signed = $this->sign( array('parameters' => $this->params, 'path' => $this->{$this->current_driver}->url_request_token()) );
			$this->params['oauth_signature'] = $signed['signature'];
			// cURL
			if( $this->client['method'] == 'POST' )
			{
				$this->CI->fs_curl->create($signed['url']);
				$this->CI->fs_curl->http_header($this->client['post_header'], $this->get_header_string($this->params));
				$this->CI->fs_curl->post($this->params);
				$response = $this->CI->fs_curl->execute();
			}
			else
			{
				$response = $this->CI->fs_curl->simple_get($signed['url']);
			}
			// parse response
			parse_str($response, $returned_items);
			$request_token 	= $returned_items['oauth_token'];
			$request_secret = $returned_items['oauth_token_secret'];
			/*!!!!!!!!!!!*/
			// INSERT ERROR HANDLING HERE
			/*!!!!!!!!!!!*/
			// set cookie
			set_cookie(array(
				'name' 		=> 'oauth_token_secret',
				'value' 	=> $returned_items['oauth_token_secret'],
				'expire' 	=> time()+3600
			));
			// Step 2: Authorize the Request Token
			// update request token
			$this->params['oauth_token'] = $returned_items['oauth_token'];
			// update signature
			// set signature
			$signed = $this->sign( array('parameters' => $this->params, 'path' => $this->{$this->current_driver}->url_authorize()) );
			// redirect to signed url for login
			redirect($signed['url']);
			exit;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * get_user_info
	 *
	 * get user information using drivers fn
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	public function get_user_info( $token = null )
	{
		// if token is set
		if( $token != null )
		{
			// get user data using token
			return $this->{$this->current_driver}->get_user_info($token);
		}
		// if token is empty, try to get data from db
		elseif( $token = $this->get_token($this->current_driver) )
		{
			// if token is given, return user data
			if( isset($token) )
			{
				// if using oAuth v2
				if( $this->client['oauth_version'] == '2' )
				{
					// set access token
					$this->client['access_token'] = $this->client_data[$this->current_driver]['access_token'] = $token;
				}
				// if using oAuth v1
				else
				{
					$this->client['access_token'] = $this->client_data[$this->current_driver]['access_token'] = $token['access_token'];
					$this->client['oauth_token_secret'] = $this->client_data[$this->current_driver]['access_secret'] = $token['access_secret'];
					
					$this->parameters = array(
						'oauth_token' 						=> $token['access_token'],
						'oauth_consumer_key'			=> $this->client['id'],
						'oauth_nonce' 						=> md5(uniqid(rand(), TRUE)),
						'oauth_timestamp' 				=> time(),
						'oauth_signature_method' 	=> $this->client['signature'],
						'oauth_version'						=> '1.0'
					);
					//
					$signed	= $this->sign( array('parameters' => $this->parameters, 'method' => 'GET', 'path' => $this->{$this->current_driver}->url_user_data()));
					$token['url'] = $signed['url'];
				}
				// try to get user data
				$user = $this->{$this->current_driver}->get_user_info($token);
				// check if result is working
				if( is_array($user) && isset($user[key($user)]) )
				{
					return $user;
				}
			}
		}
		// if everything fails return false
		return FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * post
	 *
	 * post data to a service
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	 public function post()
	 {
	 	
	 }
	// --------------------------------------------------------------------
	/**
	 * access
	 *
	 * get an authorization code from provider and return token
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	public function access($code, $options = array())
	{
		// oauth 2
		if( $this->client['oauth_version'] == '2' )
		{
			// assign standard params
			$params = array(
				'client_id' 		=> $this->client['id'],
				'client_secret' => $this->client['secret'],
				'grant_type' 		=> isset($options['grant_type']) ? $options['grant_type'] : $this->client['grant_type'],
			);
			// assign params depending on grant type
			switch( $params['grant_type'] )
			{
				// if grant_type = authorization_code
				case 'authorization_code':
					$params['code'] = $code;
					$params['redirect_uri'] = isset($options['redirect_uri']) ? $options['redirect_uri'] : $this->client['redirect_uri'];
				break;
				// if grant_type = refresh_token
				case 'refresh_token':
					$params['refresh_token'] = $code;
				break;
			}
			// get access token url from driver
			$url = $this->{$this->current_driver}->url_access_token();
			// check method
			switch ($this->client['method'])
			{
				// if method = GET
				case 'GET':
					// append params to url
					$url .= '?'.http_build_query($params);
					// get response
					$response = file_get_contents($url);
					// parse response
					parse_str($response, $return);
				break;
				// of method = POST
				case 'POST':
					// create conteyt
					$opts = array(
						'http' => array(
							'method'  => 'POST',
							'header'  => 'Content-type: application/x-www-form-urlencoded',
							'content' => http_build_query($params),
						)
					);
					// get response
					$_default_opts = stream_context_get_params(stream_context_get_default());
					$context = stream_context_create(array_merge_recursive($_default_opts['options'], $opts));
					$response = file_get_contents($url, false, $context);
					// parese response
					$return = json_decode($response, true);
				break;
				// if no method is assign, throw error
				default:
					throw new OutOfBoundsException("Method '{$this->client['method']}' must be either GET or POST");
			}
			// check for errors
			$this->handle_exceptions($return);
			// get token
			return $this->token($params['grant_type'], $return);
		}
		// ------------------------------
		// oAuth version 1
		else
		{
			// set params
			$this->params = array(
				'oauth_consumer_key'			=> $this->client['id'],
				'oauth_nonce' 						=> md5(uniqid(rand(), TRUE)),
				'oauth_timestamp' 				=> time(),
				'oauth_signature_method' 	=> $this->client['signature'],
				'oauth_version'						=> '1.0'
			);
			$this->params['oauth_secret'] 			= $this->client['oauth_token_secret'] = $this->CI->input->cookie('oauth_token_secret');
			$this->params['oauth_token']				= $this->CI->input->get('oauth_token');
			$this->params['oauth_verifier']			= $this->CI->input->get('oauth_verifier');
			// sign url
			$signed	= $this->sign( array('parameters' => $this->params, 'path' => $this->{$this->current_driver}->url_access_token()));
			$this->params['oauth_signature'] = $signed['signature'];
			// check method for cURL
			if( $this->client['method'] == 'POST' )
			{
				$this->CI->fs_curl->create($signed['url']);
				$this->CI->fs_curl->http_header($this->client['post_header'], $this->get_header_string($this->params));
				$this->CI->fs_curl->post($this->params);
				$response = $this->CI->fs_curl->execute();
			}
			else
			{
				$response = $this->CI->fs_curl->simple_get($signed['url']);
			}
			// return data
			return $this->token_v1($response);
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Exception handling
	 *
	 * handling exceptions
	 *
	 * @access	public
	 * @param	array
	 */
	 function handle_exceptions( $result )
	 {
		 // check if error exists
		 if ( ! empty($result['error']))
		 {
			 // get error code
			$code = isset($result['code']) ? $result['code'] : 0;
			// OAuth 2.0 Draft 10 style 
			if (isset($result['error']))
			{
				$message = $result['error'];
			}
			// cURL style
			elseif (isset($result['message']))
			{
				$message = $result['message'];
			}
			// neither
			else
			{
				$message = 'Unknown Error.';
			}
			// throw exception
			$this->exception( $message, $code );
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Exception
	 *
	 * throw exceptions
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	function exception( $message, $code = 0 )
	{
		// log error message
		log_message('error', $message.' - Code: '.$code);
		// if developement throw error
		if( ENVIRONMENT == 'development' )
		{
			// show error
			show_error($message);
			// throw error
			throw new Exception($message, $code);
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Token
	 *
	 * fetch token
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	function token( $grant_type, $result )
	{
		// check grant type
		switch($grant_type)
		{
			// if type is authorization_code
			case 'authorization_code':
				// check if access token is set
				if ( ! isset($result['access_token']))
				{
					throw new Exception('Required option not passed: access_token'.PHP_EOL.print_r($result, true));
				}
				// Some providers (not many) give the uid here, so lets take it
				isset($result[$this->client['uid_name']]) and $this->client['uid'] = $result[$this->client['uid_name']];
				// We need to know when the token expires, add num. seconds to current time
				isset($result[$this->client['expire_name']]) and $this->client['expires'] = time() + ((int) $result[$this->client['expire_name']]);
				// Grab a refresh token so we can update access tokens when they expires
				isset($result['refresh_token']) and $this->client['refresh_token'] = $result['refresh_token'];
				// get access token
				$this->client['access_token'] = $result['access_token'];
				// save access token
				$this->save_token();
				// return access token
				return $this->client['access_token'];
			break;
			// if type is refresh_token
			case 'refresh_token':
				// refresh token flow
			break;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Token_v1
	 *
	 * fetch token for version 1
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	function token_v1( $results )
	{
		// parse result
		parse_str($results, $result);
		// add to client
		$this->params['oauth_token'] 				= $this->client['access_token'] 	= $result['oauth_token'];
		$this->client['access_secret'] 			= $result['oauth_token_secret'];
		// Some providers (not many) give the uid here, so lets take it
		isset($result[$this->client['uid_name']]) and $this->client['uid'] = $result[$this->client['uid_name']];
		//
		$signed = $this->sign( array('parameters' => $this->params, 'path' => $this->{$this->current_driver}->url_user_data()));
		// save token
		$this->save_token( );
		// return token
		return array(
			'access_token' 		=> $result['oauth_token'], 
			'access_secret' 	=> $result['oauth_token_secret'],
			'url' 						=> $signed['url']
		);
	}
	// --------------------------------------------------------------------
	/**
	 * save tokens
	 *
	 * save token to db
	 *
	 * @access	public
	 * @param	array
	 */
	 function save_token( )
	 {
		 // create update data
		 $update['access_token'] 																								= $this->client['access_token'];
		 isset($this->client['refresh_token']) and $update['refresh_token'] 		= $this->client['refresh_token'];
		 isset($this->client['access_secret']) and $update['access_secret'] 		= $this->client['access_secret'];
		 isset($this->client['uid']) and $update['uid'] 												= $this->client['uid'];
		 isset($this->client['expires']) and $update['expires'] 								= $this->client['expires'];
		 // update token in database
		 db_update_insert(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => $this->current_driver), 
		 array('data' => $update), array('merge' => TRUE, 'json' => array('data')) );
	 }
 	// --------------------------------------------------------------------
 	/**
 	 * get token
 	 *
 	 * get token from db
 	 *
 	 * @access	public
 	 * @param	string
 	 * @param	array
 	 */
	function get_token( $service = null )
	{
		if( !isset($this->client_data[$service]['oauth_version']) || $this->client_data[$service]['oauth_version'] != '1' )
		{
			return $this->get($service, 'access_token');
		}
		else
		{
			return $this->get($service, array('access_token', 'access_secret'));
		}
	}
 	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * get oauth account data from db
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	function get( $service = null, $key = 'access_token' )
	{
		if( $service != null )
		{
			$key_missing = 'false';
			// check if key is missing
			foreach( (array) $key as $k )
			{
				if( !isset($this->client_data[$service][$k]) )
				{
					$key_missing = 'true';
				}
			}
			// if key is missing get from db
			if( $key_missing == 'true' )
			{
				// get data from db
				$db_data = db_select(config('system/current/db_prefix').config('system/current/db_data'), array('key' => 'settings', 'type' => $service), array('json' => array('data'), 'single' => true));
				// if db data is given
				if( $db_data != FALSE )
				{
						$this->client_data[$service] = array_merge($db_data, $this->client_data[$service]);
				}
				// data should be present
				$key_missing = 'false';
				// check if key is present now
				foreach( (array) $key as $k )
				{
					if( !isset($this->client_data[$service][$k]) )
					{
						$key_missing = 'true';
					}
				}
			}
			// check if entry exist
			if( $key_missing == 'false' )
			{
				// return values
				if( is_array($key) )
				{
					return $this->client_data[$service];
				}
				else
				{
					return $this->client_data[$service][$key];
				}
			}
		}
		// return false
		return FALSE;
	}
	// --------------------------------------------------------------------
	/**
	 * delete token
	 *
	 * delete token from db
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	 function delete_stored( $service = null )
	 {
		 if( $service != null && $service != '' )
		 {
			// get token from db
			db_delete(config('system/current/db_prefix').config('system/current/db_data'), array('key' => 'settings', 'type' => $service));
			// return token
			return TRUE;
		 }
		 // return false
		 return FALSE;
	 }
 	// --------------------------------------------------------------------
	// OAuth 1 Stuff
	// --------------------------------------------------------------------
	/**
	 * _oauth_escape
	 *
	 * private oAuth 1 escape method
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	private static function _oauth_escape( $string ) 
	{
		// if $string is not a string
		if (is_array($string)){ $this->exception('Array passed to _oauthEscape'); }
		// if string is exactly 0
		if ($string === 0) { return 0; }
		// if string is string(0)
		if ($string == '0') { return '0'; }
		// if string is false
		if ( strlen($string) == 0 ) { return ''; }
		// raw url encode string
		$string = rawurlencode($string);
		//fix rawurlencode of ~ 
		$string = str_replace('%7E','~', $string);
		// encode other chars
		$string = str_replace('+','%20',$string);
		$string = str_replace('!','%21',$string);
		$string = str_replace('*','%2A',$string);
		$string = str_replace('\'','%27',$string);
		$string = str_replace('(','%28',$string);
		$string = str_replace(')','%29',$string);
		// return string
		return $string;
	}
	/**
	 * _normalized_parameters
	 *
	 * private oAuth 1 param normalization method
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	private function _normalized_parameters( $params = null )
	{
		// set array for ksort in case no params are given
		$normalized_keys = array();
		$return_array = array();
		// looph through parameters
		foreach( $params as $name => $value ) 
		{
			// escape values
			$normalized_keys[$this->_oauth_escape($name)] = $this->_oauth_escape($value);
		}
		// sort values by key alphabetially
		ksort($normalized_keys);
		// build array because http_build_query screws up
		foreach($normalized_keys as $key => $val) 
		{
			if (is_array($val)) 
			{
				sort($val);
				foreach($val as $element) 
				{
					array_push($return_array, $key . "=" . $element);
				}
			}
			else 
			{
				array_push($return_array, $key .'='. $val);
			}
		}
		// return joined array
		return join("&", $return_array);
	}
	/**
	 * sign
	 *
	 * oAuth 1 sign fn
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	function sign( $options = array() )
	{
		// merge options
		$options = array_merge(array(
			'parameters' => array(),
			'method' 		 => $this->client['method']
		),$options);
		// create signature
		$options['parameters']['oauth_signature'] = $this->_generate_signature( array('path' => $options['path'], 'method' => $options['method'], 'parameters' => $options['parameters']) );
		// values
		$output = array(
			'parameters' 	=> $options['parameters'],
			'signature'	 	=> $options['parameters']['oauth_signature'],
			'url' 				=> $options['path'].'?'.$this->_normalized_parameters($options['parameters']),
			'header' 			=> $this->get_header_string(),
			'sbs' 				=> $this->sbs
		);
		return $output;
	}
	/**
	 * _generate_signature
	 *
	 * oAuth 1 _generate_signature fn
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	private function _generate_signature( $options = array() ) 
	{
		// mermge options
		$options = array_merge(
			array(
				'path' 			=> '',
				'parameters' 		=> array(),
			)
			,$options);
		// prepare secret key
		$secret_key = '';
		if( isset($this->client['secret']) )
		{
			$secret_key .= $this->_oauth_escape($this->client['secret']);
		}
		$secret_key .= '&';
		if( isset($this->client['oauth_token_secret']) )
		{
			$secret_key .= $this->_oauth_escape($this->client['oauth_token_secret']);
		}
		// sign url
		switch($this->client['signature'])
		{
			case 'PLAINTEXT':
				return urlencode($secret_key);
			case 'HMAC-SHA1':
				$this->sbs = $this->_oauth_escape($options['method']).'&'
										.$this->_oauth_escape($options['path']).'&'
										.$this->_oauth_escape($this->_normalized_parameters($options['parameters']));
				return base64_encode(hash_hmac('sha1',$this->sbs,$secret_key,TRUE));
			default:
				$this->exception('Unknown signature method for FS_OAuth');
			break;
		}
	}
	/**
	 * get_header_string
	 *
	 * oAuth 1 create string for post header
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 */
	public function get_header_string( $parameters = array() ) 
	{
		// sort parameters by key
		ksort($parameters);
		// predefine result var
		$result = 'OAuth ';
		// loop through parameter
		foreach( $parameters as $key => $value )
		{
			if (strpos($key,'oauth_') !== 0)
			{
				continue;
			}
			if (is_array($value))
			{
				foreach( $value as $val )
				{
					$result .= $key .'="' . $this->_oauth_escape($val) . '", ';
				}
			}
			else
			{
				$result .= $key . '="' . $this->_oauth_escape($value) . '", ';
			}
		}
		return preg_replace('/, $/','',$result);
	}

	//
// end of class
}