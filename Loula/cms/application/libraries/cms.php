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
    function init_nav()
    {
        // Creating Nav array
        // Dashboard ------------------------
        $this->nav_array[1] = array(
            'label'     => 'Dashboard',
            'path'      => '/dashboard',
            'rights'    => array('*')
        );
        // Content ------------------------
        $this->nav_array[2] = array(
            'label'     => 'Content',
            'path'      => '/content/list',
            'rights'    => array('*')
        );
 		   // Page Settings
		    $this->nav_array[2]['sub'][] = array(
		        'label'     => 'New Article',
		        'path'      => '/content/edit/new',
		        'rights'    => array('1','2')
		    );
		    // Personal Settings
		    $this->nav_array[2]['sub'][] = array(
		        'label'     => 'Edit Article',
		        'path'      => '/content/list',
		        'rights'    => array('1','2')
		    );
        // Media ------------------------
        $this->nav_array[3] = array(
            'label'     => 'Media',
            'path'      => '/multimedia',
            'rights'    => array('*')
        );
        // Settings ------------------------
        $this->nav_array[4] = array(
            'label'     => 'Settings',
            'path'      => '/settings',
            'type'      => 2,
            'rights'    => array('*')
        );
            // Page Settings
            $this->nav_array[4]['sub'][] = array(
                'label'     => 'Page settings',
                'path'      => '/settings/page',
                'rights'    => array('1','2')
            );
            // Personal Settings
            $this->nav_array[4]['sub'][] = array(
                'label'     => 'Personal settings',
                'path'      => '/settings/personal',
                'rights'    => array('1','2')
            );
	        // Users Settings
	        $this->nav_array[4]['sub'][] = array(
	            'label'     => 'Users',
	            'path'      => '/settings/user',
	            'rights'    => array('1')
	        );
        /* ---------------------------------------------- */
        // building cms nav
        $nav = "<ul id='cms_nav'><li id='steeleworks'><a href='".base_url()."dashboard'>SW</a></li>";
        foreach($this->nav_array as $sort => $nav_item)
        {
			// set active null
			$active = null;
			// get user group
			$user_group = user('group');
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
	            $nav .= '<li class="item'.$active.'"><span><a href="'.base_url(FALSE).$nav_item['path'].'">'.$nav_item['label'].'</a></span>';
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
