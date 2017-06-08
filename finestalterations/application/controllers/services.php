<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Services extends MY_Controller {

	public function index( $url_service = null )
	{	
		// define variables
		$this->data['headline'] = 'Alteration Services';
		// get content from db
		$services = db_select(config('db_prefix').config('db_entries'), array('type' => '1', 'status' => '1'), array('single' => false));
		// prepare posts
		foreach( $services as $key => $service )
		{
			// save position by id
			$service_id[$service['position']] = $key;
			// reset variable
			$active = "";
			// check if current service
			if( '/'.$url_service == $service['permalink'] )
			{
				if( isset($service['images']) && count($service['images']) > 0)
				{
					$service['images'] = db_select(config('db_files'), array('id' => $service['images']));
					// check images
					if( isset($service['images']) && is_array($service['images']) )
					{
						js_add('fs.gallery');
						$active = ' active';
						foreach($service['images'] as $img)
						{
							$service['banner'][] = '<div class="image'.$active.'">
								<img data-src="'.base_url().'/media/images/'.$img['filename'].'_thumb_230_660.'.$img['ext'].'" alt="'.$img['filename'].'" /></div>';
							$active = '';
						}
						$service['banner'] = '<div class="gallery"><div class="image-wrap">'.implode('',$service['banner']).'</div></div>';
						
					}
				}
				// preapre variables
				$this->data = array_merge($this->data, $service);
				$this->data['site_headline'] 	= $service['title'];
				$this->data['site_content'] 	= $service['text'];
				$active = " class='active'";
			}
			// create menu items
			$this->data['site_menu'][$service['position']] = "<li".$active.">
				<a href='".base_url().'services'.variable($service['permalink'])."'>".variable($service['menu_item'])."</a></li>";
		}
		// sort site menu
		ksort($this->data['site_menu']);
		
		// prepare selected service
		if( $url_service == null || (variable($this->data['site_content']) == null && variable($this->data['site_headline']) == null) )
		{
			$position = key($this->data['site_menu']);
			$key = $service_id[$position];
			if( isset($service['images']) && count($service['images']) > 0)
			{
				$service['images'] = db_select(config('db_files'), array('id' => $service['images']));
			}
			// check images
			if( isset($service['images']) && count($service['images']) > 0)
			{
				$service['images'] = db_select(config('db_files'), array('id' => $service['images']));
				// check images
				if( isset($service['images'])  && is_array($service['images']) )
				{
					js_add('fs.gallery');
					$active = ' active';
					foreach($service['images'] as $img)
					{
						$service['banner'][] = '<div class="image'.$active.'">
							<img data-src="'.base_url().'/media/images/'.$img['filename'].'_thumb_230_660.'.$img['ext'].'" alt="'.$img['filename'].'" /></div>';
						$active = '';
					}
					$services[$key]['banner'] = '<div class="gallery"><div class="image-wrap">'.implode('',$service['banner']).'</div></div>';
						
				}
			}
			// load content
			$this->data = array_merge($this->data, $services[$key]);
			$this->data['site_headline'] 	= $services[$key]['title'];
			$this->data['site_content'] 	= $services[$key]['text'];
			// replace menu item
			$this->data['site_menu'][$position] = "<li class='active'>
				<a href='".base_url().'services'.variable($services[$key]['permalink'])."'>".variable($services[$key]['menu_item'])."</a></li>";
		}
		// prepare site menu
		$this->data['site_menu'] = implode('',$this->data['site_menu']);
		// load view
		view('multipage', $this->data);
	}

}