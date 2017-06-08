<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Settings extends MY_Controller {

	var $configs 	= NULL;
	var $form 		= NULL;
	
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	// index 
	function index()
	{
		if($this->input->post('submit'))
		{
			$data['message'] = '<div class="notice success"><p>'.$this->_update().'</p></div>';
		}
		// ------------------------------
		// Retrieve from DB
		$this->db->select('key, type, value');
		$this->db->where('key', 'settings');
		$this->db->from($this->config->item('client_prefix').$this->config->item('db_data'));
		
		$query = $this->db->get();
		
		foreach ($query->result() as $row)
		{
			// indexed by id only
			$data['opt']['value'][$row->type] = $row->value;
		}
		
		// ------------------------------
		// CSS
		$this->stylesheet->add('form-0.0.1, settings-0.0.1');
		// ------------------------------
		// set data
		$this->data['title'] 					= 'Einstellungen';
		$data['config'] 						= 'form_settings_general';
		$data['form_attributes'] 				= array('id' => 'form', 'class' => 'settings');
		$this->data['content'] 					= form($data, 'settings_form');
		// ------------------------------
		// $this->load->library('form');
		// $this->form = $this->form->get_instance('arg');
		// $b = $this->form->get_instance('lukas');
		// echo $b->get();
		// ------------------------------
		$this->load->view('custom/settings_page', $this->data);
	}	
	// Submit Changes
	function _update()
	{	
		$array['page_name'] = $this->input->post('page_name');
		$array['slogan_de'] = $this->input->post('slogan_de');
		$array['slogan_en'] = $this->input->post('slogan_en');
		$array['disclaimer'] = $this->input->post('disclaimer');		
		$array['company'] = $this->input->post('company');
		$array['email'] = $this->input->post('email');
		$array['phone'] = $this->input->post('phone');
		$array['fax'] = $this->input->post('fax');
		$array['street'] = $this->input->post('street');
		$array['city'] = $this->input->post('city');
		$array['zusatz'] = $this->input->post('zusatz');
		$array['zusatz_en'] = $this->input->post('zusatz_en');	
		$array['keywords'] = $this->input->post('keywords');
		$array['description'] = trim(substr($this->input->post('description'), 0, 150));
				
		foreach($array as $type => $value)
		{

			$this->db->where('key', 'settings');
			$this->db->where('type', $type);
			$this->db->from($this->config->item('client_prefix').$this->config->item('db_data'));
			if ($this->db->count_all_results() == 0) 
			{
				// No record exists, insert.		
				$data = array(
				               'key' => 'settings',
				               'type' => $type,
				               'value' => $value
				            );
				$this->db->insert($this->config->item('client_prefix').$this->config->item('db_data'), $data);
		    } else {
				// A record does exist, update it.
				$this->db->where('key', 'settings');
				$this->db->where('type', $type);				
				$this->db->update($this->config->item('client_prefix').$this->config->item('db_data'), array('value' => $value));
			}
		}
		return lang('changes_saved');
	}
	
// close controller	
}

/* End of file settings.php */
/* Location: ./application/formandsystem/controllers/settings.php */