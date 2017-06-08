<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Stores extends MY_Controller {

	public function index( $current_store )
	{	
		// add assets
		css_add('stores');
		js_add('http://maps.google.com/maps/api/js?sensor=true, gmaps, stores');
		// define variables
		$this->data['headline'] = 'Our Stores';
		// prepare site menu
		foreach($this->data['stores'] as $pos => $store )
		{
			// reset variable
			$active = "";
			// check if current service
			if( $current_store == $store['permalink'] )
			{
				// preapre variables
				$this->data['site_content'] = $this->load->view('store', $store, TRUE);
				$active = " class='active'";
			}
			//
			$this->data['site_menu'][] = '<li'.$active.'><a href="'.base_url().'stores/'.$store['permalink'].'">'.$store['name'].'</a></li>';
		}
		// check if site content is set
		if( !isset($this->data['site_content']) )
		{
			// get first store
			reset($this->data['stores']);
			$first_store = $this->data['stores'][key($this->data['stores'])];
			// set content
			$this->data['site_content'] = $this->load->view('store', $first_store, TRUE);
			// set menu
			$this->data['site_menu'][key($this->data['site_menu'])] = '<li class="active"><a href="'.base_url().'stores/'.$first_store['permalink'].'">'.$first_store['name'].'</a></li>';
		}
		
		$this->data['site_menu'] = implode('',$this->data['site_menu']);
		// load view
		view('multipage', $this->data);
	}

}