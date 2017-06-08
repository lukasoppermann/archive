<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Google Analytics Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Url
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/libraries/analytics
 */
class CI_FS_google {

	var $CI;
	
	public function __construct($params = null)
	{
		// ----------------
		// define CI instance		
		$this->CI = &get_instance();
		// ----------------
		// load variable helper
		$this->CI->load->helper('fs_variable');
		// ----------------
		// log class initialization
		log_message('debug', "FS_Google Class Initialized");
	}
// --------------------------------------------------------------------
/**
 * analytics - insert analytics code into page
 *	 
 * @return string
 */
	function analytics( $code = null )
	{	
		if( $code != null )
		{
			$this->CI->fs_optimize->js->add_lines(
			";var _gaq = [['_setAccount', '".$code."'], ['_trackPageview']];
			setTimeout(function() {
				(function(d, t, a) {
				 var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
				 g[a] = a;
				 g.src = '".( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http' )."://www.google-analytics.com/ga.js';
				 s.parentNode.insertBefore(g, s);
				}(document, 'script', 'async'));
			}, 0);");
		}
	}
// --------------------------------------------------------------------
/**
 * fonts - load google fonts
 *
 * @param int
 * @param string
 * @param string	 
 * @return string
 */	
	function fonts( $fonts = null, $callback = false )
	{
		if( $fonts != null )
		{
			// check if callback is given
			if( $callback == null || $callback == '' )
			{
				$callback = "function(){}";
			}
			else
			{
				$callback = "function(){".implode('',$callback)."}";
			}
			// if fonts is string, explode
			if( !is_array($fonts) )
			{
				$fonts = explode(',', $fonts);
			}
			// 
			$this->CI->fs_optimize->js->add_lines(
				";WebFontConfig = {
					google: { families: [ '".implode("', '",$fonts)."' ] },
				   active: ".$callback."
				};
				setTimeout(function() {
					(function(d, t, a) {
						var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
						g[a] = a;
						g.src = '".( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http' )."://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
						s.parentNode.insertBefore(g, s);
					})(document, 'script', 'async');
				}, 0);"
			);
		}
	}
// end of google analytics class
}
/* End of file analytics.php */
/* Location: ./system/libraries/analytics.php */