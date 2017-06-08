<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Downloads extends MY_Controller {
	
	var $data = null;
	
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	// index 
	function index()
	{
		// -------------------------------------------------
		// loading stuff
		$this->stylesheet->add('base-0.0.1, form-0.0.1, download-0.0.1');
		$this->load->library('form_validation');
		// -------------------------------------------------
		// if submit
		$submit = $this->input->post('submit');
		if( isset($submit) && ($submit == 'hochladen'))
		{
			$this->upload_file();		
		}
		elseif( isset($submit) && ($submit == 'speichern') )
		{
			$this->edit_file();
		}
		elseif( isset($submit) && ($submit == 'löschen')  )
		{
			$this->delete_file($this->input->post('id'));
		}
		$this->data['list'] = $this->display_files();

		// -------------------------------------------------
		// define variables
		$this->data['title'] = 'Downloads';	
		$this->data['upload_name'] = $this->input->post('upload_name');			
		// -------------------------------------------------
		// load view
		$this->data['template'] = 'forms/downloads';
		$this->load->view('default/default_page', $this->data);
	}
	
	function upload_file()
	{
		$this->form_validation->set_rules('upload_name', 'Angezeigter Name', 'required');
		// --------------
		$config['upload_path'] = $this->config->item('client_folder').'/media/files/';
		$config['allowed_types'] = 'gif|jpg|png|pdf|docx|xls|doc|zip|txt';
		$config['max_size']	= '100000';
		$config['max_width']  = '2024';
		$config['max_height']  = '2024';
		$config['file_name']  = urlencode(str_replace(array(' ', '&'), array('-','+'), replace_accents($this->input->post('upload_name'))));
				
		$this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload('upload_file'))
		{
			$this->data['message'] = $this->upload->display_errors('<div class="notice error"><p>', '</p></div>');
		}
		else
		{
			$data = $this->upload->data();

			$insert = array(
					'name' 	=> $this->input->post('upload_name'),
					'file' 	=> $data['file_name'],
					'type' 	=> $data['file_type']
				);
				
			if($data['is_image'])
			{
				$insert['data']	= 'category:'.$this->input->post('category').';relative_path:'.$config['upload_path'].';size:'.$data['file_size'].';width:'.$data['image_width'].
							';height:'.$data['image_height'];
			}
			else
			{
				$insert['data']	= 'category:'.$this->input->post('category').';relative_path:'.$config['upload_path'].';size:'.$data['file_size'];
			}
			// -------------------------------------------------
			// validate
			if ($this->form_validation->run() == FALSE)
			{
				$this->data['message'] = validation_errors('<div class="notice error"><p>', '</p></div>');
			}
			else
			{
				// -------------------------------------------------
				// insert into entry table
				$this->db->insert($this->config->item('client_prefix').$this->config->item('db_files'), $insert); 
				redirect(current_url(),'refresh');
			}
		}
	}
	
	function display_files()
	{
		// -------------------------------------------------
		// retieve from DB
		$this->db->select('id, name, file, type, data');
		$this->db->from($this->config->item('client_prefix').$this->config->item('db_files'));
		
		$query = $this->db->get();
		
		// build list
		$list = null;
		
		if($query->num_rows() > 0)
		{	
			foreach ($query->result() as $row)
			{
				$tmp_data = _split_data_new($row->data);
				//
				if(isset($tmp_data['category'])){
					$items[$row->id] = array_merge(
						$tmp_data,
						array(
						'id' 	=> $row->id,
						'name' 	=> $row->name,
						'file' 	=> $row->file,
						'type' 	=> $row->type,
						'path' 	=> $this->config->item('client_base_url').'media/files/'					
						)
					);
				}
			}
			
			if(isset($items)){
			$items = _array_sort($items, 'name');
			$items = _array_sort($items, 'category');

				foreach($items as $id => $item)
				{
					if(file_exists($item['relative_path'].$item['file']))
					{
						// add to list	
						$list .= $this->load->view('forms/download_edit', $item, true);
					}
					else
					{
						@unlink($item['relative_path'].$item['file']);
					}
				}
			}
		}
		
		return $list;
	}
	
	function edit_file()
	{
		$id 		= $this->input->post('id');
		$name 		= $this->input->post('name');
		$category 	= $this->input->post('category');
		$path 		= $this->input->post('path');
		//
		$this->db->select('data');
		$this->db->where('id', $id);
		$this->db->from($this->config->item('client_prefix').$this->config->item('db_files'));
		
		$query = $this->db->get();
		
		$row = $query->result_array();
		
		$insert_data = null;
		$tmp_data = _split_data_new($row[0]['data']);
		$tmp_data['category'] = $category;
		
		foreach($tmp_data as $key => $data)
		{
			$insert_data .= $key.':'.$data.';';
		}	
		//
		$this->db->where('id', $id);
		$this->db->update($this->config->item('client_prefix').$this->config->item('db_files'), array('name' => $name, 'data' => $insert_data));
	}
	
	function delete_file($id)
	{
		$base = rtrim(base_url(),'/');
		$base = substr($base,0, strrpos($base, '/'));
		$path = '../.'.str_replace($base, '', $this->input->post('full_path'));
		
		$this->load->helper('file');
		$this->db->where('id', $id);
		$this->db->delete($this->config->item('client_prefix').$this->config->item('db_files'));
		
		@unlink($path);
	}

}
	
// close downloads.php