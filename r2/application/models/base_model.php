<?php if (! defined('BASEPATH')) exit('No direct script access');

class base_model extends CI_Model{
	// ---------------------------------------------------------------------------
	// get news for news page
	function news($id = null)
	{
		if($id == null)
		{
			// get news from db
			$news = index_array(get_db_data(config('prefix').config('db_entries'), array('where' => array('type' => '2'), 'select' => '*')), 'language', TRUE);
			// sort posts
			$news = index_array($news[config('lang_id')], 'date', FALSE);
			arsort($news);
			// loop through current lang news
			foreach($news as $id => $item)
			{
				$output[] = $this->load->view('custom/news', $item, TRUE);
			}
			//
			if(count($output) > 0)
			{
				return array('page' => '<h1>News</h1>'.implode('',$output));
			}
		}
		else
		{
			$news = get_db_data(config('prefix').config('db_entries'), array('where' => array('id' => $id), 'select' => '*'));
			$news[0]['single'] = 'true';
			return array('page' => $this->load->view('custom/news', $news[0], TRUE), 'header' => variable($news[0]['title_image']));
		}
	}
	// ---------------------------------------------------------------------------
	// get teaser for sidebar
	function teaser()
	{
		// get 
		$post_data = index_array(get_db_data(config('prefix').config('db_entries'), array('where' => array('type' => '2'), 'select' => '*')), 'language', TRUE);
		// get news teaser
		if(count($post_data) > 0 && array_key_exists(config('lang_id'), $post_data) )
		{
			// sort posts
			$teaser = index_array($post_data[config('lang_id')], 'date', TRUE);
			arsort($teaser);
			// teaser count
			$c = 0;
			// loop through posts
			$output = array('');
			foreach($teaser as $k => $v) 
			{
				if($c < 2)
				{
					$t = $v[key($v)];
					if(isset($t['excerpt']) && $t['excerpt'] != null )
					{
						// get image for teaser
						if(isset($t['news_image']) && $t['news_image'] != null)
						{
							$image = get_db_data(config('prefix').config('db_files'), array('where' => array('id' => $t['news_image']), 'select' => '*'));
							$t['news_img'] = $image[0];
						}
						// create teaer output
						$output[] = $this->load->view('custom/teaser', $t, true);
						$c++;
					}
				}
			}
			// return
			return '<div id="teaser_bar">'.implode($output, '').'</div>';
		}
	}
	// ---------------------------------------------------------------------------	
	// prepare downloads for post
	function downloads($id)
	{
		// get 
		$post_data = get_db_data(config('prefix').config('db_entries'), array('where' => array('id' => $id), 'select' => '*'));	
		$post_data = $post_data[0];
		//
		if(isset($post_data['files']) && is_array($post_data['files']) )
		{
			foreach($post_data['files'] as $file)
			{
				$this->db->or_where('id',$file);
			}
			//
			$this->db->select('*');
			$query = $this->db->get(config('prefix').config('db_files'));
			//
			$output[] = '';
			foreach ($query->result() as $row)
			{
				$files = json_decode($row->data, true);
				$files['id'] = $row->id;
				$files['type'] = $row->type;
				$output[] = '<div class="download-file"><a href="'.media($files['path']).'" target="_blank"><h4>'.$files['label'].'</h4><span>Download ('.byte_format($files['size']).' / '.$files['filetype'].')</span></a></div>';
			}
			// return
			if(isset($output) && count(array_filter($output)) > 0)
			{
				return '<div id="media_box"><h3>Download</h3>'.implode('',$output).'</div>';
			}
			else
			{
				return FALSE;
			}
		}
	}
	// ---------------------------------------------------------------------------	
	// prepare header to post in header-view
	function header($entry_id = null)
	{
		// try to fetch img from current entry
		if($entry_id != null)
		{
			// get post image
			$post = get_db_data(config('prefix').config('db_entries'), array('where' => array('id' => $entry_id), 'select' => '*'));
			if(isset($post[0]['title_image']) && $post[0]['title_image'] != null)
			{
				$img_id = $post[0]['title_image'];
			}
		 }
		//
		if(isset($img_id))
		{
			$image = get_db_data(config('prefix').config('db_files'), array('where' => array('id' => $img_id), 'select' => '*'));
			$image = $image[0];
		}
		else
		{
			$image = get_db_data(config('prefix').config('db_files'), array('where' => array('key' => 'default'), 'select' => '*'));			
			$image = $image[0];
		}
		// check for type
		if($image['type'] == 'image')
		{
			return "<div class='img'><img src='".media($image['path'])."' alt='".config('page_name').' - '.$image['alt']."' /></div>";
		}
		elseif($image['type'] == 'gallery')
		{
			$c = 0;
			shuffle($image['files']);
			foreach($image['files'] as $file)
			{
				$c++;
				if($c > 1)
				{
					$src = 'data-';
					$class = 'later';
				}
				//
				$_img = get_db_data(config('prefix').config('db_files'), array('where' => array('id' => $file), 'select' => '*'));			
				$output[] = '<div class="img"><img class="'.variable($class).'" '.variable($src).'src="'.media($_img[0]['path']).'" width="950" height="350" alt="'.config('page_name').' - '.$_img[0]['alt'].'"></div>';
			}
			// return
			return '<div class="slideshow">'.implode('',$output)."</div>";
		}
		//
	}
// End of model
}