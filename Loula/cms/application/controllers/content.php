<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content extends MY_Controller {
	
	
	public function index($method = null, $id = null)
	{
		if( $method == 'edit' )
		{
			$this->edit($id);
		}
		elseif( $method == 'list' )
		{
			$this->list_entries();
		}
		elseif( $method == 'grid' )
		{
			$this->grid_entries();
		}
	}
	// ---------------------------------------------------
	// List Entries
	public function list_entries()
	{
		$this->data['view'] = '<div id="view_menu"><span id="list" class="active">list</span><a id="grid" href="'.base_url().'content/grid">grid</a></div>';
		// load assets
		$this->load->model('content_model');
		//
		$this->data['title'] = '';
		$this->data['content'] = '<ul id="entries" class="filtered-list">';
		//
		$this->db->select('id, type, status, title, text, data');
		$this->db->from('client_entries');
		$query = $this->db->get();
		foreach ($query->result_array() as $row)
		{
			$entries[$row['id']] = $row;
			if( $entries[$row['id']]['title'] == null && $entries[$row['id']]['text'] == null)
			{
				$this->db->where('id', $row['id']);
				$this->db->delete('client_entries');
			}
			else
			{
				if( isset($entries[$row['id']]['data']) && variable($entries[$row['id']]['data']) != null)
				{
					$entries[$row['id']]['data'] = json_decode($entries[$row['id']]['data'], TRUE);
				}
				// build entry element
				$this->data['content'] .= $this->load->view('custom/entry_item', $entries[$row['id']], TRUE);
			}
		}
		// -----------------------
		$filters = index_array( $this->content_model->db_fetch('client_data',array('where' => array('key' => 'product')) ), 'type', TRUE);
		// -----------------------
		// get designers
		foreach( $filters['designer'] as $value )
		{
			$designers[$value['position']] = '<li data-value="'.$value['tag'].'" class="filter">'.$value['label'].'</li>';
		}
		// sort by position
		ksort($designers);
		// merge items
		$this->data['designers'] = implode('',$designers);
		// -----------------------
		// get product types
		foreach( $filters['type'] as $value )
		{
			$product_types[$value['position']] = '<li data-value="'.$value['tag'].'" class="filter">'.$value['label'].'</li>';
		}
		// sort by position
		ksort($product_types);
		// merge items
		$this->data['product_type'] = implode('',$product_types);
		// -----------------------
		$this->data['content'] .= '</ul>';
		// load entry form
        view('custom/entry_list', $this->data);
	}
	// ---------------------------------------------------
	// Entry grid
	public function grid_entries(  )
	{
		$this->data['view'] = '<div id="view_menu"><a id="list" href="'.base_url().'content/list">list</a><span id="grid" class="active">grid</span></div>';
		// load assets
		$this->load->model('content_model');
		//
		$this->data['title'] = '';
		$this->data['content'] = '<ul id="entries_grid" class="filtered-list">';
		//
		$this->db->select('id, type, status, title, text, data');
		$this->db->from('client_entries');
		$query = $this->db->get();
		// fetch images
		$this->db->select('id, data');
		$this->db->where('type','image');
		$images = $this->db->get('client_files')->result_array();
		foreach( $images as $img)
		{
			$_images[$img['id']] = json_decode($img['data'], TRUE);
		}
		// ---------------------
		foreach ($query->result_array() as $row)
		{
			$entries[$row['id']] = $row;
			if( $entries[$row['id']]['title'] == null && $entries[$row['id']]['text'] == null)
			{
				$this->db->where('id', $row['id']);
				$this->db->delete('client_entries');
			}
			else
			{
				if( $row['type'] == 2 )
				{
					if( isset($entries[$row['id']]['data']) && variable($entries[$row['id']]['data']) != null)
					{
						$entries[$row['id']]['data'] = json_decode($entries[$row['id']]['data'], TRUE);
					}
					// add image
					if( isset($entries[$row['id']]['data']['images']) && count($entries[$row['id']]['data']['images']) > 0 )
					{
						$_id = $entries[$row['id']]['data']['images'][key($entries[$row['id']]['data']['images'])];
						$entries[$row['id']]['thumb'] = $this->config->item('client_base').$this->config->item('client_media')."images/".$_images[$_id]['thumb_150'];
					}
					else
					{
						$entries[$row['id']]['thumb'] = $this->config->item('client_base').$this->config->item('client_media').
						"layout/empty_thumb150.jpg";
					}
					// ----------------------------------------------------------
					// if position is set
					if( isset($entries[$row['id']]['data']['position']) )
					{
						if( !isset($pos[$entries[$row['id']]['data']['designer']]) || 
						!in_array($entries[$row['id']]['data']['position'], $pos[$entries[$row['id']]['data']['designer']]) )
						{
							$_pos = $entries[$row['id']]['data']['position'];
						}
						else
						{
							$_pos = count($_pos);
							//
							while( in_array($_pos, $pos[$entries[$row['id']]['data']['designer']]) )
							{
								++$_pos;
							}
						}
						// add item to designer
						$grids[$entries[$row['id']]['data']['designer']][$_pos] = $this->load->view('custom/entry_grid_item', $entries[$row['id']], TRUE);
						// add position to array
						$pos[$entries[$row['id']]['data']['designer']][] = $_pos;
					}
					// -------------------------
					// if not position set
					else
					{
						$grid_alt[$entries[$row['id']]['data']['designer']][] = $this->load->view('custom/entry_grid_item', $entries[$row['id']], TRUE);
					}
				}
			}
		}
		// -------------------------
		// merge grids
		foreach($grid_alt as $designer => $items)
		{
			foreach( $items as $vals)
			{
				$grids[$designer][] = $vals;
			}
			//
		}
		// -------------------------
		// build entry element
		foreach($grids as $designer => $products)
		{
			ksort($products);
			$this->data['content'] .= '<div class="item grid-group '.$designer.'" data-designer="'.$designer.'"><div class="group"><h4>'.ucwords(str_replace('-',' ',$designer)).'</h4>'.implode('',$products).'</div></div>';	
		}
		// -----------------------
		$filters = index_array( $this->content_model->db_fetch('client_data',array('where' => array('key' => 'product')) ), 'type', TRUE);
		// -----------------------
		// get designers
		foreach( $filters['designer'] as $value )
		{
			$designers[$value['position']] = '<li data-value="'.$value['tag'].'" class="filter">'.$value['label'].'</li>';
		}
		// sort by position
		ksort($designers);
		// merge items
		$this->data['designers'] = implode('',$designers);
		// -----------------------
		// get product types
		foreach( $filters['type'] as $value )
		{
			$product_types[$value['position']] = '<li data-value="'.$value['tag'].'" class="filter">'.$value['label'].'</li>';
		}
		// sort by position
		ksort($product_types);
		// merge items
		$this->data['product_type'] = implode('',$product_types);
		// -----------------------
		$this->data['content'] .= '</ul>';
		// load entry form
        view('custom/entry_grid', $this->data);
	}
	// ---------------------------------------------------
	// Edit Entry
	public function edit($id = null)
	{
		// load assets
		$this->load->model('content_model');
		// if new
		if($id == 'new' || $id == null)
		{
			$id = $this->create();
		}
		// fetch entry
		$this->db->select('id, type, status, title, text, data');
		$this->db->where('id',$id);
		$this->db->from('client_entries');
		$query = $this->db->get();
		$this->data['entry'] = $query->row_array();
		if(isset($this->data['entry']['data']))
		{
			$this->data['entry']['data'] = json_decode($this->data['entry']['data'], TRUE);
		}
		// check for images
		if(isset($this->data['entry']['data']['images']) && count($this->data['entry']['data']['images']) > 0)
		{
			$this->db->select('id, key, type, data');
			// loop through ids
			foreach($this->data['entry']['data']['images'] as $key => $img_id)
			{
				$this->db->or_where('id', $img_id);
			}
			// fetch from db
			$this->db->from('client_files');
			$query = $this->db->get();
			//
			foreach ($query->result_array() as $row)
			{
				$images[$row['id']] = $row;
				if( isset($images[$row['id']]['data']) && variable($images[$row['id']]['data']) != null)
				{
					$images[$row['id']]['data'] = json_decode($images[$row['id']]['data'], TRUE);
				}
			}
			//
			$this->data['images'] = variable($images);
		}
		// get product_types
		$this->data['product_types'] = $this->content_model->cat_select('product_type', 'type', $this->data['entry']['data']['product_type']);
		$this->data['product_types_edit'] = $this->content_model->cat_select_edit('product_type_edit', 'type', array('label','sizes','tag','position'));
		// product type position
		$count = count($this->content_model->db_fetch('client_data',array('where' => array('key' => 'product', 'type' => 'type'))));
		for($i=0; $i <= $count; $i++)
		{
			$this->data['product_type_position'][] = '<option value="'.($i+1).'">'.($i+1).'</option>';
		}
		$this->data['product_type_position'] = '<label id="product_type_pos_label" for="product_type_position">Position </label><select id="product_type_position" class="position">'.implode('',$this->data['product_type_position']).'</select>';
		// get designers
		$this->data['designer'] = $this->content_model->cat_select('designer', 'designer', $this->data['entry']['data']['designer']);
		$this->data['designer_edit'] = $this->content_model->cat_select_edit('designer_edit', 'designer', array('label','tag','position'));
		// designer position
		$count = count($this->content_model->db_fetch('client_data',array('where' => array('key' => 'product', 'type' => 'designer'))));
		for($i=0; $i <= $count; $i++)
		{
			$this->data['designer_position'][] = '<option value="'.($i+1).'">'.($i+1).'</option>';
		}
		$this->data['designer_position'] = '<label id="designer_pos_label" for="designer_position">Position </label><select id="designer_position" class="position">'.implode('',$this->data['designer_position']).'</select>';
		// load entry form
        view('form/entry', $this->data);
	}
	// ---------------------------------------------------
	// create
	function create()
	{
		$data = array('type' => 1);
		$this->db->insert('client_entries', $data);
		return $this->db->insert_id();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */