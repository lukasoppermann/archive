<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Home extends MY_Controller {

	public function index( $var = null )
	{
		if( $var == 'confirm' )
		{
			$this->data['confirm'] = $this->confirm();
		}
		//
		$this->data['page_title'] = 'High Fashion Shoes';
		// get images
		$this->load->model('media_model');
		$_m1 = $this->media_model->get_home_products();
		$_m2 = $this->media_model->get_home_images();
		$_media_1 = array_key_exists('media', $_m1) ? $_m1['media'] : array();
		$_media_2 = array_key_exists('media', $_m2) ? $_m2['media'] : array();
		$media = array_merge( array_values($_media_1), array_values($_media_2) );
		// set default elements
		if( isset($_m2['instore']['home_image']) )
		{
			$elements[] = array(
				'class' 	=> variable($_m2['instore']['column']).' cat-link',
				'home_image' => variable($_m2['instore']['home_image']),
				'box_title' => 'in store',
				'link' 		=> 'instore',
				'template' 	=> 'home_item'
			);
		}
		$elements[] = array(
			'class' 	=> 'flex-height column-one',
			'content' 	=>  variable($this->page_model->get_news(5)),
			'template' 	=> 'home_text'
		);
		$elements[] = array(
			'class' 	=> variable($_m2['eboutique']['column']).' cat-link',
			'home_image' => variable($_m2['eboutique']['home_image']),
			'box_title' => 'e-boutique',
			'link' 		=> 'eboutique',
			'template' 	=> 'home_item'
		);
		// set dynamic elements
		foreach($media as $m)
		{
			$elements[] = array(
				'class' 	=> variable($m['column']),
				'home_image' => variable($m['home_image']),
				'box_title' => variable($m['label']),
				'link' 		=> isset($m['link']) ? $this->home_link($m['link']) : '',
				'template' 	=> 'home_item'
			);
		}
		// loop through elements
		foreach($elements as $element)
		{
			$output[] = $this->load->view('custom/'.$element['template'], $element, TRUE);
		}
		// add container
		$this->data['content'] = implode('', $output);
				
	    // load into template
        view('default', $this->data);
	}
	// --------------------------------
	// home_link
	function home_link($link)
	{
		if(strpos($link, '.') != false)
		{
			return 'http://'.str_replace('http://','', $link);
		}
		else
		{
			return base_url().$link;
		}
	}
	// --------------------------------
	// confirm
	function confirm()
	{
		// delete cart
		$this->cart->destroy();
		//
		return "<div id='order_conf'><h1>Thank you for your purchase at Loula</h1></div>";
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */