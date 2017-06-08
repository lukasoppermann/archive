<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Content extends MY_Controller {

	public function index( $method = null, $id = null )
	{	
		if( method_exists($this,$method) )
		{
			if( $this->input->post('ajax') == true || $this->input->post('ajax') == 'true' )
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
		$entries = db_select( config('system/current/db_prefix').config('db_entries'), array('type' => array_keys($types)), array('json' => 'data') );
		// check entry status
		foreach( $entries as $id => $entry )
		{
			if( trim($entry['permalink']) == '' || trim($entry['permalink']) == null )
			{
				$update_ids[] = $entry['id'];
				// update status
				$entries[$id]['status'] = '2';
			}
		}
		// if any item needs update, update
		if( isset($update_ids) && is_array($update_ids) && count($update_ids) > 0 )
		{
			// set entries to draft
			db_update( config('system/current/db_prefix').config('db_entries'), array('id' => $update_ids), array('status' => '2') );
		}
		// sort entries
		$entries = index_array($entries, 'type', TRUE);
		// list entries	
		if( isset($entries) && is_array($entries) )
		{
			foreach( $entries as $key => $list )
			{
				$c = 0;
				foreach($list as $entry)
				{
					if(!isset($entry['position']))
					{
						$entry['position'] = $c;
					}
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
						if( isset( $group[$key][$entry['position']] ) ){ $c++; }
						$group[$key][$entry['position'] + $c] = $this->load->view('content/entry_item', $entry, TRUE);
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
		}
		// define columns
		$column_one = (isset($column[0]) ? implode('',$column[0]) : '');
		$column_two = (isset($column[1]) ? implode('',$column[1]) : '');
		// ------------------------
		$this->data['content'] = '<div class="column">'.$column_one.'</div><div class="column">'.$column_two.'</div>';
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
			$type = $id;
			$id = $this->create( $id );
		}
		elseif( $this->input->post('id') != null && $id == null )
		{
			$id = $this->input->post('id');
		}
		
		// -------------------
		// fetch data from db
		// list entries
		$types = config('entry_types');
		$conf = config('links');
		// -------------------
		// get data from db
		$entry = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('json' => array('data'), 'single' => true));
		if( !isset($entry['id']) )
		{
			$entry['id'] 		= $id;
		}
		// get all entries
		$entries = db_select(config('system/current/db_prefix').config('db_entries'), array('type' => '2', 'status' => '1'), 
				array('json' => 'data', 'index' => 'id', 'index_single' => true));
		//
		if( isset($entries) )
		{
			foreach( $entries as $key => $e )
			{
				if( isset($e['product_type']) )
				{
					$_entries[$e['product_type']][$e['id']] = $e;
				}
			}
		}
		if( isset($_entries) )
		{
			$entries = $_entries;
			// set boats
			$max_slots = 0;
		
			if( isset($entry['boat']) && isset($entries['1'][$entry['boat']]['slots']) )
			{
				$entry['count_boats'] = $entries['1'][$entry['boat']]['slots']*$entries['1'][$entry['boat']]['rows'];
			}
			else
			{
				if( isset($entries['1']) )
				{
					$entry['count_boats'] = $entries['1'][key($entries['1'])]['slots']*$entries['1'][key($entries['1'])]['rows'];
				}
				else
				{
					$entry['count_boats'] = 0;
				}
			}
			if( isset($entries['1']) )
			{
				foreach($entries['1'] as $key => $boat)
				{
					//
					if( ($boat['slots']*$boat['rows']) > $max_slots )
					{
						$max_slots = $boat['slots']*$boat['rows'];
					}
					//
					$entry['boats'][$key]['name'] = $boat['title'];
					$entry['boats'][$key]['data'] = 'data-slots="'.$boat['slots']*$boat['rows'].'"';
				}
			}
			// set modules
			$entry['choose_module'] = '';
			if( isset($entries['2']) )
			{
				foreach( $entries['2'] as $k => $mod )
				{
					$modules[$mod['id']] = $mod;
					$entry['choose_module'] .= "<li class='choose-module' data-slots='".$mod['slots']."' data-module='".$mod['id']."'>".$mod['title']."</li>";
				}
			}
			//
			$entry['count_modules'] = 0;
			//
			if( isset($entry['modules']) )
			{
				foreach( $entry['modules'] as $pos => $val  )
				{
					if( isset($modules[$val]) )
					{
						$entry['active_modules'][$pos] = "<li class='module' data-slots='".$modules[$val]['slots']."' data-module='".$modules[$val]['id']."'>
							<span class='label'>".$modules[$val]['title']."</span>
						<span class='close'>×</span></li>";
						$entry['count_modules'] += $modules[$val]['slots'];
					}
				}
				$i = $entry['count_modules'];
				//
				while( $i < $max_slots )
				{
					if($entry['count_boats'] > $i)
					{
						$entry['active_modules'][$i+1] = "<li class='module empty'><span class='label'>Add module</span><span class='close'>×</span></li>";
					}
					else
					{
						$entry['active_modules'][$i+1] = "<li class='module empty' style='display:none;'><span class='label'>Add module</span><span class='close'>×</span></li>";
					}
					$i++;
				}
				//
			}
			else
			{
				for( $i=0; $i < $max_slots; $i++ )
				{
					if($entry['count_boats'] > $i)
					{
						$entry['active_modules'][$i+1] = "<li class='module empty'><span class='label'>Add module</span><span class='close'>×</span></li>";
					}
					else
					{
						$entry['active_modules'][$i+1] = "<li class='module empty' style='display:none;'><span class='label'>Add module</span><span class='close'>×</span></li>";
					}
				}
			}
			if( isset($entry['active_modules']) )
			{
				$entry['active_modules'] = '<ul class="active-modules">'.implode('',$entry['active_modules']).'</ul>';
			}
			else
			{
				$entry['active_modules'] = '<ul class="active-modules"></ul>';
			}
		}
		//
		if( isset($entry['homepage']) )
		{
			$entry['homepage'] = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('json' => array('data'), 'single' => true));
		}
		// 
		if( isset($entry) && isset($entry['id']) && $entry['id'] == null )
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
				db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['id']), array('data/social_images/news' => false), array('merge' => TRUE, 'json' => array('data')));
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
		foreach(config('entry_types') as $key => $_type)
		{
			$entry['types'][$key] = $_type['label'];
			if( isset($type) && $_type['name'] == $type )
			{
				$entry['type'] 	= $key;
			}
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
		if( isset($entry['type']) && isset($entry['permalink']) )
		{
			if( isset($entry['type']) && $entry['type'] == 2 && !isset($entry['product_type']) ){ $entry['product_type'] = 1; }
			$entry['link'] = '';
			if( isset($conf[$entry['type']]) && trim($conf[$entry['type']]['name'],'/') != '' )
			{
				$entry['link'] .= '/'.trim($conf[$entry['type']]['name'],'/');
			}
			$entry['link'] .= ( (isset($entry['type']) && $entry['type'] == 2) ? '/'.config('product_type/'.$entry['product_type'].'/path') : '');
			// add slash to end of link
			if($entry['link'] != null){ $entry['link'] = $entry['link'].'/'; }
			// add base url
			$entry['link'] = trim(config('system/current/base_url'),'/').'/'.ltrim($entry['link'],'/').trim($entry['permalink'],'/');
		}
		// check status
		if(!isset($entry['status']) || $entry['status'] == '0')
		{
			$entry['status'] = '2';
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
		$conf = config('links');
		$data = db_prepare_data(array('id','menu_id', 'permalink', 'tags', 'type', 'language', 'status', 'title', 'text', 'data' => array('position','meta_title', 'meta_description', 'preview_text', 'menu_item','facebook','twitter','price','product_type','rows','product_code','boat'),'homepage_text','homepage_title'), FALSE);
		// slots for boat
		if( $data['data']['product_type'] == '1' )
		{
			$data['data']['slots'] = $this->input->post('slots');
		}
		// slots for module
		if( $data['data']['product_type'] == '2' )
		{
			$data['data']['slots'] = $this->input->post('module_slots');
		}
		// social media
		$data['data']['facebook'] = substr($data['data']['facebook'], 0, 410);
		$data['data']['twitter'] = substr($data['data']['twitter'], 0, 110);
		// fetch blocks
		$data['data']['blocks'] = json_decode($this->input->post('blocks'), TRUE);
		// clean block
		foreach( $data['data']['blocks'] as $key => $block)
		{
			$data['data']['blocks'][$key]['title'] = $block['title'];
			$data['data']['blocks'][$key]['content'] = str_replace("\n","<br />",urldecode($block['content']));
		}
		// fetch modules
		$data['data']['modules'] = json_decode($this->input->post('modules'), TRUE);
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
			$data['permalink'] = '/'.trim(to_alphanum($data['permalink'], array(' ' => '-','_' => '-','/' => '/')),'/');
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
		// build link
		$widget_link = '';
		// check for page not being contact
		if( $entry['type'] != 7 )
		{	
			// check if type exists
			if( isset($conf[$data['type']]) && trim($conf[$data['type']]['name'],'/') != '' )
			{
				$widget_link .= '/'.trim($conf[$data['type']]['name'],'/');
			}
			$widget_link .= ( ($data['type'] == 2) ? '/'.config('product_type/'.$data['data']['product_type'].'/path') : '');
			// add slash to end of link
			if($widget_link != null){ $widget_link = $widget_link.'/'; }
			// add base url
			$widget_link = trim(config('system/current/base_url'),'/').'/'.ltrim($widget_link,'/').trim($data['permalink'],'/');
		}
		// if contact page
		else
		{
			$widget_link = trim(config('system/current/base_url'),'/').'/'.'contact';
		}	
		//
		if( isset($entry['homepage']) && ( (isset($data['homepage_title']) && trim($data['homepage_title']) != '') || (isset($data['homepage_text']) && trim($data['homepage_text']) != '') ) )
		{
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('title' => variable($data['homepage_title']), 'text' => variable($data['homepage_text']), 'permalink' => $widget_link), array('merge' => TRUE, 'json' => array('data')));
		}
		elseif( isset($entry['homepage']) && trim($data['homepage_title']) == '' && trim($data['homepage_text']) == '' )
		{
			db_delete(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']) );
			$data['data']['homepage'] = null;
		}
		elseif( !isset($entry['homepage']) && ( (isset($data['homepage_title']) && trim($data['homepage_title']) != '') || (isset($data['homepage_text']) && trim($data['homepage_text']) != '') ) )
		{
			$data['data']['homepage'] = db_insert(config('system/current/db_prefix').config('db_entries'), array('type' => config('homepage_type'),
			'data' => array('column' => '0'), 'status' => 1, 'title' => variable($data['homepage_title']), 'text' => variable($data['homepage_text']), 'permalink' => $widget_link));
		}
		unset($data['homepage_text'], $data['homepage_title']);
		// -------------------
		// post to social media
		if( $data['status'] == 1 )
		{
			$link = '';
			// check for page not being contact
			if( $data['type'] != 7 )
			{
				if( isset($conf[$data['type']]) && trim($conf[$data['type']]['name'],'/') != '' )
				{
					$link .= '/'.trim($conf[$data['type']]['name'],'/');
				}
				$link .= ( ($data['type'] == 2) ? '/'.config('product_type/'.$data['data']['product_type'].'/path') : '');
				// add slash to end of link
				if($link != null){ $link = $link.'/'; }
				// add base url
				$link = trim(config('system/current/base_url'),'/').'/'.ltrim($link,'/').trim($data['permalink'],'/');
			}
			// if contact page
			else
			{
				$link = trim(config('system/current/base_url'),'/').'/'.'contact';
			}
			// get post data
			$post_data = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $this->input->post('id')), array('json' => array('data'), 'single' => TRUE));
			// post to facebook
			if( $data['data']['facebook'] != null && $data['data']['facebook'] != false )
			{
				// default data
				$fbdata = array('name' => $post_data['title'],'link' => $link, 'message' => $data['data']['facebook']);
				// add image
				if( isset($post_data['social_images']['facebook']) && $post_data['social_images']['facebook'] != null )
				{
					// fetch image, get link
					$image = db_select(config('db_files'), array('id' => $post_data['social_images']['facebook']), array('json' => array('data'), 'single' => TRUE));
					$fbdata['image'] = config('system/current/base_url').'/images/'.$image['filename'].'.'.$image['ext'];
					// caption
					$fbdata['caption'] = $post_data['title'];
				}
				// post to facebook
				fs_oauth_post('facebook', $fbdata);
				// remove facebook data
				$data['data']['facebook'] = false;
				$data['social_images']['facebook'] = null;
			}
			// post to twitter
			if( $data['data']['twitter'] != null && $data['data']['twitter'] != false )
			{
				$twdata = array('link' => $link, 'message' => $data['data']['twitter']);
				// post tweet
				fs_oauth_post('twitter', $twdata);
				// remove tweet data
				$data['data']['twitter'] = false;
				$data['social_images']['twitter'] = null;
			}
		}
		// -------------------
		// save to db
		db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $this->input->post('id')), $data, array('create' => TRUE, 'merge' => TRUE, 'json' => array('data')));
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
		// check if type is set
		foreach( config('entry_types') as $key => $_type )
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
			$entry = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('single' => true) );
			// get images
			foreach( $entry['images'] as $image )
			{
				db_delete(config('db_files'), array('id' => $image) );
			}
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
		$type = $this->input->post('deleteType');
		if( $type != null )
		{
			// get all entries
			$entries = db_select(config('system/current/db_prefix').config('db_entries'), array('type' => $type, 'status' => '3' ));
			$banners = db_select( config('system/current/db_prefix').config('db_data'), array('type' => 'banner'), array('select' => 'data', 'single' => true) );
			$banners = index_array($banners, 'id');
			// get images & delete banners
			foreach($entries as $entry)
			{
				if( count($entry['images']) > 0 )
				{
					foreach($entry['images'] as $img)
					{
						if( isset($banners[$img]) )
						{
							unset($banners[$img]);
						}
					}
				}
			}
			if( count($banners) == 0)
			{
				$banners = '';
			}
			// update banners
			db_delete(config('system/current/db_prefix').config('db_data'), array('type' => 'banner'));
			db_insert(config('system/current/db_prefix').config('db_data'), array('type' => 'banner', 'key' => 'settings', 'data' => $banners));
			// delete entries
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
		if( $this->input->post('id') != '' && $this->input->post('id') != null && $this->input->post('title') == '' && ( $this->input->post('text') == '' || $this->input->post('text') == 'Your content here' ) )
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
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('data/social_images' => $imgs), array('merge' => TRUE, 'json' => array('data')));
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
							db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('data/image' => null), array('merge' => TRUE, 'json' => array('data')));
						}
						else
						{
							// delte block
							db_delete(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']) );
							// update entry
							db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('data/homepage' => null), array('merge' => TRUE, 'json' => array('data')));
						}
					}
					else
					{
						// update block
						db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $entry['homepage']), array('data/image' => $imgs['news']), array('merge' => TRUE, 'json' => array('data')));
					}
				}
				else
				{
					// create block
					$homepage = db_insert(config('system/current/db_prefix').config('db_entries'), array('type' => config('homepage_type'),
					'data' => array('column' => '0'), 'status' => 1, 'title' => '', 'text' => '', 'data' => array('image' => $imgs['news'])));
					// update entry
					db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('data/homepage' => $homepage), array('merge' => TRUE, 'json' => array('data')));
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
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $item), array('data/position' => $position), array('merge' => TRUE, 'json' => array('data')));	
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
					db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $item['id']), array('data/position' => $position), array('merge' => TRUE, 'json' => array('data')));	
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