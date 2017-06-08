<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media extends MY_Controller {

	public function index()
	{
		$this->data['page_title'] = 'In the media';
		// load assets
		$this->load->model('media_model');
		// load content
		$this->data['content'] = '<div class="media">'.$this->media_model->get_items().'</div>';
		// load view
		view('default', $this->data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */