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
 * doctype - creates links to favorite icons
 *
 * @param string $version - html version of document
 * @return string doctype
 */
function doctype($type = 'html5')
{
	global $_doctypes;

	if ( ! is_array($_doctypes))
	{
		if ( ! require_once(APPPATH.'config/doctypes.php'))
		{
			return FALSE;
		}
	}

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
 * favicon - creates links to favorite icons
 *
 * @param string $icon - link to normal favicon 16x16 ico 8bit
 * @param string $iphone - link to iphone/ipad icon 57x57 PNG
 * @return string $favicon
 */
function favicon($icon = NULL, $iphone = NULL) 
{	
	$favicon = NULL;		
	if( !empty($icon))
	{
		$favicon .= "\t".'<link rel="shortcut icon" href="'.base_url().$icon.'" type="image/x-icon" />'."\n";
		$favicon .= "\t".'<link rel="icon" href="'.base_url().$icon.'" type="image/vnd.microsoft.icon" />'."\n";
	}
	if( !empty($iphone))
	{
		$favicon .= "\t".'<link rel="apple-touch-icon" href="'.base_url().$iphone.'" />'."\n";
	}
	return $favicon;		
}
// --------------------------------------------------------------------
/**
 * meta - creates meta tags
 *
 * @param	array
 * @return	string
 */
function meta($opt = array(NULL)) 
{	
	$CI 		=& get_instance();
	$settings 	= $CI->config->item('settings');
	//	
	$opt = array_merge(
		array(
			'charset' 		=> $settings['charset'],
			'language' 		=> $CI->config->item('lang_abbr'),
			'author' 		=> $settings['site-author'],
			'developer' 	=> $settings['developer'],
			'generator'  	=> $settings['generator'],
			'copyright' 	=> (isset($settings['copyright-by']) ? date('Y').' '.$settings['copyright-by'] : ''),
			'keywords' 		=> $settings['keywords'],
			'description' 	=> $settings['description'],
			'robots'		=> $settings['robots']
		), 
		$opt);

		$meta = NULL;

		foreach($opt as $key => $value)
		{
			if($key == 'charset' && !empty($value))
			{
				$meta .= "\t".'<meta http-equiv="content-type" content="text/html; charset='.$value.'" />'."\n";
			}
			elseif( !empty($value))
			{
				$meta .= "\t".'<meta name="'.$key.'" content="'.$value.'" />'."\n";
			}
		}
	return $meta;		
}
// --------------------------------------------------------------------
/**
 * title - creates a html document title
 *
 * @param string $title - if empty fetches default title
 * @return string $title
 */
function title($title = NULL)
{
	$CI =& get_instance();
	$settings = $CI->config->item('settings');
	//
	if( !empty($title))
	{
		$title .= ' '.$settings['delimiter'].' '.$settings['site-name'];
	}
	elseif(!isset($settings['title']))
	{
		$title = $settings['title'].' '.$settings['delimiter'].' '.$settings['site-name'];
	}
	else
	{
		$title = $settings['slogan'].' '.$settings['delimiter'].' '.$settings['site-name'];
	}
	//
	return "\t".'<title>'.$title.'</title>';
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
	//
	$settings = $CI->config->item('logo');	
	$opt = array_merge(
		array(
			'file' 	=> variable($settings['file']),
			'url' 	=> variable($settings['logo_url']),
			'alt' 	=> variable($settings['logo_alt'])
		), 
		$opt);
	// check for correct url for logo
	$opt['file'] = preg_match("[http://|http:|www.|ww.]", $opt['file']) ? $opt['file'] : base_url().$opt['file'];
	
	if(isset($opt['url']) && !preg_match("[http://|http:|www.|ww.|.*\.]", $opt['url']) )
	{
		return '<h4 id="logo"><a href="'.lang_url(TRUE).$opt['url'].'"><img id="logo_img" src="'.$opt['file'].'" alt="'.$opt['alt'].'" /></a></h4>';
	}
	elseif( isset($opt['url']) )
	{
		return '<h4 id="logo"><a href="'.$opt['url'].'"><img id="logo_img" src="'.$opt['file'].'" alt="'.$opt['alt'].'" /></a></h4>';		
	}
	else
	{
		return '<h4 id="logo"><img id="logo_img" src="'.$opt['file'].'" alt="'.$opt['alt'].'" /></h4>';
	}
	
}
// --------------------------------------------------------------------
/**
 * slogan - creates an h4 tag with a link
 *
 * @param	array
 * @return	string
 */
function slogan($opt = array(NULL))
{
	$CI =& get_instance();
	$settings = $CI->config->item('settings');
	//	
	$opt = array_merge(
		array(
			'slogan'	=> $settings['slogan'],
			'url' 		=> $settings['logo_url']
		), 
		$opt);	
	
	if(isset($opt['url']) && !preg_match("[http://|http:|www.|ww.|.*\.]", $opt['url']) )
	{
		return '<h4 id="slogan"><a href="'.lang_url().'/'.$opt['url'].'">'.$opt['slogan'].'</a></h4>';
	}
	elseif( isset($opt['url']) )
	{
		return '<h4 id="slogan"><a href="'.$opt['url'].'">'.$opt['slogan'].'</a></h4>';		
	}
	else
	{
		return '<h4 id="slogan">'.$opt['slogan'].'</h4>';
	}	
}
// ------------------------------------------------------------------------

/**
 * copyright - produces a copyright notice
 *
 * @param string $name
 * @param string $year (default only current year, if $year -> '$year - current_year')
 * @param string $before (default = 'Copyright &copy; ')
 * @param string $after (default = ' - All Rights Reserved.')
 * @return string copyright
 */

function copyright($opt = array(NULL)) 
{	
	$CI 		=& get_instance();
	$settings 	= $CI->config->item('settings');
	//	
	$opt = array_merge(
		array(
			'year' 			=> date('Y'),
			'url' 			=> $settings['copyright-url'],
			'copyright' 	=> $settings['copyright'],
			'by' 			=> $settings['copyright-by']
		), 
		$opt);
	//
	if(!preg_match("[http://|http:|www.|ww.|.*\.]", $opt['url']))
	{
		if($opt['url'] == false)
		{
			return '<div id="copyright">'.$opt['copyright'].' '.$opt['year'].' '.$opt['by'].'</div>';
		}
		else
		{
			return '<div id="copyright"><a href="'.lang_url().'/'.$opt['url'].'">'.$opt['copyright'].' '.$opt['year'].' '.$opt['by'].'</a></div>';
		}
	}
	else
	{
		return '<div id="copyright"><a href="'.$opt['url'].'">'.$opt['copyright'].' '.$opt['year'].' '.$opt['by'].'</a></div>';
	}
	
}


/* End of file metadata_helper.php */
/* Location: ./system/helpers/metadata_helper.php */