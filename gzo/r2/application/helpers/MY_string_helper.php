<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter MY_string Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/string
 */

// ------------------------------------------------------------------------
/**
 * replace_accents - replaces accent special characters with normal latin characters
 *
 * @param string
 * @return string
 */
function replace_accents($string, $find = array(NULL), $replace = array(NULL)) 
{ 
	// merge finds
	$find = array_merge(array('à','á','â','ã','ä','æ','å', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö','ø', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä','Æ','Å', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö','Ø', 'Ù','Ú','Û','Ü', 'Ý'), $find);
	// merge replace
	$replace = array_merge(array('a','a','a','a','ae','ae','aa', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','oe','oe', 'u','u','u','u', 'y','y', 'A','A','A','A','AE','AE','AA', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','OE','OE', 'U','U','U','U', 'Y'), $replace);
	// replace and return
	return str_replace( $find, $replace, $string); 
}

/* End of file MY_string_helper.php */
/* Location: ./application/helpers/MY_string_helper.php */