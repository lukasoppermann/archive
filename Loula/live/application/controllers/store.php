<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Store extends MY_Controller {
	
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	
	// instore
	public function instore()
	{
		$this->data['page_title'] = 'instore';
		$this->data['current_store'] = 'instore';
		$this->store('instore');
	}
	
	// e-boutique
	public function eboutique()
	{
		$this->data['page_title'] = 'e-boutique';
		$this->data['current_store'] = 'eboutique';
		$this->store('eboutique');
	}
	
	// sale
	public function sale()
	{
		$this->data['page_title'] = 'sale';
		$this->data['current_store'] = 'sale';
		$this->store('sales');
	}
	// -------------------------------------------------------------------------------
	// get store data
	function store($store = null)
	{
		// load assets
		$this->load->model('store_model');
		// designers menu
		$this->data['submenu'] = $this->store_model->designers($store);
		// tags
		$this->data['tags'] = $this->store_model->tags($store);
		// get designers
		$designers = index_array($this->store_model->store_data('designer'), 'tag');
		$this->data['cur_designer'] = $designers[key($designers)]['label'];
		// get products
		$products = $this->store_model->get_products('', $store);
		// prepare data
		if(count($products) > 0)
		{
			// ------------------------------
			// hero
			$_products = array_values($products);
			$hero = $_products[mt_rand(0, count($products)-1)];
			$data = array_merge($designers[$hero['designer']], $hero, array('current_store' => $this->data['current_store']));
			$this->data['cur_designer'] = $designers[$hero['designer']]['label'];
			$this->data['hero'] = $this->load->view('product', $data, true);
			// ------------------------------
			// prepare other shoes
			foreach($products as $product)
			{
				if( isset($product['position']) && $product['position'] != null)
				{
					if( isset($pos) && in_array($product['position'], $pos) )
					{
						$product['position'] = count($shoes)+1;
						while( in_array($product['position'], $pos) )
						{
							$product['position']++;
						}
					}
					//
					$shoes[$product['position']] = $this->load->view('product_preview', $product, TRUE);
					$pos[] = $product['position'];
				}
				else
				{
					$others[] = $this->load->view('product_preview', $product, TRUE);
				}
			}
			// add to shoes
			if( isset($others) )
			{
				foreach( $others as $val)
				{
					$shoes[] = $val;
				}
			}
			ksort($shoes);
			// shoes
			$this->data['products'] = implode('',$shoes);
		}
	    // load into template
        view('store', $this->data);
	}
	// -------------------------------------------------------------------------------
	// get product page
	function get_product_page()
	{
		// load assets
		$this->load->model('store_model');
		// get product
		$data = $this->store_model->get_product($this->input->post('id'));
		if($data != FALSE)
		{
			// get designer
			$designers = index_array($this->store_model->store_data('designer'), 'tag');
			// merge data
			$data = array_merge($data, $designers[$data['designer']], array('current_store' => $this->input->post('current_store')));
			$data['id'] = $this->input->post('id');
			// return data
			echo $this->load->view('product',$data,TRUE);
		}
		else
		{
			echo FALSE;
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */