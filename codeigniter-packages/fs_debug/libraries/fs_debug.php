<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System FS_Debug Class
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/fdebug
 */
 
class FS_Debug
{
	// define variables
	var $logs;
	var $CI;
	var $css;
	var $js;
	var $state;

	public function __construct()
	{
		$this->CI =& get_instance();
		// load helper
		$this->CI->load->helper('fs_debug');
		// add css & js
		if (defined('ENVIRONMENT') && ENVIRONMENT != 'production')
		{
			$this->add_js("benchmark", base_url().'libs/js/benchmark-0.0.1.js');
			$this->add_css("benchmark", base_url().'libs/css/benchmark-0.0.1.css');
		}
		// log initialization
		log_message('debug', "FS_Debug Class Initialized");
     }  	     
 	// --------------------------------------------------------------------
 	/**
 	 * log
 	 *
 	 * add anything to the debug log
 	 *
 	 * @access	public
 	 * @param	string / array	 
 	 */
    function log($var, $comment = null, $from = null)
    {
        // if not from is set
        if($from == null)
        {
            // get file from which function is called
            $file = debug_backtrace();
            // get just file basename
            $from = basename($file[0]['file']);
        }
		// add css & js
		$this->add_js("log", base_url().'libs/js/debug-0.0.1.js');
		$this->add_css("log", base_url().'libs/css/debug-0.0.1.css');
        // log information with current time
        $this->logs[] = array('var' => $var, 'comment' => $comment, 'from' => $from, 'time' => date('m/d/Y - H:i:s'));
    }
    // --------------------------------------------------------------------
 	/**
 	 * show_log
 	 *
 	 * show debug log
 	 *
 	 * @access	public
 	 * @param	string / array	 
 	 */
 	 function show_log()
 	 {
 	     // check if CI is in development Environment
 	     if (defined('ENVIRONMENT') && ENVIRONMENT != 'production' && is_array($this->logs))
 	     {
 	         // define vars
 	         $output = '';
 	         // prepare each log item from display
 	         foreach($this->logs as $log)
     	     {
     	         // if item is array
     	         if( is_array($log['var']) )
     	         {
     	             // get print_r output for nice format
     	             ob_start();
                     print_r($log['var']);
                     $dump = ob_get_contents();
                     ob_end_clean();
                     // save output inside pre tag to keep format
     	             $content = "<pre class='log-array'>".$dump."</pre>";
     	         }
     	         // if item is string
     	         elseif( is_string($log['var']) )
     	         {
     	             // display string in span
     	             $content = "<span class='log-string'>".$log['var']."</span>";
     	         }
     	         // for everything else
     	         else
     	         {
     	              // get print_r output for nice format
     	             ob_start();
                     print_r($log['var']);
                     $dump = ob_get_contents();
                     ob_end_clean();
                     // save output inside pre tag to keep format
     	             $content = "<pre class='log-output'>".$dump."</pre>";  
     	         }
     	         // add to output
     	         $output .= '<div class="log-item">
     	                        <div class="log-meta-data">
									<div class="log-left">
     	                            	<span class="log-from">'.$log['from'].'</span> -
     	                            	<span class="log-time">'.$log['time'].'</span>
									</div>
									<div class="log-comment"><span>'.$log['comment'].'</span></div>
     	                        </div>
     	                        <div class="log-content">'.$content.'</div>
     	                    </div>';
     	     }
     	     // return log
     	     return "<div class='log-container'><span class='log-close'>×</span>".$output."</div>";
 	     }
 	 }
 	// --------------------------------------------------------------------
 	/**
 	 * benchmark
 	 *
 	 * show benchmark bar
 	 *
 	 * @access	public
 	 * @param	string / array	 
 	 */
    function benchmark()
    {
		if( defined('ENVIRONMENT') && ENVIRONMENT != 'production'  && $this->state == TRUE )
		{
			// create benchmark view
			$benchmark = '<div class="benchmark">';
			$benchmark .= '<span class="benchmark-quick">Memory Usage: {memory_usage}&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Total Execution Time: {elapsed_time}</span>';
			$benchmark .= '<span class="benchmark-details">detailed benchmark</span>';
			$benchmark .= '</div>';
		    // return benchmark
		    return $benchmark;
		}
    }
 	// --------------------------------------------------------------------
 	/**
 	 * benchmark_init
 	 *
 	 * init benchmark
 	 *
 	 * @access	public
 	 * @param	string / array	 
 	 */
    function benchmark_init( $state = TRUE )
    {
		// define state
		$this->state = $state;
		//
		if( defined('ENVIRONMENT') && ENVIRONMENT != 'production' && $this->state == TRUE )
		{
			$this->CI->output->enable_profiler(TRUE);
		}
    }
// --------------------------------------------------------------------
/**
* add_js
*
* add js files to array
*
* @access	public
* @param	string / array	 
*/
	function add_js($id, $file)
	{
		if(!is_array($this->js) || !array_key_exists($id, $this->js))
		{
			$this->js[$id] = $file;
		}
	}
// --------------------------------------------------------------------
/**
* add_css
*
* add css files to array
*
* @access	public
* @param	string / array	 
*/
	function add_css($id, $file)
	{
		if(!is_array($this->css) || !array_key_exists($id, $this->css))
		{
			$this->css[$id] = $file;
		}
	}
// --------------------------------------------------------------------
/**
* js
*
* print js files loaded in fs_debug class
*
* @access	public
* @param	string / array	 
*/
	function print_js()
	{
		if( is_array($this->js) )
		{
			$output = "";
			// loop through array
			foreach($this->js as $file)
			{
				$output .= '<script type="text/javascript" src="'.$file.'"></script>'."\n\r";
			}
			// return files
			return $output;
		}
	}
// --------------------------------------------------------------------
/**
* css
*
* print css files loaded in fs_debug class
*
* @access	public
* @param	string / array	 
*/
	function print_css()
	{
		if( is_array($this->css) )
		{
			$output = "";
			// loop through array
			foreach($this->css as $file)
			{
				$output .= "\t".'<link rel="stylesheet" href="'.$file.'" type="text/css" media="screen" />'."\r\n";
			}
			// return files
			return $output;
		}
	}
// ----------------------------
// close Class
}