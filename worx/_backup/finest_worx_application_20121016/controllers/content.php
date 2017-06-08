<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Content extends MY_Controller {

	public function index( $method = null, $id = null )
	{	
		if( method_exists($this,$method) )
		{
			if( $this->input->post('ajax') == true )
			{
				echo $this->$method( $id );
			}
			else
			{
				$this->$method( $id );
			}
		}
		else
		{
			$this->overview();
		}
	}
	// --------------------------------------------------------------------
	/**
	 * overview
	 *
	 * @description	show all list items
	 * 
	 */
	function overview()
	{
		$this->clean_up();
		// meta title
		$this->data['meta_title'] = 'Content overview';
		// load assets
		css_add('content_list, fs.sortable');
		js_add('fs.sortable, content_list');
		// list entries
		$types = config('entry_types');
		// get data from db
		$entries = index_array(db_select(config('system/current/db_prefix').config('db_entries'), array('type' => array_keys($types)), 
			array('json' => 'data')), 'type', TRUE);
		// list entries	
		foreach( $entries as $key => $list )
		{
			foreach($list as $entry)
			{
				// prepare time
				if( !isset($entry['last_saved']) )
				{
					$entry['last_saved'] = $entry['date'];
				}
				// load view
				if( $entry['status'] == '3' )
				{
					$deleted[$key][] = $this->load->view('content/entry_item', $entry, TRUE);
				}
				// other items
				else
				{
					$group[$key][$entry['position']] = $this->load->view('content/entry_item', $entry, TRUE);
				}
			}
			if( isset($group[$key]) )
			{
				// sort entries
				ksort($group[$key]);
			}			
			$lists[$key] = $this->load->view('content/entry_group', array('group_type' => $types[$key]['name'], 'group' => $types[$key]['label'], 'group_id' => $key, 
			'list' => implode('',(array) variable($group[$key])), 'deleted' => implode('',(array) variable($deleted[$key]))), TRUE);
		}
		// ------------------------
		// add groups to columns
		$i = 1;
		foreach($lists as $list)
		{
			$i = 1 - $i;
			$column[$i][] = $list;
		}
		// ------------------------
		$this->data['content'] = '<div class="column">'.implode('',$column[0]).'</div><div class="column">'.implode('',$column[1]).'</div>';
		// load into template
		view('content/entry_list', $this->data);
	}
	// --------------------------------------------------------------------
	/**
	 * edit
	 *
	 * @description	edit article or create new system
	 * 
	 */
	function edit( $id = null )
	{
		// load assets
		css_add('content_edit,fs.wysiwyg,datepicker,filedrop');
		js_add('rangy-core,rangy-textrange,rangy-selectionsaverestore,fs.base,fs.dom_parser,fs.wysiwyg,jquery.datepicker,jquery.filedrop,fs.gui,content');
		// create new product if no product selected
		if( ( $id == null || !is_numeric($id) ) && $this->input->post('id') == null )
		{
			$id = $this->create( $id );
		}
		elseif( $this->input->post('id') != null && $id == null )
		{
			$id = $this->input->post('id');
		}
		// -------------------
		// fetch data from db
		$entry = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('json' => array('data'), 'single' => true));
		//
		if( isset($entry['homepage']) )
		{
			$entry['homepage'] = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('json' => array('data'), 'single' => true));
		}
		// 
		if( $entry['id'] == null && isset($entry) )
		{
			$entry['id'] = $this->create( $id );
		}
		// prepare date
		if( isset($entry['publication_date']) && $entry['publication_date'] != null && $entry['publication_date'] != false )
		{
			$entry['publication_date'] = date("d/m/Y", strtotime($entry['publication_date']));
		}
		// -------------------
		// check for images
		if( isset($entry['images']) && is_array($entry['images']) && count($entry['images']) > 0 )
		{
			// retrieve images form DB
			$entry['images'] = db_select(config('db_files'), array('id' => $entry['images']), array('json' => array('data'), 'single' => FALSE));
			// check News Image
			if( ( ( !isset($entry['homepage']) || !is_array($entry['homepage']) )
				&& isset($entry['social_images']['news']) && $entry['social_images']['news'] != false) 
				|| 
				( isset($entry['homepage']) && is_array($entry['homepage']) && (!isset($entry['homepage']['image']) || (isset($entry['social_images']['news']) && $entry['homepage']['image'] != $entry['social_images']['news'])) ) 
			)
			{
				// delete because no block is connected
				$entry['social_images']['news'] = '';
				db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['id']), array('data/social_images/news' => false), TRUE, array('data'));
			}
			if( isset($entry['images']) && is_array($entry['images']) && count($entry['images']) > 0 )
			{
				// build image preview
				foreach( $entry['images'] as $image)
				{
					$image['file'] = $image['filename'].'_thumb_150.'.$image['ext'];
					$image['social_images'] = variable($entry['social_images']);
					$entry['display_images'][] = $this->load->view('media/image', $image, TRUE);
				}
				$entry['display_images'] = implode('',$entry['display_images']);
			}
		}
		// -------------------
		// prepare entry types
		foreach(config('entry_types') as $key => $type)
		{
			$entry['types'][$key] = $type['label'];
		}
		// -------------------
		// prepare blocks
		if( isset($entry['blocks']) )
		{
			foreach( $entry['blocks'] as $id => $block )
			{
				$blocks[] = '<div class="block"><span class="close">&times;</span>
							<input type="text" class="block-headline" value="'.$block['title'].'" placeholder="headline" />
							<textarea class="block-content" placeholder="content">'.str_replace('<br />', "\n",$block['content']).'</textarea>
							</div>';
			}
			// implode block
			if( isset($blocks) && count($blocks) > 0)
			{
				$entry['blocks'] = implode('',$blocks);
			}
		}
		// -------------------
		// load content into view
		$this->data['content'] = $this->load->view('content/edit', $entry, TRUE);
		// meta title
		$this->data['meta_title'] = 'Edit Content: '.variable($entry['title']);
		// -------------------
		// load into template
		view('default', $this->data);
	}
	// --------------------------------------------------------------------
	/**
	 * save
	 *
	 * @description	save entry to system
	 * 
	 */
	function save()
	{
		// load assets
		$this->load->helper('text');
		// fetch data
		$data = db_prepare_data(array('id','menu_id', 'permalink', 'tags', 'type', 'language', 'status', 'title', 'text', 'data' => array('position','meta_title', 'meta_description', 'menu_item','facebook','twitter'),'homepage_text','homepage_title'), FALSE);
		// social media
		$data['data']['facebook'] = substr($data['data']['facebook'], 0, 410);
		$data['data']['twitter'] = substr($data['data']['twitter'], 0, 110);
		// add url to fb & tw
		if( ($data['data']['facebook'] != '' || $data['data']['twitter'] != '') && $data['status'] == '1' )
		{
			$url = 'test'; //tiny_url()
			// add url to twitter & facebook
			($data['data']['facebook'] != '') ? $data['data']['facebook'] .= ' '.$url : '';
			($data['data']['twitter'] != '') ? $data['data']['twitter'] .= ''.$url : '';
		}
		// fetch blocks
		$data['data']['blocks'] = json_decode($this->input->post('blocks'), TRUE);
		// clean block
		foreach( $data['data']['blocks'] as $key => $block)
		{
			$data['data']['blocks'][$key]['title'] = $block['title'];
			$data['data']['blocks'][$key]['content'] = str_replace("\n","<br />",urldecode($block['content']));
		}
		// prepare tags
		if( $data['tags'] != null )
		{
			$_tags = explode(',',to_alphanum($data['tags'], array(';' => ',', '.' => ',')));
			foreach( $_tags as $k => $tag )
			{
				$tags[] = to_alphanum(trim($tag), array(' ' => '-') );
			}
			// merge tags
			$data['tags'] = implode(', ', $tags);
		}
		// add last saved
		$data['data']['last_saved'] = server_time();
		// add publication date
		if( $this->input->post('publication_date') != null )
		{
			$format = config('date_format');
			$data['data']['publication_date'] = explode($format['divider'], $this->input->post('publication_date'));
			$data['data']['publication_date'] = $data['data']['publication_date'][$format['y']].'-'.
												leading_zero($data['data']['publication_date'][$format['m']]).'-'.
												leading_zero($data['data']['publication_date'][$format['d']]).' 00:00:00';
		}
		// prepare permalink
		if( $data['permalink'] != null )
		{
			$data['permalink'] = '/'.trim(to_alphanum($data['permalink'], array(' ' => '-','_' => '-')),'/');
		}
		// check permalink
		$permalink = $data['permalink'];
		// 
		while( $this->check_permalink($permalink, $this->input->post('id')) == 'true' )
		{
			$permalink = $data['permalink'].'-'.rand(1,20);
		}
		$data['permalink'] = $permalink;
		// sort entries
		if( variable($data['data']['position']) == null )
		{
			$data['data']['position'] = $this->sort_all($data['type']);
		}
		// -------------------
		// block
		$entry = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $data['id']), array('json' => array('data'), 'single' => true));
		$types = config('homepage_links');
		//
		if( isset($entry['homepage']) && ( (isset($data['homepage_title']) && trim($data['homepage_title']) != '') || (isset($data['homepage_text']) && trim($data['homepage_text']) != '') ) )
		{
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('title' => variable($data['homepage_title']), 'text' => variable($data['homepage_text']), 'permalink' => $types[$data['type']]['name'].$data['permalink']), TRUE, array('data'));
		}
		elseif( isset($entry['homepage']) && trim($data['homepage_title']) == '' && trim($data['homepage_text']) == '' )
		{
			db_delete(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']) );
			$data['data']['homepage'] = null;
		}
		elseif( !isset($entry['homepage']) && ( (isset($data['homepage_title']) && trim($data['homepage_title']) != '') || (isset($data['homepage_text']) && trim($data['homepage_text']) != '') ) )
		{
			$data['data']['homepage'] = db_insert(config('system/current/db_prefix').config('db_entries'), array('type' => config('homepage_type'),
			'data' => array('column' => '0'), 'status' => 1, 'title' => variable($data['homepage_title']), 'text' => variable($data['homepage_text']), 'permalink' => $types[$data['type']]['name'].$data['permalink']));
		}
		unset($data['homepage_text'], $data['homepage_title']);
		// -------------------
		// save to db
		db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $this->input->post('id')), $data, TRUE, array('data'));
		// 
		if( $data['title'] != null || $data['title'] != null )
		{
			$this->session->unset_userdata('id');
		}
		//
		echo json_encode(array('success' => 'TRUE', 'data' => $data));
	}
	// --------------------------------------------------------------------
	/**
	 * check permalink
	 *
	 * @description	check if permalink is used alredy
	 * 
	 */
	function check_permalink($permalink, $id = null)
	{
		if( $id == null )
		{
			$id = $this->input->post('post_id');
		}
		$permalink = '/'.trim(trim($permalink),'/');
		//
		$entry = db_select(config('system/current/db_prefix').config('db_entries'), array('permalink' => $permalink), array('json' => array('data'), 'single' => true));
		if( $entry != FALSE )
		{
			if( $entry['id'] == $id )
			{
				return "false";
			}
			return "true";
		}
		return "false";
	}
	// --------------------------------------------------------------------
	/**
	 * create
	 *
	 * @description	creates empty entry and returns id
	 * 
	 */
	function create( $url )
	{
		// preset type
		$type = 1;
		// get types
		$types = config('entry_types');
		// check if type is set
		foreach( $types as $key => $_type )
		{
			if( $_type['name'] == $url )
			{
				$type = $key;
			}
		}
		// check if id exists
		if( $this->session->userdata('id') )
		{
			// retrieve entry 
			$entry = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $this->session->userdata('id')), 
			array('json' => TRUE, 'single' => TRUE));
			// if entry exists update
			if( isset($entry) && isset($entry['id']) && is_array($entry) )
			{
				db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['id']), array('type' => $type));
				return $entry['id'];
			}
		}
		// if entry does not exists -> create
		$data = array('type' => $type, 'status' => 0);
		db_insert(config('system/current/db_prefix').config('db_entries'), $data);
		$this->session->set_userdata(array('id' => $this->db->insert_id()));
		//
		return $this->db->insert_id();
	}
	// --------------------------------------------------------------------
	/**
	 * delete
	 *
	 * @description	delete entry from db
	 * 
	 */
	function delete( $id = null )
	{
		if( $id != null && $this->input->post('status') == 3 )
		{
			db_delete(config('system/current/db_prefix').config('db_entries'), array('id' => $id) );
		}
		elseif( $id != null )
		{
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('status' => '3'));
		}
		// return id
		return $id;
	}
	// --------------------------------------------------------------------
	/**
	 * trash
	 *
	 * @description	trash delete entry from db
	 * 
	 */
	function trash( $type = null )
	{
		if( $type != null )
		{
			db_delete(config('system/current/db_prefix').config('db_entries'), array('type' => $type, 'status' => '3' ) );
		}
	}
	// --------------------------------------------------------------------
	/**
	 * clean_up
	 *
	 * @description	
	 * 
	 */
	function clean_up()
	{
		if( $this->input->post('id') != null && $this->input->post('title') == '' && ( $this->input->post('text') == '' || $this->input->post('text') == 'Your content here' ) )
		{
			db_delete(config('system/current/db_prefix').config('db_entries'), array('id' => $this->input->post('id')) );
		}
		else
		{
			db_delete(config('system/current/db_prefix').config('db_entries'), array('status' => '0') );
		}
		return TRUE;
	}
	// --------------------------------------------------------------------
	/**
	 * social_images
	 *
	 * @description	adds or removes image from social media channel
	 * 
	 */
	function social_images( $id )
	{
		if( $id != null )
		{
			$imgs = $this->input->post('social_images');
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('data/social_images' => $imgs), TRUE, array('data'));
			// check if 
			if( array_key_exists('news', $imgs) )
			{
				// get entry from db
				$entry = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('json' => array('data'), 'single' => TRUE));
				//
				if( isset($entry['homepage']) && $entry['homepage'] != null )
				{
					if( $imgs['news'] == false || $imgs['news'] == 'false' )
					{
						$homepage = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('json' => array('data'), 'single' => TRUE));
						if( $homepage['text'] != null || $homepage['title'] != null )
						{
							// update block
							db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('data/image' => null), TRUE, array('data'));
						}
						else
						{
							// delte block
							db_delete(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']) );
							// update entry
							db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('data/homepage' => null), TRUE, array('data'));
						}
					}
					else
					{
						// update block
						db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('data/image' => $imgs['news']), TRUE, array('data'));
					}
				}
				else
				{
					// create block
					$homepage = db_insert(config('system/current/db_prefix').config('db_entries'), array('type' => config('homepage_type'),
					'data' => array('column' => '0'), 'status' => 1, 'title' => '', 'text' => '', 'data' => array('image' => $imgs['news'])));
					// update entry
					db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('data/homepage' => $homepage), TRUE, array('data'));
				}
			}
		}
		return $id;
	}
	// --------------------------------------------------------------------
	/**
	 * sort
	 *
	 * @description	updates position of items
	 * 
	 */
	function sort()
	{
		foreach( $this->input->post('items') as $item => $position )
		{
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $item), array('data/position' => $position), TRUE, array('data'));	
		}
	}
	// --------------------------------------------------------------------
	/**
	 * sort_all
	 *
	 * @description	sort all items of given type and return last+1
	 * 
	 */
	function sort_all( $type = null )
	{
		if( $type != null )
		{
			// retrieve items 
			$items = db_select(config('system/current/db_prefix').config('db_entries'), array('type' => $type, 'status' => array('1','2') ), 
			array('json' => TRUE, 'single' => FALSE));
			//
			if( isset($items) && is_array($items) && isset($items[key($items)])  && count($items) >= 1 )
			{
				//
				foreach( $items as $key => $item )
				{
					if( isset($item['position']) && $item['position'] != null )
					{
						$positioned[$item['position']] = $item;
					}
					else
					{
						$unpositioned[] = $item;
					}
				}
				ksort($positioned);
				$array = array_values($positioned);
				// merge arrays
				if( isset($unpositioned) )
				{
					foreach( $unpositioned as $item )
					{
						$array[] = $item;
					}
				}
				// set position
				$position = 1;
				foreach( $array as $key => $item )
				{
					db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $item['id']), array('data/position' => $position), TRUE, array('data'));	
					$position++;
				}
				// return last position +1 
				return $position;
			}
			return 1;
		}
		return false;
	}
	// --------------------------------------------------------------------
}

/* End of file content.php */
/* Location: ./application/controllers/content.php */