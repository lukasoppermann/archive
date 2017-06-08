<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Home extends MY_Controller {

	public function index(  )
	{	
		// load assets
		css_add('blocks, home');
		// fetch blocks from db
		$blocks = db_select(config('db_prefix').config('db_entries'), array('type' => 6, 'status' => 1));
		// loop through blocks
		foreach( $blocks as $block )
		{
			if( isset($block['image']) && $block['image'] != null )
			{
				$block['image'] = db_select(config('db_files'), array('id' => $block['image']), array('json' => array('data'), 'single' => TRUE));
			}
			else
			{
				// reset image
				$block['image'] = null;
			}
			!isset($block['position']) ? $block['position'] = '0' : '';
			//
			$this->data['column'][$block['column']][$block['position']] = $this->load->view('block', $block, TRUE);
		}
		// loop through columns
		foreach($this->data['column'] as $id => $column)
		{
			ksort($column);
			$this->data['column'][$id] = implode('',$column);
		}
		// -------------------------------
		$images = config('banner');
		// adding images
		if( isset($images) && is_array($images) && count($images) > 0)
		{
			$imgs = db_select(config('db_files'), array('id' => array_keys($images)));
			// check images
			if( isset($imgs)  && is_array($imgs) )
			{
				js_add('fs.gallery');
				$active = ' active';
				foreach($imgs as $img)
				{
					$caption = '';
					if($images[$img['id']]['caption'] != '')
					{
						$caption = '<a href="'.base_url(FALSE).$images[$img['id']]['link'].'" class="caption">'.$images[$img['id']]['caption'].'</a>';
					}
					
					$entry['banner'][] = '<div class="image'.$active.'">
						<img data-src="'.base_url().'/media/images/'.$img['filename'].'_thumb_380_1000.'.$img['ext'].'" alt="'.$img['filename'].'" />
						'.$caption.'
					</div>';
					$active = '';
				}
				$this->data['big_banner'] = '<div class="gallery"><div class="image-wrap">'.implode('',$entry['banner']).'</div></div>';
			}
		}
		//
		view('home', $this->data);
	}

}