<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Js Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Java Script
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/js
 */
class CI_Js {

	var $CI;
	var $data;
	private static $variables;
	
	public function __construct()
	{
		$this->CI =& get_instance();
		// define variables
		self::$variables = NULL;
		// get config data
		$this->CI->config->load('js');
		$this->data = $this->CI->config->item('js');
		// Automatically load the css helper
		$this->CI->load->helper('js');
		
		log_message('debug', "Js Class Initialized");
	}
	// --------------------------------------------------------------------
	/**
	 * add
	 *
	 * add js files to class
	 *
	 * @access	public
	 * @param	string / array	 
	 */
	function add(/*group, value, value, value = array()*/)
	{
		// get args
		$args = func_get_args();
		// work files
		$this->process_files($args, 'add');
	}
	// --------------------------------------------------------------------
	/**
	 * add_lines
	 *
	 * add js scripts to class
	 *
	 * @access	public
	 * @param	string / array	 
	 */
	function add_lines(/*group, value*/)
	{
		// get args
		$args = func_get_args();
		// check for group
		if( count($args) > 1 )
		{
			reset($args);
			$first = key($args);
			// check if group is valid
			if( in_array($args[$first], $this->data['groups']) )
			{
				$group = $args[$first];
				unset($args[$first]);
				$lines = $args[key($args)];
			}
		}
		else
		{
			$group = 'default';
			$lines = $args[key($args)];			
		}

		if(preg_match('#<script\b[^>]*>(.*)<\/script>#s', $lines, $match))
		{
			$lines = $match[1];
		}
		// work files
		if(isset($this->data['lines'][$group]))
		{
			$this->data['lines'][$group] .= ' '.$lines;
		}
		else
		{
			$this->data['lines'][$group] = $lines;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * delete
	 *
	 * delete js files from class
	 *
	 * @access	public
	 * @param	string / array
	 */
	function delete(/*group, value, value, value = array()*/)
	{
		// get args
		$args = func_get_args();
		// work files
		$this->process_files($args, 'delete');
	}
	// --------------------------------------------------------------------
	/**
	 * variables
	 *
	 * adds variables to class
	 *
	 * @access	public
	 * @param	string / array
	 */
	function variables(/*array or string|strings*/)
	{
		// get args
		$args = func_get_args();
		// prepare $args
		if(count($args) == 1 && is_array($args[0]))
		{
			$variables = $args[0];
		}
		elseif(count($args) > 1 && !is_array($args[0]))
		{
			$vars = explode(',',$args[0]);
			foreach($vars as $var)
			{
				$tmp = explode(':',$var);
				$variables[trim($tmp[0])] = trim($tmp[1]);
			}
		}
		else
		{	
			$tmp = explode(':',$args[0]);
			$variables[trim($tmp[0])] = trim($tmp[1]);
		}
		// add to variables
		foreach($variables as $var => $value)
		{
			self::$variables[$var] = $value;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * process_files
	 *
	 * process js files depending on action
	 *
	 * @access	public
	 * @param	string / array
	 * @param	string
	 */
	function process_files($args, $action)
	{
		reset($args);
		$first = key($args);
		// check for one array with an array as element
		if(count($args) == 1 && is_array($args[$first]))
		{
			$args = $args[$first];
			$first = key($args);
		}
		// check if first arg is group, else assign to 'default'
		if( in_array($args[$first], $this->data['groups']) )
		{
			$group = $args[$first];
			unset($args[$first]);
		}
		else
		{
			$group = 'default';
		}
		foreach($args as $key => $arg)
		{
			// if is array turn into 2d array
			if( isset($arg) && is_array($arg) )
			{
				foreach($arg as $tmp)
				{
					if( isset($arg) && !empty($tmp) && $tmp != NULL)
					{
						if( preg_match('/(\w*)\,(\w*)/',$tmp) )
						{
							foreach(explode(',',$tmp) as $tmp_file)
							{
								$args[] = trim($tmp_file);
							}
						}
						else
						{
							$args[] = trim($tmp);
						}
					}
				}
				unset($args[$key]);
			}
			elseif( preg_match('/(\w*)\,(\w*)/',$arg) )
			{
				foreach(explode(',',$arg) as $file)
				{
					$args[] = trim($file);
				}
				unset($args[$key]);
			}
			// if element is empty unset
			elseif( !isset($arg) || empty($arg) || $arg == NULL)
			{
				unset($args[$key]);
			}
		}
		// loop trough all values (now 2d $args)
		foreach($args as $key => $arg)
		{
			// if element is js files (indicated by ".js" suffix)
			if( substr($arg, -3) == '.js' || preg_match("[http://|http:|www.|ww.]", $arg) )
			{
				if(preg_match("[http://|http:|www.|ww.]", $arg))
				{
					$file = $arg;
				}
				else
				{
					if( file_exists(trim($arg, '/')) )
					{
						$file = trim($arg, '/');
					}
					elseif( file_exists($this->data['dir'].trim($arg, '/')) )
					{
						$file = $this->data['dir'].trim($arg, '/');
					}
					
				}
			}
			// if file exists with this exact name (and added ".js") in default dir
			elseif(file_exists($this->data['dir'].trim($arg, '/').'.js'))
			{
				$file = $this->data['dir'].trim($arg, '/').'.js';
			}
			else
			{
				if( !empty($arg) )
				{
					// find all files in dir with name beginning with arg
					$files = glob($this->data['dir'].$arg.'-*');
				}
				// if array is NOT empty, rsort and use the first value (latest Version) 
				if( !empty($files))
				{
					rsort($files);
					$file = $files[0];
				}
			}
			// process depending on action
			if($action == 'delete')
			{
				// check if file is in array, DELETE
				if( isset($file) && (isset($this->data['files'][$group]) && in_array($file, $this->data['files'][$group])) )
				{
					unset($this->data['files'][$group][array_search($file, $this->data['files'][$group])]);
				}
			}
			elseif($action == 'add')
			{
				// check if file is NOT already in array, ADD
				if( isset($file) && (!isset($this->data['files'][$group]) || !in_array($file, $this->data['files'][$group])) )
				{
					$this->data['files'][$group][] = $file;
				}
			}
		}
	}
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * get js files depending on group
	 *
	 * @access	public
	 * @param	string / array
	 * @return $string
	 */
	function get($group = NULL, $compress = NULL)
	{
		$js = NULL;
		if( preg_match('/(\w*)\,(\w*)/',$group) )
		{
			foreach(explode(',',$group) as $g)
			{
				$tmp[] = trim($g);
			}
			$group = $tmp;
		}
		
		if($group == NULL)
		{
			foreach($this->data['files'] as $group => $files)
			{
				$js .= $this->process($group, $compress);
			}
		}
		elseif( is_array($group) )
		{
			foreach($group as $g)
			{
				if(array_key_exists($g, $this->data['files']))
				{
					$js .= $this->process($g, $compress);
				}
			}
		}
		elseif( isset($this->data['files']) && array_key_exists($group, $this->data['files']) )
		{
			$js .= $this->process($group, $compress);
		}
		// return css files
		return $js;
	}
	// --------------------------------------------------------------------
	/**
	 * process
	 *
	 * process js files
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return 	string
	 */
	function process($group = NULL, $compress = NULL)
	{	
		// predefine css var
		$js = NULL;
		// if compression is activated
		if($compress === TRUE && ENVIRONMENT !== 'testing' && ENVIRONMENT !== 'development')
		{
			$files = $this->compress($this->data['files'][$group], $group);		
		}
		// if compression is deactivated		
		else
		{
			$files = $this->data['files'][$group];
		}
		// loop through files
		foreach($files as $file)
		{
			// if file is external
			if(preg_match("[http://|http:|www.|ww.]", $file))
			{
				$js .= "\t".str_replace('[file]', $file, $this->data['tags'][$group])."\n";
			}
			// if file is internal: add base url
			else
			{
				$js .= "\t".str_replace('[file]', base_url().$file, $this->data['tags'][$group])."\n";
			}
		}
		// add script lines
		if($compress !== TRUE && isset($this->data['lines'][$group]))
		{
			$js .= "\t".str_replace('[file]', $this->data['lines'][$group], $this->data['tags']['lines'])."\n";
		}
		// return css files in right syntax
		return $js;
	}
	// --------------------------------------------------------------------
	/**
	 * compress
	 *
	 * compress js files
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */	
	function compress($files, $group = NULL)
	{
		$this->CI->load->library('jsmin');
		// check for cache directory, create if it does not exist
		if( !is_dir($this->data['cache_dir']) )
		{
			mkdir($this->data['cache_dir'], 0755);
		}
		// create file name from all files
		$js_files[] = $filename = $this->data['cache_dir'].md5(implode('',(array) $files)).'.js.php';
		// init variable
		$output = NULL;
		//
		if( !file_exists($filename) || ENVIRONMENT == 'development' )
		{
			// loop through files
			foreach( (array) $files as $tmp_file )
			{
				// if file exists and is not external read and put into $output
				if(file_exists( $var = $tmp_file ) && !preg_match("[http://|http:|www.|ww.]", $tmp_file) )
				{
					$output .= file_get_contents($var);
				}
				// if file is external just include it.
				elseif( preg_match("[http://|http:|www.|ww.]", $tmp_file) )
				{
					$js_files[] = $tmp_file;
				}
				else
				{
					log_message('debug', 'The file '.$var.' does not exist in the proposed directory.');
					unset($files[array_search($var, $files)]);					
				}
			}
			// add lines
			if(isset($this->data['lines'][$group]))
			{
				$output .= $this->data['lines'][$group];
			}
			
			if( !empty($output) )
			{
				foreach($this->data['regex'] as $key => $regex)
				{
					if(is_array($regex))
					{
						foreach($regex as $find => $replace)
						{
							$output = preg_replace( $find, $replace, $output );
						}
					}
					else
					{
						$callback = $this->create_callback( 'callback_'.$key );
						$output = preg_replace_callback( $regex, $callback, $output );
					}
				}
				$output = $this->CI->jsmin->minify($output);	
				if(strlen($output) > 1024 && $this->data['gzip'] == TRUE  && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
				{
					$header = '<?php
						ob_start("ob_gzhandler");
						header("content-type: text/javascript; charset: UTF-8");
						header("cache-control: must-revalidate");
						header("expires: ".gmdate(\'D, d M Y H:i:s\', time() + '.$this->data['offset'].')." GMT"); 
					?>';
				}
				else
				{
					$header = '<?php header ("content-type: text/javascript; charset: UTF-8"); ?>';
				}
				
				file_put_contents($filename, trim(preg_replace('#[ ]{2,}#',' ',preg_replace('#[\r\n|\r|\n|\t|\f]#',' ',$header)).$output));
				log_message('debug', 'The files "'.implode(', ',$files).'" have been compressed into "'.$filename.'".');
			}
		}
		else
		{
			// loop through files
			foreach( (array) $files as $tmp_file )
			{
				// if file is external just include it.
				if( preg_match("[http://|http:|www.|ww.]", $tmp_file) )
				{
					$js_files[] = $tmp_file;
				}	
			}
		}
		// return compressed file
		return $js_files;
	}	
	// --------------------------------------------------------------------
	/**
	 * create_callback
	 *
	 * create a callback function
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */	
	private static function create_callback( $name )
	{
		return create_function( '$match', 'return call_user_func( array( "' . __CLASS__ . '", "' . $name . '" ), $match );' );
	}
	// --------------------------------------------------------------------
	/**
	 * callback_variables
	 *
	 * replace variables
	 *
	 * @access	public
	 * @param	string
	 * @return 	string
	 */	
	public static function callback_variables( $match )
	{
		if(isset(self::$variables[$match[1]]))
		{
			return self::$variables[$match[1]];
		}
	}
	// --------------------------------------------------------------------
	/**
	 * empty_cache
	 *
	 * deletes all cached files from cache dir
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function empty_cache()
	{
		delete_files($this->data['cache_dir']);
	}
// END Js Class
}
/* End of file Js.php */
/* Location: ./system/libraries/Js.php */