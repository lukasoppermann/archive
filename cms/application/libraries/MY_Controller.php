<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class MY_Controller extends Controller {

	var $CI 	= null;
	var $data	= null;
	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
		// Define Variables
		$this->CI 		=& get_instance();
		// load Stylesheets
		$this->CI->load->library('stylesheet');
		$this->CI->load->library('javascript');	
		
		$this->CI->stylesheet->add('base-0.0.1');
		// Initialize Menus
		$this->CI->load->library('navigation');
		$this->CI->navigation->do_active(array('klima', 'spa', 'support', 'ueber-uns', 'kontakt', 'hevac', 'about-us', 'contact'));
			// Main Menu
			$this->data['main_menu'] 		= $this->CI->navigation->tree(array('menu' => 'main', 'lvl' =>'2', 'list_id' => 'main_menu'));
			$this->data['meta_menu'] 		= $this->languages->switcher(array('current_first' => true));
			$this->data['sub_menu'] 		= $this->CI->navigation->tree(array('menu' => 'main', 'start_lvl' => '2', 'lvl' => '2', 'list_id' => 'sub_menu', 'show_empty' => false));
			$this->data['support'] 			= $this->CI->navigation->tree(array('menu' => 'meta', 'lvl' =>'1', 'list_id' => 'support_menu'));
			$this->data['footer'] 			= $this->CI->navigation->tree(array('menu' => 'footer', 'lvl' =>'1', 'list_id' => 'footer_menu'));				
			$this->data['breadcrumbs'] 		= $this->CI->navigation->path(array('path_class' => 'left', 'path_before' => '<a href="'.lang_url().'">'.$this->data["page_name"].'</a>'));

			$this->data['lang'] 			= $this->languages->current('abbr');

			// if($this->data['lang'] == 'de')
			// {
			// 	$this->lang->load('static', 'german');
			// }
			// else
			// {
			// 	$this->lang->load('static', 'english');			
			// }

			$path							= $this->CI->navigation->active('path');
			$this->data['spa']				= ($path[0] == '/spa') ? 'spa' : '';

			$this->data['head_img']			= 'default.jpg';
			$this->data['order_email']		= 'oppermann.lukas@googlemail.com';
		}
		// ------------------------------------------------------------------------
		// Function: _link	
		function _link($string)
		{
			// check if http:// or www. is in path
			if(preg_match("[http://|http:|www.|ww.|.*\.]", $string))
			{
				return $string;		
			}
			else
			{
				// if not add treat as relative path
				$languages = $this->languages->get('abbr');
				$tmp_lang = explode('/',$string);
				// check for language in url
				if(!isset($tmp_lang[1]) || (!in_array($tmp_lang[1], $languages) && !in_array('/'.$tmp_lang[1], $languages )))
				{		
					// no language -> add it
					return lang_url().'/'.ltrim($string,'/');
				}
				else
				{
					// language in path -> add base_url only
					return base_url().substr($string, 1);
				}
			}
		}
		// ------------------------------------------------------------------------
		// Function: _get_entries
		function _get_entries($type = null, $limit = null, $id = null, $json = FALSE, $sort = FALSE)
		{
			$this->CI->db->select('id, menu_id, type, title, content, excerpt, date, data');
			//
			if($id != null)
			{
				$this->CI->db->where('id', $id);
			}
			if($type != null)
			{
				$this->CI->db->where('type', $type);
			}

			$this->CI->db->where('status', '1');
			$this->CI->db->where('language', $this->CI->config->item('lang_id'));
			$this->CI->db->order_by('date', 'desc');

			if($limit != null)
			{
				$query = $this->CI->db->get($this->config->item('prefix').$this->config->item('db_entries'), $limit);		
			}
			else
			{
				$query = $this->CI->db->get($this->config->item('prefix').$this->config->item('db_entries'));
			}
			foreach($query->result() as $row)
			{
				$data[$row->id] = array(
					'id' => $row->id,
					'menu_id' => $row->menu_id,
					'type' => $row->type,
					'title' => $row->title,
					'content' => $row->content,
					'excerpt' => $row->excerpt,
					'date' => $row->date,
					'data' => $row->data
				);
				if($json == TRUE || $sort == TRUE)
				{
					$data[$row->id]['data'] = json_decode($row->data, TRUE);
					if($sort == TRUE)
					{
						$sorter[$row->id] = $data[$row->id]['data']['pos'];	
					}
				}		
			}
			if(isset($data) && $sort == TRUE)
			{
				asort($sorter);
				$i = 0;
				foreach($sorter as $id => $val)
				{
					$i++;
					$return[$i] = $data[$id];
					$return['ids'][$id] = $i;
				}
				return $return;
			}
			elseif(isset($data))
			{
				return $data;
			}
			if(isset($id))
			{
				return $data[$id];
			}
			else
			{
				return FALSE;
			}
		}
		// ------------------------------------------------------------------------
		// Function: _get_boxes
		function _get_boxes($values)
		{
			$_data['content'] 	= $values['content'];
			$_data['title']		= $values['title'];
			if(isset($values['data']))
			{
				if(isset($values['data']['links']))
				{
					if(isset($values['data']['links']['active']) && $values['data']['links']['active'] == "Aktivieren")
					{
						$_data['links'] = null;
						foreach($values['data']['links']['title'] as $id => $link_title)
						{
							$url = $this->_link($values['data']['links']['url'][$id]);
							$_data['links'] .= '<li><a href="'.$url.'" class="link">'.$link_title."</a></li>\n";
						}
						$box_title = isset($values['data']['links']['box-title']) ? "<h4>".$values['data']['links']['box-title']."</h4>" : '';
						$_data['links'] = "<div class='media-box links'>\n".$box_title."\n<ul class='link-list'>\n".$_data['links']."</ul></div>\n";
					}
				}
				// build text box
				if(isset($values['data']['text']))
				{
					if(isset($values['data']['text']['active']) && $values['data']['text']['active'] == "Aktivieren")
					{
						$_data['textbox'] = null;
						foreach($values['data']['text']['title'] as $id => $text_title)
						{
							$url = $this->_link($values['data']['text']['link'][$id]);
							$url = !empty($values['data']['text']['link_name'][$id]) ? "<a href='".$url."' class='link'>".$values['data']['text']['link_name'][$id]."</a>" : '';
							$_data['textbox'] .= "<div class='media-box text-box'>\n<h4>".$values['data']['text']['title'][$id]."</h4>\n<p>".$values['data']['text']['text'][$id]."</p>\n".$url."</div>\n";
						}
					}
				}
				return $_data;
			}
		}
}
	
/* End of file MY_Controller.php */
/* Location: ./application/web_site_name/libraries/MY_Controller.php */