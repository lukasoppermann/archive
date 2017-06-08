<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Url Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Url
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/url
 */
class CI_FS_url {

	var $CI;
	var $parts;
	
	public function __construct($params = null)
	{
		// ----------------
		// define CI instance		
		$this->CI = &get_instance();
		// ----------------
		// load variable helper
		$this->CI->load->helper('fs_variable');
		// ----------------
		// load library
		$this->CI->load->library('fs_log');
		// ----------------
		// log class initialization
		log_message('debug', "FS_Url Class Initialized");
		// ----------------
		// get parts 
		$this->parts = $this->CI->uri->segment_array();
		// ----------------
		// check for short_url
		if( $this->CI->config->item('short_url') === TRUE )
		{
			$this->short_url();
		}
		// ----------------
	}
// --------------------------------------------------------------------
/**
 * parts - lets you retrieve a part from the url and save into config
 *
 * @param int
 * @param string
 * @param string	 
 * @return string
 */
	function part($position, $config = null, $default = null, $compare = null)
	{
		// get element from url
		$element = $this->CI->uri->segment($position);
		// set default if no element
		if($default != null)
		{
			if( $element == null)
			{
				$element = $default;
			}
			elseif( !in_array($element, $compare) )
			{
				$element = $default;
			}
		}
		if( $config != null )
		{
			// set config
			$this->CI->config->set_item($config, $element);
		}
		// set url parts
		$this->CI->config->set_item('url_parts', ltrim($this->CI->config->item('url_parts').'/'.$element,'/') );
		// set url parts array
		if(isset($config) && $config != null)
		{
			$array = array_merge((array) $this->CI->config->item('url_parts_array'), array($config => $element));
		}
		else
		{
			$array = $this->CI->config->item('url_parts_array');
			$array[] = $element;
		}
		$this->CI->config->set_item('url_parts_array', array_filter($array) );
		// return element
		return $element;	
	}
// --------------------------------------------------------------------
/**
 * short_url - check for short_url and redirects if found
 *
 */
	function short_url()
	{
		if( isset($this->parts[key($this->parts)]) )
		{
			// get potential short_url
			$short_url = $this->parts[key($this->parts)];
			// check if is language or controller
			if( $this->short_url_forbidden($short_url) != 'FALSE' )
			{
				// load assets
				$this->CI->load->helper(array('language'));
				$this->CI->lang->load('url');
				// test for short_url
				$long_url = db_select(config('system/current/db_prefix').config('db_short_url'), array('short_url' => $short_url), 
				array('single' => TRUE, 'json' => FALSE));
				//
				if( is_array($long_url) )
				{
					$response = $this->check_url($long_url['long_url'], FALSE);
					//
					if( $response['status'] == 200 )
					{
						// log
						$this->CI->fs_log->log( sprintf(lang('short_url_clicked'), $short_url, $long_url['long_url']), 5);
						// redirect
						redirect($long_url['long_url']);
					}
					elseif( $response['status'] == 301 )
					{
						// save new location to db
						db_update(config('system/current/db_prefix').config('db_short_url'), array('short_url' => $short_url), 
						array('long_url' => $response['location'], 'status' => $response['status']));
						// log
						$this->CI->fs_log->log( sprintf(lang('short_url_moved'), $short_url, $long_url['long_url']), 5);
						// redirect
						redirect($response['location']);
					}
					else
					{
						db_update(config('system/current/db_prefix').config('db_short_url'), array('short_url' => $short_url), 
						array('status' => $response['status']));
						// log
						$this->CI->fs_log->log( sprintf(lang('short_url_failed'), $short_url, $long_url['long_url']), 6);
						// return false
						return FALSE;
					}
				}
			}
		}
	}
// --------------------------------------------------------------------
/**
 * check_url - checks url and returns status
 *
 * @param string
 * @param boolean for status only
 * @return int
 */
	function check_url( $_url, $status = TRUE )
	{
		// parse url
		$url 	= parse_url($_url);
		$host 	= $url['host'];
		$query 	= isset($url['query']) ? $url['query'] : '';
		$path 	= isset($url['path']) ? $url['path'] : '';
		// get url port
		$port = isset($url['port']) ? $url['port'] : '';
		if( isset($port) )
		{
			$port = 80;
		}
		// prepare request
		if( $path != null )
		{
			$head = $path.'?'.$query;
		}
		else
		{
			$head = '/';
		}
		//
		$request = "HEAD ".$head." HTTP/1.1\r\n"
		          ."Host: $host\r\n"
		          ."Connection: close\r\n"
		          ."\r\n\0";
		$address = gethostbyname($host);
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_connect($socket, $address, $port);
		// send request
		socket_write($socket, $request, strlen($request));
		// get response
		$response = explode(' ', socket_read($socket, 1024));
		$response['status'] = $response[1];
		// close connection
		socket_close($socket);
		// return status
		if( $status === TRUE ) 
		{
			return $response['status'];
		}
		else
		{
			// get location if needed
			if( $response['status'] == 301 && file_get_contents($_url) )
			{
				foreach($http_response_header as $val)
				{
					$key = substr($val, 0, strpos($val, ':'));
					// check if location
					if( strtolower($key) == 'location' )
					{
						$response['location'] = trim(substr($val, strpos($val,':')+1));
					}
				}
			}
			// return full response
			return $response;
		}
	}
// --------------------------------------------------------------------
/**
 * shorten_url - creates a short_url if it does not exist
 *
 * @param string
 * @return int
 */
	function shorten_url( $url, $short_url = null )
	{
		// load assets
		$this->CI->load->helper(array('text','language'));
		$this->CI->lang->load('url');
		// prep url
		$url = rtrim(trim($url),'/');
		if( !substr($url, 0, 7) == 'http://' && !substr($url, 0, 8) == 'https://' )
		{
			$url = 'http://'.$url;
		}
		// check for url status
		$response = $this->check_url($url, FALSE);
		// if url is permanently moved
		if( $response['status'] == 301 )
		{
			$url = $response['location'];
		}
		// check if url is okay
		if( $response['status'] == 301 || $response['status'] == 200 )
		{
			// check for url in db
			$db_url = db_select(config('system/current/db_prefix').config('db_short_url'), array('long_url' => $url), 
			array('json' => null, 'single' => TRUE));
			// if already in db
			if( is_array($db_url) )
			{
				// log
				$this->CI->fs_log->log( sprintf(lang('url_exists_log'), $url, $db_url['short_url']), 5);
				// return short url
				return $db_url['short_url'];
			}
			// url not in db
			else
			{
				if( $short_url != false && $short_url != null )
				{
					// prep short_url
					$short_url = trim(to_alphanum( str_replace(' ', '-',trim($short_url)), array(' ' => '-','&' => '+')),'-');
					// check for url in db
					$db_url = db_select(config('system/current/db_prefix').config('db_short_url'), array('short_url' => $short_url), 
					array('json' => null, 'single' => TRUE));
					//
					if( is_array($db_url) || $this->short_url_forbidden($short_url) === 'FALSE')
					{
						// log
						$this->CI->fs_log->log( sprintf(lang('short_url_exists_log'), $short_url), 6);
						// return error
						return array('error' => lang('short_url_exists'));
					}
					else
					{
						// insert url into db
						db_insert(config('system/current/db_prefix').config('db_short_url'), array('short_url' => $short_url, 'long_url' => $url));
						// log
						$this->CI->fs_log->log( sprintf(lang('short_url_created'), $short_url, $url), 5);
						// return short_url
						return $short_url;
					}
				}
				else
				{
					// create initial short_url
					$short_url = random_string('alnum', mt_rand(3, config('short_url_length')));
					// find available short_url
					while( db_select(config('system/current/db_prefix').config('db_short_url'), array('short_url' => $short_url), 
					array('json' => null, 'single' => TRUE)) )
					{
						$short_url = random_string('alnum', mt_rand(3, config('short_url_length')));
					}
					// insert into db
					db_insert(config('system/current/db_prefix').config('db_short_url'), array('short_url' => $short_url, 'long_url' => $url));
					// log
					$this->CI->fs_log->log( sprintf(lang('short_url_created'), $short_url, $long_url), 5);
					// return short_url
					return $short_url;
				}
			}
		}
		else
		{
			// log
			$this->CI->fs_log->log( sprintf(lang('url_status_error_log'), $url), 6);
			// return error
			return array('error' => lang('url_status_error'));
		}
	}
// --------------------------------------------------------------------
/**
 * shorten_url_forbidden - check if short_url can be used
 *
 * @param string
 * @return boolean
 */
	function short_url_forbidden( $short_url )
	{
		// load assets
		$this->CI->load->helper('language');
		$this->CI->lang->load('url');
		// check length
		if(strlen($short_url) > 2)
		{
			// scan dir if dir exists
			if( is_dir('./'.$short_url.'/') )
			{
				$dir = scandir('./'.$short_url.'/');
			}
			// check routes
			$routes = array_keys($this->CI->router->routes);
			// if not in routes
			if( !in_array($short_url, $routes) )
			{
				foreach($routes as $route)
				{
					$explode = explode('/',$route);
					// check route part
					if( $explode[0] == $short_url )
					{
						// log
						$this->CI->fs_log->log( sprintf(lang('short_url_route'), $short_url), 6);
						//
						return 'FALSE';
					}
				}
			}
			elseif( in_array($short_url, $routes) || is_array($dir) )
			{
				// log
				$this->CI->fs_log->log( sprintf(lang('short_url_route'), $short_url), 6);
				//
				return 'FALSE';
			}
			else
			{
				// not forbidden
				return TRUE;
			}
		}
		// to short
		return 'FALSE';
	}
// --------------------------------------------------------------------
/**
 * switch_lang - retrieves the current page in a different language or the parent page if sibling does not exists
 *
 * @param int
 * @param int
 * @return string
 */	
	function switch_lang($page_id, $lang_id)
	{
		
	}
// end of url class
}
/* End of file Url.php */
/* Location: ./system/libraries/Url.php */