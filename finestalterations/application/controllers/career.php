<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Career extends MY_Controller {

	public function index( $job_url = null )
	{	
		// define variables
		$this->data['headline'] = 'Career Opportunities';
		css_add('career');
		// prepare site menu
		// get content from db
		$entries = db_select(config('db_prefix').config('db_entries'), array('type' => '5', 'status' => '1'), array('single' => false));
		//
		if( isset($entries) && count($entries) >= 1 && variable($entries) != null )
		{
			// prepare posts
			foreach( $entries as $key => $entry )
			{
				// save position by id
				$by_id[$entry['position']] = $key;
				// reset variable
				$active = "";
				// check if current service
				if( '/'.$job_url == $entry['permalink'] )
				{
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
					// preapre variables
					$this->data = array_merge($this->data, $entry);
					$this->data['site_headline'] 	= $entry['title'];
					$this->data['site_content'] 	= $entry['text'];
					$active = " class='active'";
				}
				// create menu items
				$this->data['site_menu'][$entry['position']] = "<li".$active.">
					<a href='".base_url().'career'.variable($entry['permalink'])."'>".variable($entry['menu_item'])."</a></li>";
			}
			// sort site menu
			ksort($this->data['site_menu']);

			// prepare selected service
			if( $job_url == null || (variable($this->data['site_content']) == null && variable($this->data['site_headline']) == null) )
			{
				$position = key($this->data['site_menu']);
				$key = $by_id[$position];
				// get images
				$entry = $entries[$key];
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
				// load content
				$this->data = array_merge($this->data, $entries[$key]);
				$this->data['site_headline'] 	= $entries[$key]['title'];
				$this->data['site_content'] 	= $entries[$key]['text'];
				// replace menu item
				$this->data['site_menu'][$position] = "<li class='active'>
					<a href='".base_url().'career'.variable($entries[$key]['permalink'])."'>".variable($entries[$key]['menu_item'])."</a></li>";
			}
			// add links
			$this->data['site_content'] .= '<br /><br /><script type="text/javascript">document.write(
"<n gnetrg=\"_oynax\" uers=\"znvygb:pnerref\100svarfgnygrengvbaf\056pbz\056nh\" pynff=\"ohggba vadhvel\">Nccyl<\057n>".replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));
</script>';
			// prepare site menu
			$this->data['site_menu'] = implode('',$this->data['site_menu']);
		}
		// load view
		view('multipage', $this->data);
	}

}