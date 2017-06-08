<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Metadata Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/metadata
 */

// --------------------------------------------------------------------
/**
 * doctype - creates a valid doctype
 *
 * @param string $version - html version of document
 * @return string doctype
 */
function doctype($type = 'html5')
{
	global $_doctypes;
	// check if doctyes is loeaded
	if ( ! is_array($_doctypes))
	{
		// if not, try to load doctypes config
		if ( ! require_once(APPPATH.'config/doctypes.php'))
		{
			// if config does not exists, return FALSE 
			return FALSE;
		}
	}
	// if given doctype exists
	if (isset($_doctypes[$type]))
	{
		return $_doctypes[$type];
	}
	else
	{
		return FALSE;
	}
}
// --------------------------------------------------------------------
/**
 * html - creates a valid html element
 *
 * @param string $version - html version of document
 * @return string doctype
 */
function html( $variable = null, $_class = null, $ie = true )
{
	if( $ie == false )
	{
		return "<html".($_class != null ? ' class="'.$_class.'" ': '').($variable != null ? ' '.$variable : '').">\n";
	}
	else
	{
		// prepare class
		$class = ($_class != null ? ' '.$_class: '');
		// prepare variable
		$variable = ($variable != null ? ' '.$variable : '');
		// return html
		return  '<!--[if lt IE 8]>'."\n\t".
				'<html class="ie ie7'.$class.'"'.$variable.'>'."\n".
				'<![endif]-->'."\n".
				'<!--[if IE 8]>'."\n\t".
				'<html class="ie ie8'.$class.'"'.$variable.'>'."\n".
				'<![endif]-->'."\n".
				'<!--[if IE 9]>'."\n\t".
				'<html class="ie ie9'.$class.'"'.$variable.'>'."\n".
				'<![endif]-->'."\n".
				'<!--[if IE 10]>'."\n\t".
				'<html class="ie ie10'.$class.'"'.$variable.'>'."\n".
				'<![endif]-->'."\n".
				'<!--[if gt IE 10]>'."\n\t".
				'<html class="ie iegt10'.$class.'"'.$variable.'>'."\n".
				'<![endif]-->'."\n".
				'<!--[if !IE]>'."\n\t".
				'<html'.($_class != null ? ' class="'.$_class.'"': '').$variable.'>'."\n".
				'<![endif]-->'."\n";
	}

}
// --------------------------------------------------------------------
/**
 * favicon - creates links to favorite icons
 *
 * @param string | array
 * @return string
 */
function favicon($icon = NULL) 
{	
	$CI =& get_instance();
	// load config file
	$CI->config->load('fs_meta');
	// favorite icon link tags
	$fav_tags = $CI->config->item('favorite_tags');
	// check for favicon
	if( !is_array($icon) )
	{	
		// if has extension
		if( $pos = strrpos('.',$icon) )
		{
			$ext  = substr($icon, $pos);
			$icon = substr($icon, 0, $pos-1);
		}
		// if does not have extension
		else
		{
			$ext[] = 'png';
			$ext[] = 'ico';
		}
		// get default extensions for images
		$favs = $CI->config->item('favorite_images');
		// loop through extensions
		foreach($favs as $key => $val)
		{
			// check if favs is not set to false
			if( $val !== false )
			{
				// check if only one extension is given
				if( !is_array($ext) )
				{
					$file = $CI->config->item('dir_layout').ltrim($icon.$val.'.'.$ext,'/');
					// check if file exists
					if( file_exists($file) )
					{
						// if file exists, add
						$favicon[] = "\t".str_replace('[file]', base_url().$file, $fav_tags[$key])."\n";
					}
				}
				// if multiple extensions are given
				else
				{
					// loop through extensions
					foreach($ext as $ex)
					{
						$file = ltrim($icon.$val.'.'.$ex,'/');
						// check root
						if( file_exists($file) )
						{
							// if file exists, add
							$favicon[] = "\t".str_replace('[file]', base_url().$file, $fav_tags[$key])."\n";
						}
						// check if file exists
						elseif( file_exists($CI->config->item('dir_layout').$file) )
						{
							// if file exists, add
							$favicon[] = "\t".str_replace('[file]', base_url().$CI->config->item('dir_layout').$file, $fav_tags[$key])."\n";
						}
					}
				}
			}
		}
	}
	// if is array
	else
	{
		// loop through elements
		foreach($icon as $key => $val)
		{
			$favicon[] = "\t".str_replace('[file]', base_url().$CI->config->item('dir_layout').ltrim($val,'/'), $fav_tags[$key])."\n";
		}
	}
	// return favicon if set
	if( isset($favicon) && is_array($favicon) )
	{
		// return all icons
		return implode('', $favicon);
	}	
}
// --------------------------------------------------------------------
/**
 * meta - creates meta tags
 *
 * @param	array
 * @return	string
 */
function meta($opt = array()) 
{	
	$CI =& get_instance();
	// merge option array
	$opt = array_merge(array(
		'charset' 		=> $CI->config->item('charset'),
		'author' 		=> $CI->config->item('site-author'),
		'developer' 	=> $CI->config->item('developer'),
		'generator'  	=> $CI->config->item('generator'),
		'copyright' 	=> date('Y').' '.$CI->config->item('copyright-by'),
		'keywords' 		=> $CI->config->item('keywords'),
		'description' 	=> $CI->config->item('description'),
		'robots'		=> $CI->config->item('robots'),
		'humans'		=> $CI->config->item('humans'),
		'chromeframe' 	=> $CI->config->item('chromeframe')
	), $opt);
	// loop through meta options
	foreach($opt as $key => $value)
	{
		if( $key == 'charset' && !empty($value) )
		{
			$meta[] = "\t".'<meta http-equiv="content-type" content="text/html; charset='.$value.'" />'."\n";
		}
 		elseif( $key == 'chromeframe' && ($value == TRUE || $value == '') )
		{
			$meta[] = "\t".'<meta http-equiv="X-UA-Compatible" content="chrome=1">'."\n";
		}
		elseif( $key == 'humans' && !empty($value) )
		{
			$meta[] = "\t".'<link type="text/plain" rel="author" href="'.base_url().$value.'" />'."\n";
		}
		elseif( !empty($value))
		{
			$meta[] = "\t".'<meta name="'.$key.'" content="'.$value.'" />'."\n";
		}
		// reset key
		$key = null;
	}
	// return meta tags
	return implode('', $meta);
}
// --------------------------------------------------------------------
/**
 * title - creates a html document title
 *
 * @param string - if empty fetches default title
 * @return string $title
 */
function title($title = NULL, $iphone_title = NULL)
{
	$CI =& get_instance();
	//
	if($title == null)
	{
		$title = $CI->config->item('title');
	}
	// title for ios saving app
	if( $iphone_title == NULL )
	{
		$iphone_title = $title;
	}
	//
	return "\t".'<title>'.$title.' '.$CI->config->item('delimiter').' '.$CI->config->item('site-name').'</title>'.
	"\n\t".'<meta name="apple-mobile-web-app-title" content="'.$iphone_title.'" />';
}
// --------------------------------------------------------------------
/**
 * logo - creates an h4 tag with an image and a link tag
 *
 * @param	array
 * @return	string
 */
function logo($opt = array(NULL)) 
{
	$CI =& get_instance();
	// merge options
	$opt = array_merge(array(
			'file' 	=> $CI->config->item('logo_img'),
			'url' 	=> $CI->config->item('logo_url'),
			'alt' 	=> $CI->config->item('logo_title'),
			'id' 	=> 'logo'
		), $opt);
	// check for correct url for logo
	if( substr($opt['file'], 0, 5) != 'http:' && substr($opt['file'], 0, 4) != 'www.' )
	{
		$opt['file'] = base_url().$opt['file'];
	}
	// check for correct url 
	if( isset($opt['url']) )
	{
		if( substr($opt['url'], 0, 5) != 'http:' && substr($opt['url'], 0, 4) != 'www.' )
		{
			$opt['url'] = base_url().$opt['url'];
		}
		// return with link
		return '<h2 id="'.$opt['id'].'"><a class="logo-link" href="'.$opt['url'].'"><img class="logo-img" src="'.$opt['file'].'" alt="'.$opt['alt'].'" /></a></h2>';
	}
	// return without link
	return '<h2 id="'.$opt['id'].'"><img class="logo-img" src="'.$opt['file'].'" alt="'.$opt['alt'].'" /></h2>';
}
// ------------------------------------------------------------------------

/**
 * copyright - produces a copyright notice
 *
 * @param array
 * @return string copyright
 */

function copyright($opt = array(NULL)) 
{	
	$CI =& get_instance();
	//	
	$opt = array_merge(
		array(
			'year' 			=> $CI->config->item('copyright-year') != null ? $CI->config->item('copyright-year') : date('Y'),
			'form_year' 	=> $CI->config->item('copyright-from'),
			'url' 			=> $CI->config->item('copyright-url'),
			'copyright' 	=> $CI->config->item('copyright'),
			'by' 			=> $CI->config->item('copyright-by')
		), $opt);
	// prepare year
	if( $opt['from_year'] != false )
	{
		$opt['year'] = $opt['from_year'].'–'.$opt['year'];
	}
	//
	if( $opt['url'] != false )
	{
		if( substr($opt['url'], 0, 6) == "https:" || substr($opt['url'], 0, 5) == "http:" || substr($opt['url'], 0, 4) == "www." )
		{
			$url = $opt['url'];
		}
		else
		{
			$url = base_url().$opt['url'];			
		}
		// return copyright with link
		return '<div id="copyright"><a href="'.$url.'">'.$opt['copyright'].' '.$opt['year'].' '.$opt['by'].'</a></div>';	
	}
	// return copyright wothout link
	return '<div id="copyright">'.$opt['copyright'].' '.$opt['year'].' '.$opt['by'].'</div>';
}


/* End of file metadata_helper.php */
/* Location: ./system/helpers/metadata_helper.php */