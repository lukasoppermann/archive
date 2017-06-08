<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Alternative languages helper
*
* Returns a string with links to the content in alternative languages
*
* version 0.2
* @author Luis <luis@piezas.org.es>
* @modified by Ionut <contact@quasiperfect.eu>
*/
// ------------------------------------------------------------------------

/**
 * lang_url
 *
 * Returns the "base_url" item from your config file with the language abbr
 *
 * @access	public
 * @return	string
 */
function lang_url()
{
	$CI =& get_instance();
	return $CI->config->slash_item('base_url').$CI->config->item('lang_abbr');
}
// ------------------------------------------------------------------------
function alt_site_url($uri = '', $ul = true)
{
    $CI =& get_instance();
    $actual_lang=$CI->uri->segment(1);
    $languages=$CI->config->item('lang_desc');
    $languages_useimg=$CI->config->item('lang_useimg');
    $ignore_lang=$CI->config->item('lang_ignore');
    if (empty($actual_lang))
    {
        $uri=$ignore_lang.$CI->uri->uri_string();
        $actual_lang=$ignore_lang;
    }
    else
    {
        if (!array_key_exists($actual_lang,$languages))
        {
            $uri=$ignore_lang.$CI->uri->uri_string();
            $actual_lang=$ignore_lang;
        }
        else
        {
            $uri=$CI->uri->uri_string();
            // $uri=substr_replace($uri,'',0,1);
        }
    }
    $alt_url= ($ul == true ? '<ul>' : '');
    //i use ul because for me formating a list from css is easy
    foreach ($languages as $lang=>$lang_desc)
    {
         if ($actual_lang!=$lang)
         {
            $alt_url.='<li'.(url_get_lang(current_url()) == $lang ? ' class="active"' : '').'><a href="'.base_url();
            if ($lang==$ignore_lang)
            {
                $new_uri=ereg_replace('^'.$actual_lang,'',$uri);
                $new_uri=substr_replace($new_uri,'',0,1);
            }
            else
            {
                $new_uri=ereg_replace('^'.$actual_lang,$lang,$uri);
            }
            $alt_url.=$new_uri.'">';
            if ($languages_useimg){
                //change the path on u'r needs
                //in images u need to have for example en.gif and so on for every   
                //language u use
                //the language description will be used as alternative
                $alt_url.= '<img src="'.base_url().'images/'.$lang.'.gif" alt="'.$lang_desc.'"></a></li>'."\n";
            }
            else
            {
                $alt_url.= $lang_desc.'</a></li>'."\n";
            }
         }
    }
    $alt_url.= ($ul == true ? '</ul>' : '');
    return $alt_url;
}
// ------------------------------------------------------------------------

/**
 * get language
 *
 * Returns the "base_url" item from your config file with the language part
 *
 * @access	public
 * @return	string
 */
function url_get_lang()
{
	$url = current_url();
	return substr(str_replace(base_url(),'',$url), 0, strpos(str_replace(base_url(),'',$url),'/'));
}
// ------------------------------------------------------------------------

/**
 * Base URL Language
 *
 * Returns the "base_url" item from your config file with the language part
 *
 * @access	public
 * @return	string
 */
function base_url_lang($slash = NULL)
{
	$CI =& get_instance();
	
	if($slash == true)
	{
		$base_url = $CI->config->slash_item('base_url').$CI->languages->current('abbr').'/';
	}
	else
	{
		$base_url = $CI->config->slash_item('base_url').$CI->languages->current('abbr');
	}
	return $base_url;
}
// ------------------------------------------------------------------------

/**
 * current URL without ignored items
 *
 * Returns the "current_url" without the ignored terms e.g. "do_login" 
 *
 * @access	public
 * @return	string
 */
function current_url_noignored()
{
	// $CI =& get_instance();
	// // $ignore = $CI->config->item('ignore_url');
	// $elements = explode('/',uri_string());
	// $url = NULL;
	// 
	// foreach($elements as $element)
	// {
	// 	if( trim($element)!=NULL)
	// 	{
	// 		$url .= '/'.$element;
	// 	}
	// }
	
	return current_url();
}
// ------------------------------------------------------------------------

/**
 * Active URL Part
 *
 * Returns the active part of the url 
 *
 * @access	public
 * @return	string
 */
//function: 
function get_active_site($var = false)
{
	$CI =& get_instance();
	if(uri_string() != NULL && uri_string() != '/'.$CI->config->item('language_abbr'))
	{
		$string = uri_string();
	}
	else
	{
		$string = $CI->config->item('index_menu');
	}
	
	if($var == 'true')
	{
		return strrchr(substr($string,0),'/');
	}
	else
	{
		$active = explode('/',substr($string,1));
		$active['current'] = strrchr(substr($string,0),'/');			
		return $active;
	}
}

// ------------------------------------------------------------------------

/**
 * Get the id of all active menu elements
 *
 * Some of the functions use this
 *
 * @access	public
 * @param	string
 * @param	string
 * @return	string
 */
function get_menu_active($string = NULL, $get = NULL)
{
	$CI =& get_instance();
	
	$elements = _clear_array(explode('/',uri_string( current_url() ) ) );
	$lang = url_get_lang(current_url());

	if(empty($elements) OR $elements[0] == '/'.$CI->config->item('language_abbr'))
	{
		$elements[0] = $CI->config->item('index_menu');
	}
	$CI->db->select('id, path, parent_id');
	$where = "path = '".implode('\' OR path = \'',$elements)."'";
	$CI->db->where($where);
	$CI->db->like('lang', $lang);
	$CI->db->from('menu');
	$query = $CI->db->get();		

	foreach ($query->result() as $row)
	{
		$output[$row->path]['parent_id'] = $row->parent_id;
		$output[$row->path]['id'] = $row->id;
		$output[$row->path]['level'] = array_search($row->path,$elements);
		$output[$row->path]['path'] = $row->path;
	}
	
	if(!empty($output))
	{
		if($string == NULL)
		{
			return $output;
		}
		else
		{
			if($get == 'parent_id' )
			{
				return $output[$string]['parent_id'];	
			}
			else
			{
				return $output[$string]['id'];	
			}
		}
	}
	else
	{
		return FALSE;
	}
}

function _clear_array( $array )
{
	$output = NULL;
	foreach($array as $key => $value)
	{
		if(trim($value) != NULL)
		{
			$output[] = "/".$value;
		}
	}
	return $output;
}

function strip_http( $url )
{
	$strip_array = array('http://','http://www.','www.');
	
	foreach($strip_array as $item)
	{
		$url = str_replace($item,'',$url);
	}
	return $url;
}


/* End of file MY_url_helper.php */
/* Location: ./system/application/libraries/MY_url_helper.php */