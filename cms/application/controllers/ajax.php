<?php 

if (! defined('BASEPATH')) exit('No direct script access');

class ajax extends Controller {

	var $update 	= NULL;
	var $global 	= array(NULL);
	var $type 		= array('1');

	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
		$this->setup();
	}
	
	//php 4 constructor
	function ajax() 
	{
		parent::Controller();
		$this->setup();
	}
	
	// setup 
	function setup()
	{
		/*----------------------------------------------------------------------*/
		// load client config
		$this->config->load($this->config->item('client_config_database'), '','',$this->config->item('client_prefix'));
		/*----------------------------------------------------------------------*/
		// get all global post values
		$this->global['language']	= $this->input->post('language');
		$this->global['menu']		= $this->input->post('menu');
		/*----------------------------------------------------------------------*/				
		// retrieve from database
		$this->db->select('id, label, path, parent_id, type, position, status, title');
		$this->db->where('language', $this->global['language']);				
		$this->db->order_by('position', 'asc'); 
		$this->db->from($this->config->item('client_prefix').$this->config->item('db_menu'));

		$query = $this->db->get();
		
		foreach ($query->result() as $row)
		{
			$array = array(
				'id' 		=> $row->id,
				'path' 		=> $row->path,
				'type' 		=> $row->type,
				'parent_id' => $row->parent_id,
				'position' 	=> $row->position,
				'label' 	=> $row->label,
				'status' 	=> $row->status,
				'title' 	=> $row->title
			);
			// -----------------
			// indexed by parent id and id
			$this->global['by_parent_id'][$row->parent_id][$row->id] = &$array;
			// -----------------
			// indexed by id only
			$this->global['by_id'][$row->id] = &$array;
			// -----------------
			// unset array
			unset($array);
		}
	}
	// --------------------------------------------------------------------
	/**
	 * update
	 *
	 * @description	function to update menu when moved
	 */	
	function update()
	{
		// -----------------
		// define variables
		$items = $this->input->post('items');
		// -----------------
		if( is_array($items) )
		{
			$items = group_array($items, 'parent_id');
			// -----------------
			foreach($items as $parent_id => $array)
			{
				$i = 1;
				foreach($array as $key => $item)
				{
					$this->global['by_id'][$item['id']]['parent_id'] 	= $parent_id;
					$this->global['by_id'][$item['id']]['position'] 	= $i;
					$this->global['by_id'][$item['id']]['menu'] 		= $this->global['menu'];
					// -----------------
					$data[$item['id']]['parent_id'] = $parent_id;
					$data[$item['id']]['position'] 	= $i;
					$data[$item['id']]['menu'] 		= $this->global['menu'];
					// -----------------
					++$i;
				}
				//
			}
			// -----------------
			foreach( $data as $id => $item )
			{
				$item['path'] = $this->update_path($this->global['by_id'][$id]);
				// -----------------
				$this->db->where('id', $id);
				$this->db->update($this->config->item('client_prefix').$this->config->item('db_menu'), $item);
				// -----------------
				// check for errors
				if( $this->db->affected_rows() == -1 )
				{
					$error = TRUE;
				}
			}
		}
		if(empty($error) || $error !== TRUE)
		{
			echo "<div class='notice success'><p>".lang('changes_saved')."</p></div>";
		}
		else
		{
			echo "<div class='notice error'><p>Database not updated</p></div>";			
		}
	}
	// --------------------------------------------------------------------
	/**
	 * delete
	 *
	 * @description	function to delete menu items
	 */
	function delete()
	{
		// -----------------
		// define variables
		// id
		$id = &$this->input->post('id');
		// get path
		$path = &$this->global['by_id'][$id]['path'];
		// only delete last part of path (e.g. /xyz/xyz/last-part -> /last-part)
		$path = substr($path, strripos($path, "/"), strlen($path)-strripos($path, "/"));
		// position
		$position = 0;
		// types to not update
		$type = array('1');
		// children
		$children =& $this->global['by_parent_id'][$id];
		// siblings
		$siblings = $this->global['by_parent_id'][$this->global['by_id'][$id]['parent_id']];
		unset($siblings[$id]);
		// -----------------
		// update siblings
		if( is_array($siblings) )
		{
			foreach($siblings as $key => $sibling)
			{
				// increase position
				++$position;
				// run update
				$this->db->where('id', $key);				
				$this->db->update( $this->config->item('client_prefix').$this->config->item('db_menu'), array('position' => $position) );
			}
		}
		// -----------------
		// update children		
		if( is_array($children) )
		{
			foreach($children as $key => $child)
			{
				// increase position
				++$position;
				// create update array
				( !in_array($child['type'], $this->type) ) ? $array['path'] = str_replace($path, '', $child['path']) : '';
				$array['parent_id'] = $this->global['by_id'][$id]['parent_id'];
				$array['position'] = $position;												
				// run update				
				$this->db->where('id', $key);	
				$this->db->update( $this->config->item('client_prefix').$this->config->item('db_menu'), $array );
				// update children if any
				$this->update_children($key, $path);
			}
		}
		// -----------------
		// delete menu item from db
		$this->db->where('id', $id);
		$this->db->delete($this->config->item('client_prefix').$this->config->item('db_menu'));
		// -----------------
		// remove menu id from entry		
		$this->db->where('menu_id',$id);
		$this->db->update($this->config->item('client_prefix').$this->config->item('db_entries'), array('menu_id' => '0'));
		// -----------------
		// db maintainance
		$this->db->query('ALTER TABLE '.$this->config->item('client_prefix').$this->config->item('db_menu').' AUTO_INCREMENT = 1');
	}
	// --------------------------------------------------------------------
	/**
	 * form
	 *
	 * @description	load and return form
	 */
	function form($form_type)
	{
		foreach($_POST as $key => $value)
		{
			$post[$key] = $this->input->post($key);
		}
		
		$data['config'] = $form_type;
		
		// needs to be modularized
		$data['opt']['class']	= 'rounded js_form';
		$data['opt']['value'] 	= array('id' => $post['id'], 'label' => $this->global['by_id'][$post['id']]['label'], 'path' => $this->global['by_id'][$post['id']]['path'],
		'title' => $this->global['by_id'][$post['id']]['title'], 'type' => $this->global['by_id'][$post['id']]['type']); 
		// !!!!!!!!!!!!!!!!!!!!!!
		
		$html = form($data);
		
		// needs to be modularized
		$path = $this->global['by_id'][$post['id']]['path'];
		echo json_encode(array('html' => $html, 'title' => lang($post['headline']), 'path' =>substr($path, 0, strripos($path, "/")) ));
		// !!!!!!!!!!!!!!!!!!!!!!
	}
	// --------------------------------------------------------------------
	/**
	 * template [outdated, needs to be removed] !!!!!
	 *
	 * @description	load and return template
	 */
	function template($template)
	{
		// -----------------
		// define variables
		// split template into dir and file
		$template = explode(':',$template);
		// get parent_id
		$parent_id = $this->input->post('value');
		empty($parent_id) ? $parent_id = 0 : '';
		$type = 0;
		//
		$data['opt']['value'] 	= array('parent_id' => $parent_id, 'type' => $type);
		$data['opt']['label'] 	= array('type' => 'edit');		
		$data['opt']['class']	= 'rounded js_form';
		// -----------------
		// load and echo template
		if( $template[0] == 'forms' || $template[0] == 'form' )
		{
			$data['config'] = 'form_menu';
			$html = form($data);

			if(isset($this->global['by_id'][$parent_id]['path']))
			{
				$path = $this->global['by_id'][$parent_id]['path'];
			}
			else
			{
				$path = '';
			}
			// needs to be modularized
			echo json_encode(array('html' => $html, 'path' => $path));
			// !!!!!!!!!!!!!!!!!!!!!!
			
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Add item
	 *
	 * @description	add new item to db
	 */
	function add()
	{
		// -----------------
		// define variables		
		// values
		$values			 		= &$this->input->post('values');
		// position
		$position 				= &$this->input->post('position');
		// status
		$values['status'] 		= 0;
		// type
		$values['type'] 		= (!isset($values['type'])) ? 0 : $values['type'];
		// parent_id
		$values['parent_id']	= (!isset($values['parent_id'])) ? 0 : $values['parent_id'];
		// -----------------
		// validate	

		// $items['alias'] = isset($items['alias']) ? $items['alias'] : 0;
		$values['path'] = strtolower(replace_characters($values['path'], 'url'));
		if(substr($values['path'],0,1) != '/')
		{
			$values['path'] = '/'.$values['path'];
		}
		// //
		// $items['path'] = $this->update_path($items, $this->global['data']);
		// // add to db
		$data = array(
			'menu' 		=> $this->global['menu'],
			'language' 	=> $this->global['language'],
			'status' 	=> $values['status'],
			'type' 		=> $values['type'],
			'label' 	=> $values['label'],	
			'title' 	=> $values['title'],
			'position' 	=> $position,
			'parent_id' => $values['parent_id'],								
			'path' 		=> $values['path']
		);
		// 
		$this->db->insert($this->config->item('client_prefix').$this->config->item('db_menu'), $data);
		// -----------------
		// get id of insert
		$id = $this->db->insert_id();
		// -----------------
		// return success
		if( $this->db->affected_rows() == 1 )
		{
			// echo '{"parent_id":"'.$values['parent_id'].'","success":"true"}';
			echo json_encode(array('parent_id' => $values['parent_id'], 'success' => 'true', 'id' => $id));
		}
		else
		{
			return FALSE;
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Update item
	 *
	 * @description	update item from db
	 */
	function edit()
	{
		// -----------------
		// define variables		
		// values
		$values			 		= &$this->input->post('values');
		// position
		$position 				= &$this->input->post('position');
		// status
		$values['status'] 		= 0;
		// type
		$values['type'] 		= (!isset($values['type'])) ? 0 : $values['type'];
		// -----------------
		// validate	
		
		$values['path'] = strtolower(replace_characters($values['path'], 'url'));
		if(substr($values['path'],0,1) != '/')
		{
			$values['path'] = '/'.$values['path'];
		}
		// //
		// $items['path'] = $this->update_path($items, $this->global['data']);
		// // add to db
		//
		$this->db->select('path');
		$this->db->where('id', $values['id']);				
		$this->db->from($this->config->item('client_prefix').$this->config->item('db_menu'));

		$query = $this->db->get();	
		foreach ($query->result() as $row)
		{
			$old_path = $row->path;
		}

		$data = array(
			'type' 		=> $values['type'],
			'label' 	=> $values['label'],	
			'title' 	=> $values['title'],
			'path' 		=> $values['path']
		);
		
		$this->db->where('id', $values['id']);
		$this->db->update($this->config->item('client_prefix').$this->config->item('db_menu'), $data);
		
		// -----------------
		// return success
		if( $this->db->affected_rows() == 1 )
		{
			// update children
			$this->update_children($values['id'], $old_path, $values['path']);
			//
			echo json_encode(array('id' => $values['id'], 'label' => $values['label']));
		}
		else
		{
			return FALSE;
		}
	}
	// ############################################################################################################################
	// functions
	// --------------------------------------------------------------------
	/**
	 * update_path
	 *
	 * @description	function to update menu path
	 */
	function update_path($array)
	{
		// -----------------
		if( in_array($array['type'], $this->type) )
		{
			// -----------------
			// return path because it is an alias or suchlike
			$path = $array['path'];
		}
		else
		{
			// -----------------
			// get only last part of path
			$path = substr($array['path'], strripos($array['path'], "/"));
			// if item has a parent
			if( $array['parent_id'] != 0 )
			{
				$path = $this->update_path($this->global['by_id'][$array['parent_id']]).$path;										
			}
		}
		// -----------------
		return $path;
	}
	// --------------------------------------------------------------------
	/**
	 * update_children
	 *
	 * @description	function to update menu item children
	 */
	function update_children($id, $path, $new_path = '')
	{
		if( isset($this->global['by_parent_id'][$id]) )
		{
			foreach( $this->global['by_parent_id'][$id] as $key => $array )
			{
				( !in_array($array['type'], $this->type) ) ? $array['path'] = str_replace($path, $new_path, $array['path']) : '';							
				// run update				
				$this->db->where('id', $key);	
				$this->db->update( $this->config->item('client_prefix').$this->config->item('db_menu'), $array );
				// update children if any
				$this->update_children($key, $path);
			}
		}
	}
}
/* End of file Ajax.php */
/* Location: ./application/web_site_name/controllers/Ajax.php */