<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Tips extends MY_Controller {

	public function index( $tip = null )
	{	
		if( $tip == null )
		{
			$this->data['content'] = $this->overview();
		}
		else
		{
			$this->data['content'] = $this->entry($tip);
		}
		// define variables
		$this->data['headline'] = 'Tips & Tricks';
		// load view
		view('default', $this->data);
	}	
	// ----------------------------------
	// overview
	function overview()
	{
		css_add('blocks');
		// get content from db
		$entries = db_select(config('db_prefix').config('db_entries'), array('type' => '3', 'status' => '1'), array('single' => false));
		if( count($entries) > 0 )
		{
			$i = 0;
			// prepare entries
			foreach( $entries as $entry )
			{
				// reset $i
				if($i == 3){ $i = 0; }
				// get image
				$entry['image'] = null;
				if( isset($entry['images']) && is_array($entry['images']) && count($entry['images']) > 0)
				{
					$entry['images'] = db_select(config('db_files'), array('id' => $entry['images']) );
					
					$entry['image'] = $entry['images'][key($entry['images'])];
				}
				$entry['permalink'] = '/tips'.$entry['permalink'];
				// assign to columns
				$output[$i][] = $this->load->view('block', $entry, TRUE);
				// increase $i
				++$i;
			}
			return '<div class="column">'.implode($output[0], '').'</div><div class="column">'.implode($output[1], '').'</div><div class="column column-last">'.implode($output[2], '').'</div>';
		}
	}
	// ----------------------------------
	// entry
	function entry( $tip = null )
	{
		if( $tip != null )
		{
			// get entry data
			$entry = db_select(config('db_prefix').config('db_entries'), array('status' => '1', 'permalink' => '/'.$tip), array('single' => true));
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
					$entry['banner'] = '<div class="gallery"><div class="image-wrap">'.implode('',$entry['banner']).'</div></div>';
						
				}
			}
			// prepare quotes
			if( count($entry['blocks']) > 0 )
			{
				$entry['quotes'] = "";
				//
				foreach( $entry['blocks'] as $block )
				{
					$entry['quotes'] .= '<div class="quote-box">
						<h3>'.$block['title'].'</h3>
						<div class="quote">
							'.$block['content'].'
						</div>
					</div>';
				}
			}
			// load entry
			return $this->load->view('tip', $entry, TRUE);
		}
	}
}