<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Franchise extends MY_Controller {

	public function index(  )
	{	
		css_add('franchise');
		// define variables
		$this->data['body_id'] = ' id="franchise"';
		$this->data['headline'] = 'Get in touch';
		// prepare quote
		$this->data['quote'] = '<form><input></form>';
		$entry = db_select(config('db_prefix').config('db_entries'), array('permalink' => '/franchise', 'status' => 1), array('single' => true));
		// contact button
		$button = safe_mailto('franchise@finestalterations.com.au','Franchise Inquiry', array('link' => TRUE, 'class' => 'button inquiry', 'target' =>'_blank') );
		$first = ' first';
		// prepare quotes
		if( count($entry['blocks']) > 0 )
		{
			//
			foreach( $entry['blocks'] as $block )
			{
				$this->data['quotes'][] = '<div class="quote-box'.$first.'">
					<h3>'.$block['title'].'</h3>
					<div class="quote">
						'.$block['content'].'
					</div>'.$button.'
				</div>';
				$button = '';
				$first = '';
			}
			//
			$this->data['quotes'] = implode($this->data['quotes'], '');
		}
		else
		{
			$this->data['quotes'] = '<div class="quote-box"><h3>Franchise Inquiry</h3>'.$button.'</div>';
		}
		// adding images
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
						<img data-src="'.base_url().'/media/images/'.$img['filename'].'_thumb_380_1000.'.$img['ext'].'" alt="'.$img['filename'].'" /></div>';
					$active = '';
				}
				$this->data['big_banner'] = '<div class="gallery"><div class="image-wrap">'.implode('',$entry['banner']).'</div></div>';
			}
		}
		// preapre variables
		$this->data['site_headline'] = $entry['title'];
		$this->data['site_content'] = variable($entry['text']);
		// load view
		view('bicolumn', $this->data);
	}

}