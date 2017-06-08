<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cms Class
 *
 * @version		0.1
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Cms
 * @author		Lukas Oppermann - veare.net
 */
class CI_Cms {

    var $CI;
    var $nav_array;
	var $nav_string;
	var $active;
	var $current;
	var $variables;

    // class constructor
    public function __construct($params = array())
    {
        $this->CI =& get_instance();
		$this->init_nav();
    }
    // --------------------------------------------------------------------
    /**
    * nav
    *
    * create cms navigation
    *
    * @access	public
    * @param	string / array	 
    */
    function init_nav($show = null)
    {
       $this->nav_array = null;
		// Creating Nav array
	  // Dashboard ------------------------
        $this->nav_array[1] = array(
            'label'     => 'Home',
            'path'      => '/home',
            'rights'    => array('')
        );
        // Content ------------------------
				if( is_array($show) && ( in_array(2, $show) || in_array('instore', $show) ) )
				{
	        $this->nav_array[2] = array(
	            'label'     => 'instore',
	            'path'      => '/instore',
	            'rights'    => array('')
	        );
				}
        // Media ------------------------
        $this->nav_array[3] = array(
            'label'     => 'e-boutique',
            'path'      => '/eboutique',
            'rights'    => array('')
        );
        // Settings ------------------------
        $this->nav_array[4] = array(
            'label'     => 'in the media',
            'path'      => '/in-the-media',
            'rights'    => array('')
        );
	    // Sale ------------------------
		if( is_array($show) && in_array(5, $show) )
		{
			$this->nav_array[5] = array(
				'label'     => 'sale',
				'path'      => '/sale',
				'rights'    => array(''),
				'id' 		=> 'nav_sale' 
			);
		}
        /* ---------------------------------------------- */
        // building cms nav
        $nav = "<ul id='nav_ul'>";
        foreach($this->nav_array as $sort => $nav_item)
        {
			// set active null
			$active = null;
			// get user group
			$user_group = '';
			// get active url arrys
			$current_path = current_path();
			// build nav 
			if(in_array($user_group, $nav_item['rights']) || ($user_group != null && in_array("*" ,$nav_item['rights'])))
			{
				// set active elements
				if( preg_match('&^'.$nav_item["path"].'&', '/'.$current_path) )
				{
					$this->active[] = $nav_item;
					$active = ' active';
				}
				//
	            $nav .= '<li class="item'.$active.'" '.(isset($nav_item['id']) ? 'id="'.$nav_item['id'].'"':'').'><a href="'.base_url(FALSE).$nav_item['path'].'"><span>'.$nav_item['label'].'</span></a>';
	            // if has child
	            if(isset($nav_item['sub']) && is_array($nav_item['sub']))
	            {
	                $nav .= '<ul class="sub-nav">';
	                // loop through nav sub
	                foreach($nav_item['sub'] as $sub_sort => $sub_item)
	                {
						$active = null;
						if(in_array($user_group ,$sub_item['rights']) || ($user_group != null && in_array("*" ,$sub_item['rights'])))
						{
							// set active elements
							if( preg_match('&^'.$sub_item['path'].'&', '/'.$current_path))
							{
								$this->active[] = $sub_item;
								$active = ' active';
							}
							// add nav item
	                    	$nav .= '<li class="sub_item'.$active.'"><span><a href="'.base_url(FALSE).$sub_item['path'].'">' 	
									.$sub_item['label'].'</a></span></li>';
						}
	                }
	                $nav .= '</ul>';
	            }
	            // close Nav Element
	            $nav .= '</li>';
            }
        }
        $nav .= "</ul>";
		// set current
		if(isset($this->active[count($this->active)-1]))
		{
			$this->current = $this->active[count($this->active)-1];
		}
		else
		{
			$this->current['path'] = current_path();
			$this->current['rights'] = array("*");
		}
        // return final nav
        $this->nav_string = $nav;
    }
    // --------------------------------------------------------------------
    /**
    * nav
    *
    * return nav
    *
    * @access	public
    * @param	string / array	 
    */
	public function nav()
	{
		return $this->nav_string;
	}
    // --------------------------------------------------------------------
    /**
    * active
    *
    * return the active menu items
    *
    * @access	public
    * @param	string / array	 
    */
	function active()
	{
		return $this->active;	
	}
	// --------------------------------------------------------------------
	/**
	* current
	*
	* return the current menu item
	*
	* @access	public
	* @param	string / array	 
	*/
	function current($element = 'path')
	{
		if(in_array($element, $this->current))
		{
			return $this->current[$element];
		}
		elseif($element == 'array')
		{
			return $this->current;
		}
		else
		{
			return $this->current['path'];
		}	
	}
    // --------------------------------------------------------------------
    /**
    * get_group
    *
    * gets the groups for current nav
    *
    * @access	public
    * @param	string / array	 
    */
    function get_groups()
    {
        //
        return $this->current('right');
    }
// close class
}
