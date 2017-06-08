<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter MY_date Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/content
 */
/**
 * Tags - explodes tag string and displays tags
 *
 * @access	public
 * @param	integer
 * @param	string
 * @return	int / float
 */
/**
* TODO: 
* - limit (show depending on ranking)
* - add link with dynamic link
* - add params ?
* - build tag system into db_data	
*
*/
function show_tags( &$string = null, $limit = 5, $link = false, $after = '' )
{
	// check if tags are set
	if( trim($string) != null )
	{
		// explode string
		$tags = explode(',',$string);
		// remove empty elements
		$tags = array_values(array_filter($tags));
		// check if limit is set, else set limit
		if( $limit == null || $limit == false )
		{
			$limit = count($tags);
		}
		// check if link is set
		if( $link == false )
		{
			// count tags
			$c = count($tags);
			// while limit is not reached && not more than existing tag
			for( $i = 0; $i < $limit && $i < $c; $i++ )
			{
				$tag = trim($tags[$i]);
				$output[] = '<span class="tag-'.$tag.'">'.$tag.'</span>'.$after;
			}
		}
		// return output
		return rtrim(implode('',$output),$after);
	}
}


/* End of file content_helper.php */
/* Location: ./application/helpers/content_helper.php */