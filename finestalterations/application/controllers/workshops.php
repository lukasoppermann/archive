<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Workshops extends MY_Controller {

	public function index( $workshop = null )
	{	
		// define variables
		$this->data['headline'] = 'Alteration Workshops';
		// prepare site menu
		// get content from db
		$workshops = db_select(config('db_prefix').config('db_entries'), array('type' => '2', 'status' => '1'), array('single' => false));
		//
		if( isset($workshops) && count($workshops) >= 1 && variable($workshops) != null )
		{
			// prepare posts
			foreach( $workshops as $key => $_workshop )
			{
				// save position by id
				$workshop_by_id[$_workshop['position']] = $key;
				// reset variable
				$active = "";
				// check if current workshop
				if( '/'.$workshop == $_workshop['permalink'] )
				{
					if( isset($_workshop['images']) && count($_workshop['images']) > 0)
					{
						$_workshop['images'] = db_select(config('db_files'), array('id' => $_workshop['images']));
						// check images
						if( isset($_workshop['images'])  && is_array($_workshop['images']) )
						{
							js_add('fs.gallery');
							$active = ' active';
							foreach($_workshop['images'] as $img)
							{
								$_workshop['banner'][] = '<div class="image'.$active.'">
									<img data-src="'.base_url().'/media/images/'.$img['filename'].'_thumb_230_660.'.$img['ext'].'" alt="'.$img['filename'].'" /></div>';
								$active = '';
							}
							$_workshop['banner'] = '<div class="gallery"><div class="image-wrap">'.implode('',$_workshop['banner']).'</div></div>';
						
						}
					}
					// preapre variables
					$this->data = array_merge($this->data, $_workshop);
					$this->data['site_headline'] 	= $_workshop['title'];
					$this->data['site_content'] 	= $_workshop['text'];
					$active = " class='active'";
				}
				// create menu items
				$this->data['site_menu'][$_workshop['position']] = "<li".$active.">
					<a href='".base_url().'workshops'.variable($_workshop['permalink'])."'>".variable($_workshop['menu_item'])."</a></li>";
			}
			// sort site menu
			ksort($this->data['site_menu']);
		
			// prepare selected workshop
			if( $workshop == null || (variable($this->data['site_content']) == null && variable($this->data['site_headline']) == null) )
			{
				$position = key($this->data['site_menu']);
				$key = $workshop_by_id[$position];
				// load gallery
				if( isset($workshops[$key]['images']) && count($workshops[$key]['images']) > 0)
				{
					$workshops[$key]['images'] = db_select(config('db_files'), array('id' => $workshops[$key]['images']));
					// check images
					if( isset($workshops[$key]['images'])  && is_array($workshops[$key]['images']) )
					{
						js_add('fs.gallery');
						$active = ' active';
						foreach($workshops[$key]['images'] as $img)
						{
							$workshops[$key]['banner'][] = '<div class="image'.$active.'">
								<img data-src="'.base_url().'/media/images/'.$img['filename'].'_thumb_230_660.'.$img['ext'].'" alt="'.$img['filename'].'" /></div>';
							$active = '';
						}
						$workshops[$key]['banner'] = '<div class="gallery"><div class="image-wrap">'.implode('',$workshops[$key]['banner']).'</div></div>';
						
					}
				}
				// load content
				$this->data = array_merge($this->data, $workshops[$key]);
				$this->data['site_headline'] 	= $workshops[$key]['title'];
				$this->data['site_content'] 	= $workshops[$key]['text'];			
				// replace menu item
				$this->data['site_menu'][$position] = "<li class='active'>
					<a href='".base_url().'workshops'.variable($workshops[$key]['permalink'])."'>".variable($workshops[$key]['menu_item'])."</a></li>";
			}
			// prepare site menu
			$this->data['site_menu'] = implode('',$this->data['site_menu']);
		}
		// load view
		view('multipage', $this->data);
	}

}