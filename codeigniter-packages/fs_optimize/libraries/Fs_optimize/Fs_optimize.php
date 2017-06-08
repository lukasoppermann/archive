<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006 - 2012 EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Optimize Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/driver/optimize
 */
class Fs_optimize extends CI_Driver_Library {
	
	protected $valid_drivers = array( 'js', 'css' );
	public $current_driver;
	public $current;
	public $CI;
	public $data;
	private static $variables;
	
	function __construct()
	{
		$this->CI =&get_instance();
		// load helpers
		$this->CI->load->helper('fs_js');
		$this->CI->load->helper('fs_css');
		$this->CI->config->load('fs_optimize');
		// define variables
		self::$variables = NULL;
	}
	
	function init( $driver, $params )
	{
		// set current driver
		$this->current_driver = $driver;
		// set current variables
		$this->current['data'] 					=&$this->data[$this->current_driver];
		$this->current['ext'] 					= (isset($params['ext']) ? $params['ext'] : "" );
		$this->current['dir'] 					= (isset($params['dir']) ? $params['dir'] : "" );
		$this->current['cache_dir'] 		= (isset($params['cache_dir']) ? $params['cache_dir'] : "" );
		$this->current['tags'] 					= (isset($params['tags']) ? $params['tags'] : "" );
		$this->current['regex'] 				= (isset($params['regex']) ? $params['regex'] : "" );
		$this->current['gzip']					= (isset($params['gzip']) ? $params['gzip'] : "" );
		$this->current['expire']				= (isset($params['expire']) ? $params['expire'] : "" );
		$this->current['content_type']	= (isset($params['content_type']) ? $params['content_type'] : "" );
	}
	// --------------------------------------------------------------------
	/**
	 * add
	 *
	 * add file, string or array, if array you can use
	 * 'group_name' => 'file_name' notation,
	 * or [] => 'filename' for default group || second (group) param
	 *
	 * @access	public
	 * @param	string / array
	 * @param	string
	 */
	function add($files = null, $group = 'default')
	{
		// extract files
		$this->extract_files($files, $group, 'add');
	}
	// --------------------------------------------------------------------
	/**
	 * delete
	 *
	 * delete file, string or array, if array you can use
	 * 'group_name' => 'file_name' notation,
	 * or [] => 'filename' for default group || second (group) param
	 *
	 * @access	public
	 * @param	string / array
	 * @param	string
	 */
	function delete($files = null, $group = 'default')
	{
		$this->extract_files($files, $group, 'delete');
	}
	// --------------------------------------------------------------------
	/**
	 * add_lines
	 *
	 * add lines to class
	 *
	 * @access	public
	 * @param	string / array	 
	 */
	function add_lines($lines, $group = 'default', $before = FALSE)
	{
		// check if lines are supposed to be set before the files
		$pos = ($before != FALSE ? 'before' : 'after');
		// add lines
		$this->current['data']['lines'][$pos][$group] = 
		(isset($this->current['data']['lines'][$pos][$group]) ? $this->current['data']['lines'][$pos][$group].' ' : '').$lines;
	}
	// --------------------------------------------------------------------
	/**
	 * variables
	 *
	 * adds variables to class
	 *
	 * @access	public
	 * @param	array
	 * @param	group
	 */
	function variables($variables)
	{
		// loop through $variables
		foreach($variables as $key => $value)
		{
			self::$variables[$this->current_driver][$key] = $value;
		}	
	}
	// --------------------------------------------------------------------
	/**
	 * extract files
	 *
	 * extracts files from input
	 *
	 * @access	public
	 * @param	string
	 * @param	string	
	 * @param	string	 
	 */
	function extract_files($files, $group, $action)
	{
		if( isset($files) )
		{
			// if has comma, explode into array
			if( is_string($files) && strpos($files, ',') == TRUE )
			{
				// multiple files added
				$files = explode(',', $files);
			}
			// check if string is added 
			if( is_string($files) )
			{
				// if single file is added
				if( strpos($files, ',') == FALSE )
				{
					$this->{$this->current_driver}->process_file(trim($files), $group, $action);
				}
			}
			// check for array
			elseif( is_array($files) )
			{
				// loop through files
				foreach($files as $file)
				{
					// add file
					$this->{$this->current_driver}->process_file(trim($file), $group, $action);
				}
			}
		}	
	}
	// --------------------------------------------------------------------
	/**
	 * process file
	 *
	 * adds or deletes file from group 
	 *
	 * @access	public
	 * @param	string
	 * @param	string	 
	 */
	function process_file($file, $group, $action = 'add')
	{
		// if file is external
		if(substr($file, 0, 5) == 'http:' || substr($file, 0, 6) == 'https:' || substr($file, 0, 2) == '//' || substr($file, 0, 4) == 'www.')
		{
			$_file = $file;
		}
		// if element is files (indicated by ".ext" suffix)
		elseif( substr($file, -strrpos($file,'.')) == '.'.$this->current['ext'] )
		{
			// if file exists in dir, add
			if( file_exists($this->current['dir'].trim($file, '/')) )
			{
				$_file = $this->current['dir'].trim($file, '/');
			}
			// if file exists in other dir on server
			elseif( file_exists('./'.trim($file, '/')) )
			{
				$_file = trim($file, '/');
			}
		}
		// if file exists with this exact name (and added ".ext") in default dir
		elseif(file_exists('./'.$this->current['dir'].trim($file, '/').'.'.$this->current['ext']))
		{
			$_file = $this->current['dir'].trim($file, '/').'.'.$this->current['ext'];
		}
		else
		{
			// find all files in dir with name beginning with arg
			$files = glob('{./'.$this->current['dir'].trim($file, '/').'-*,./'.$this->current['dir'].trim($file, '/').'.'.$this->current['ext'].'}', GLOB_BRACE);
			// if array is NOT empty, rsort and use the first value (latest Version) 
			if(!empty($files))
			{
				rsort($files);
				$_file = substr($files[0], 2);
			}
			elseif( $files = glob('{./'.$this->current['dir'].'*/'.trim($file, '/').'-*,./'.$this->current['dir'].'*/'.trim($file, '/').'.'.$this->current['ext'].'}', GLOB_BRACE) )
			{
				rsort($files);
				$_file = substr($files[0], 2);
			}
		}
		// if action = add
		if($action == 'add')
		{
			// check if file is NOT already in array
			if( isset($_file) && ( !isset($this->current['data']['files'][$group]) || !in_array($_file, $this->current['data']['files'][$group]) ) )
			{
				// add
				$this->current['data']['files'][$group][] = $_file;
			}
		}
		elseif($action == 'delete')
		{
			// check if file is in array, DELETE
			if( isset($_file) && ( isset($this->current['data']['files'][$group]) && in_array($_file, $this->current['data']['files'][$group])) )
			{
				unset($this->current['data']['files'][$group][array_search($_file, $this->current['data']['files'][$group])]);
			}
		}
	}
	// --------------------------------------------------------------------
	/**
	 * compress
	 *
	 * compress stylesheet files
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */	
	function compress( $group = null )
	{
		// check for cache directory, create if it does not exist
		if( !is_dir('./'.$this->current['cache_dir']) )
		{
			mkdir('./'.$this->current['cache_dir'], 0777);
		}
		// set variables
		$gzip = FALSE;
		$ext = '';
		if( substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && !substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip=0') &&
		 	$this->current['gzip'] == TRUE )
		{
			$gzip = TRUE;
			$ext = '.gz';
		}
		// check for files and implode
		if( isset($this->current['data']['files']) && isset($this->current['data']['files'][$group]) )
		{
			$name[] = implode('',$this->current['data']['files'][$group]);
		}
		// check for lines
		if( isset($this->current['data']['lines']) )
		{
			if( isset($this->current['data']['lines']['before']) && isset($this->current['data']['lines']['before'][$group]) )
			{
				$name[] = $this->current['data']['lines']['before'][$group];
			}
			if( isset($this->current['data']['lines']['after']) && isset($this->current['data']['lines']['after'][$group]) )
			{
				$name[] = $this->current['data']['lines']['after'][$group];
			}
		}
		// create file name from all files
		$filename = $this->current['cache_dir'].md5(implode('',$name)).'.'.$this->current['ext'].$ext.'.php';
		// if cache file does not exist or environment is developement
		if( !file_exists('./'.$filename) || ENVIRONMENT == 'development' )
		{
			// add lines to before files in file content
			if( isset($this->current['data']['lines']['before'][$group]) )
			{
				$output[] = trim($this->current['data']['lines']['before'][$group]);
			}
			// check if files exists
			if( isset($this->current['data']['files']) && isset($this->current['data']['files'][$group]) )
			{
				// merge all files
				foreach( $this->current['data']['files'][$group] as $file )
				{
					if( substr($file, 0, 5) == 'http:' || substr($file, 0, 6) == 'https:' || substr($file, 0, 3) == '//:' || substr($file, 0, 4) == 'www.' )
					{
						$files[] = trim($file);	
					}
					elseif( file_exists( './'.$file ) )
					{
						// get file content
						$output[] = trim(file_get_contents('./'.$file));
					}
					else
					{
						log_message('debug', 'The file '.$file.' does not exist in the proposed directory.');
					}
				}
			}
			// add lines to file content
			if( isset($this->current['data']['lines']['after'][$group]) )
			{
				$output[] = trim($this->current['data']['lines']['after'][$group]);
			}
			//
			$output = implode('',$output);
			// check if output has content 
			if( $length = strlen($output) > 0 )
			{
				if( is_array($this->current['regex']) )
				{
					// loop through regex from config
					foreach($this->current['regex'] as $key => $regex)
					{
						// if is regex with find -> replace
						if(is_array($regex))
						{
							foreach($regex as $find => $replace)
							{
								$output = preg_replace( $find, $replace, $output );
							}
						}
						// else if is normal string -> try to run function
						else
						{
							$callback = $this->create_callback( 'callback_'.$key );
							$output = preg_replace_callback( $regex, $callback, $output );
						}
					}
				}
				// check if gzip can me used
				if( $gzip === TRUE && $this->current['gzip'] == TRUE && ( $length > 1024 || strlen($output) > 1024 ) )
				{
					$header = '<?php
						ob_start("ob_gzhandler");
						header("content-type: '.$this->current['content_type'].'; charset: UTF-8");
						header("cache-control: must-revalidate");
						header("expires: ".gmdate(\'D, d M Y H:i:s\', time() + '.$this->current['expire'].')." GMT"); 
					?>';
				}
				// if gzip can't be used
				else
				{
					$header = '<?php header("content-type: '.$this->current['content_type'].'; charset: UTF-8");?>';
				}
				// run driver specific output fn
				$output = $this->{$this->current_driver}->output($output);
				// add header to output 
				$output = trim(preg_replace('#[\r\n|\r|\n|\t|\f]#',' ',$header).$output);
				// replace multiple whitespaces
				while(strpos($output, '  ') !== false) 
				{ 
					$output = str_replace('  ', ' ', $output); 
				}
				// write cache file
				file_put_contents($filename, $output);
				$files[] = $filename;
			}
		}
		// if file exists and not in dev mode, just load the external files
		else
		{
			foreach( $this->current['data']['files'][$group] as $file )
			{
				if( substr($file, 0, 5) == 'http:' || substr($file, 0, 6) == 'https:' || substr($file, 0, 3) == '//:' || substr($file, 0, 4) == 'www.' )
				{
					$files[] = trim($file);	
				}
			}
			$files[] = $filename;
		}
		// return compressed file
		return $files;
	}
	// --------------------------------------------------------------------
	/**
	 * get
	 *
	 * get files depending on group
	 *
	 * @access	public
	 * @param	string / array
	 * @return 	string
	 */
	function get($groups = NULL, $compress = TRUE, $link = FALSE, $data = null)
	{
		// if no group is selected
		if( $groups == NULL && ( isset($this->current['data']['files'] ) || isset($this->current['data']['lines']) )  )
		{
			// loop through all groups
			foreach($this->current['data']['files'] as $group => $files)
			{
				$output[] = $this->process($group, $compress, $link, $data);
			}
		}
		// if group is given
		else
		{
			// check if string with comma
			if( !is_array($groups) && strpos($groups, ',') )
			{
				$groups = explode(',',$groups);
			}
			// just one group
			if( !is_array($groups) )
			{
				// check if group exists 
				if( isset($this->current['data']['files'][$groups]) || isset($this->current['data']['lines']['before'][$groups]) ||
					isset($this->current['data']['lines']['after'][$groups]) )
				{
					$output[] = $this->process($groups, $compress, $link, $data);
				}
			}
			// is array
			else
			{
				// loop through groups
				foreach($groups as $group)
				{
					// check if group exists 
					if( isset($this->current['data']['files'][$group]) || isset($this->current['data']['lines']['before'][$group]) ||
						isset($this->current['data']['lines']['after'][$group]) )
					{
						$output[] = $this->process($group, $compress, $link, $data);
					}
				}
			}
		}
		// return files
		if( isset($output) && is_array($output) )
		{
			return implode('', $output);
		}
	}
	// --------------------------------------------------------------------
	/**
	 * process
	 *
	 * process files
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return 	string
	 */
	function process($group = NULL, $compress = NULL, $link = FALSE, $data = null)
	{	
		// check if any files are present
		if( ( array_key_exists('files', $this->current['data']) || array_key_exists('lines', $this->current['data']) ) )
		{
			// if compression is activated
			if($compress === TRUE)
			{
				$files = $this->{$this->current_driver}->compress($group);
			}
			// else
			else
			{
				// get files
				if( array_key_exists($group, $this->current['data']['files']) )
				{
					$files = $this->current['data']['files'][$group];
				}
				// get lines before files
				if(isset( $this->current['data']['lines']['before'][$group]) )
				{
					$output[] = "\t".str_ireplace('[file]', $this->current['data']['lines']['before'][$group], $this->current['tags']['lines'])."\n";
				}
				// get lines after files
				if(isset( $this->current['data']['lines']['after'][$group]) )
				{
					$lines_after = "\t".str_ireplace('[file]', $this->current['data']['lines']['after'][$group], $this->current['tags']['lines'])."\n";
				}
			}
			// get script-tag 
			$tag = (isset($this->current['tags'][$group])) ? $this->current['tags'][$group] : $this->current['tags']['default'];
			// check if files are added
			if( isset($files) )
			{
				// loop through files
				foreach($files as $file)
				{
					if( $link != TRUE )
					{
						// if file is external
						if( substr($file, 0, 5) == 'http:' || substr($file, 0, 6) == 'https:' || substr($file, 0, 3) == '//:' || substr($file, 0, 4) == 'www.' )
						{
							$_tag = str_ireplace('[data]', ($data != null ? $data : ''), $tag);
							$output[] = "\t".str_ireplace('[file]', $file, $_tag)."\n";
							
						}
						// if file is internal: add base url
						else
						{
							$_tag = str_ireplace('[data]', ($data != null ? $data : ''), $tag);
							$output[] = "\t".str_ireplace('[file]', base_url().$file, $_tag)."\n";
						}
					}
					else
					{
						// if file is external
						if( substr($file, 0, 5) == 'http:' || substr($file, 0, 6) == 'https:' || substr($file, 0, 3) == '//:' || substr($file, 0, 4) == 'www.' )
						{
							$output[] = $file;
						}
						else
						{
							$output[] = base_url().$file;
						}
					}
				}
			}
			// predefine $delimiter
			$delimiter = '';
			if( $link != FALSE && $link !== TRUE )
			{
				$delimiter = $link;
			}
			// check if output exists
			$output = (isset($output) ? implode($delimiter, $output) : '');
			// return files in right syntax
			return $output.(isset($lines_after) ? $lines_after : '');
		}
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
}
// End Class

/* End of file Optimize.php */
/* Location: ./system/libraries/optimize/optimize.php */