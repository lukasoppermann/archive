<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class About extends MY_Controller {

	public function index(  )
	{	
		$entry = db_select(config('db_prefix').config('db_entries'), array('permalink' => '/about', 'status' => 1), array('single' => true));
		// prepare quotes
		if( count($entry['blocks']) > 0 )
		{
			$this->data['quotes'] = "";
			//
			foreach( $entry['blocks'] as $block )
			{
				$this->data['quotes'] .= '<div class="quote-box">
					<h3>'.$block['title'].'</h3>
					<div class="quote">
						'.$block['content'].'
					</div>
				</div>';
			}
		}
		else
		{
			$this->data['quotes'] = '<div class="quote-box"></div>';
		}
		// get images
		if( isset($entry['images']) && count($entry['images']) > 0)
		{
			$entry['images'] = db_select(config('db_files'), array('id' => $entry['images']));
			// check images
			if( isset($entry['images'])  && is_array($entry['images']) )
			{
				js_add('fs.gallery');
				$active = ' active';
				foreach($entry['images'] as $img)
				{
					$entry['banner'][] = '<div class="image'.$active.'">
						<img data-src="'.base_url().'/media/images/'.$img['filename'].'_thumb_230_660.'.$img['ext'].'" alt="'.$img['filename'].'" /></div>';
					$active = '';
				}
				$this->data['banner'] = '<div class="gallery"><div class="image-wrap">'.implode('',$entry['banner']).'</div></div>';
						
			}
		}
		// preapre variables
		$this->data['site_headline'] = $entry['title'];
		$this->data['site_content'] = '<h2 class="main-headline">'.$entry['title'].'</h2>'.$entry['text'];
		// load view
		view('bicolumn', $this->data);
	}

}