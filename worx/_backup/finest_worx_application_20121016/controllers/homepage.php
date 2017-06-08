<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Homepage extends MY_Controller {

	public function index( $method = null, $id = null, $thumb = null )
	{	
		if( $method == null )
		{
			css_add('homepage');
			js_add('fs.gui, fs.sortable, homepage');		
			// load items from database
			$blocks = db_select(config('system/current/db_prefix').config('db_entries'), array('type' => config('homepage_type')), 
				array('json' => 'data'));
			// build blocks
			$columns = 3;
			$images = array();
			// sort into columns
			foreach( $blocks as $block)
			{
				$i = 0;
				// check $i
				if( isset($block['column']) && $block['column'] < $columns)
				{
					$i = $block['column'];
				}
				//
				$tmp_blocks[$i][$block['id']] = $block;
				// add image if not in array
				(isset($block['image']) && $block['image'] != null && !in_array($block['image'], $images)) ? $images[] =  $block['image'] : '';
			}
			$images = array_filter($images);
			// if images exisit, fetch
			if( isset($images) && is_array($images) && count($images) > 0)
			{
				// retrieve images from db
				$images = db_select(config('db_files'), array('id' => $images), array('json' => array('data'), 'single' => FALSE, 'index' => 'id', 'index_single' => TRUE));
			}
			//
			$blocks = $tmp_blocks;
			foreach($blocks as $column => $b)
			{
				// sort element
				$b = $this->sort_elements($b, 'position');
				//
				foreach($b as $pos => $block)
				{
					if(!isset($block['position']) || $pos != $block['position'])
					{
						db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $block['id']), array('data' => array('position' => $pos+1)), TRUE, array('data'));
					}
					// if image exists
					if(isset($block['image']) && $block['image'] != null && $block['image'] != false && $block['image'] != 'false')
					{
						// add image if exits
						$block['image'] = $images[$block['image']];
					}
					else
					{
						$block['image'] = null;
					}
					// render block
					$_blocks[$column][$pos] = $this->load->view('homepage/block', $block, TRUE);
				}
			}
			//
			$this->data['content'] = '';
			//
			for( $i = 0; $i < $columns; $i++)
			{
				$content = '';
				// merge blocks
				if( isset($_blocks[$i]) || isset($rest[$i]) )
				{
					!isset($_blocks[$i]) ? $_blocks[$i] = array() : '';
					!isset($rest[$i]) ? $rest[$i] = array() : '';			
					$_blocks[$i] = array_merge($_blocks[$i], $rest[$i]);
				}
				//
				if( isset($_blocks[$i]) && is_array($_blocks[$i]) )
				{
					$content = '<div class="parent" style="display:none;">
						<div class="add-block column-add" data-pos="1">
							<span class="add">add block</span></div></div>'.implode($_blocks[$i], '');
				}
				else
				{
					$content = '<div class="parent"><div class="add-block column-add" data-pos="1"><span class="add">add block</span></div></div>';
				}
				$this->data['content'] .= '<div class="column" data-column="'.$i.'">'.$content.'</div>';
			}
			// load into template
			view('default', $this->data);
		}
		else
		{
			$this->$method($id, $thumb);
		}
	}
	// -----------------------------------
	// create block
	function create_block()
	{
		$block['id'] = db_insert(config('system/current/db_prefix').config('db_entries'), array('type' => config('homepage_type'),
		'data' => array('column' => $this->input->post('column')), 'status' => 1));
		$block['title'] = '';
		$block['text'] = '';
		//
		echo $this->load->view('homepage/block', $block, TRUE);
	}
	// -----------------------------------
	// delete block
	function delete_block( )
	{
		$id = $this->input->post('id');
		// fetch block from db
		$block = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('single' => TRUE));
		// check get image
		$image = db_select(config('db_files'), array('id' => $block['image']), array('single' => TRUE));
		// check if image is block only
		if( $image['status'] == 4 )
		{
			// delete image
			db_delete( config('db_files'), array('id' => $image['id']) );
		}
		// delete block
		db_delete( config('system/current/db_prefix').config('db_entries'), array('id' => $id) );
	}
	// -----------------------------------
	// edit block
	function edit_block( )
	{
		// get post data
		$data = db_prepare_data(array('title','text','data' => array('position','column'),'id', 'value', 'key'), FALSE);
		// clean post data
		foreach($data as $key => $val)
		{
			if( $val == '')
			{
				unset($data[$key]);
			}
		}
		// check if id is given
		if( isset($data['id']) )
		{
			// check if position is sent (item moved)
			if( isset($data['data']['position']) && $data['data']['position'] != '' )
			{
				// get blocks from db and index by id
				$blocks = db_select(config('system/current/db_prefix').config('db_entries'), array('type' => config('homepage_type')), array('index' => 'id', 'index_single' => TRUE));
				// get columns
				$columns = json_decode($this->input->post('columns'), TRUE);
				// loop through columns
				foreach($columns as $c_id => $column)
				{
					if( !isset($done) || !in_array($c_id, $done) )
					{
						$done[] = $c_id;

						foreach($column as $pos => $block)
						{
							if( $pos+1 != $blocks[$block['id']]['position'] || $c_id != $blocks[$block['id']]['column'] )
							{
								db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $block['id']) , array('column' => $c_id, 'position' => $pos+1), TRUE, array('data'));
							}
						}
					}
				}
			}
			// item edited but not moved
			else
			{
				// update db
				db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $data['id']), array($data['key'] => $data['value']), TRUE, array('data'));
				echo json_encode(array('success' => 'saved'));
			}
		}
	}
	// -----------------------------------
	// delete_image
	function delete_image()
	{
		$id = $this->input->post('id');
		// get block from db
		$block = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('single' => TRUE));
		// get image from db
		$image = db_select(config('db_files'), array('id' => $block['image']), array('single' => TRUE));
		// if block only image -> delete
		if( $image['status'] == 4 )
		{
			unlink(config('system/current/dir').'/'.config('upload_image_dir').'/'.$image['filename'].'_'.config('homepage_thumb').'.'.$image['ext']);
			db_delete(config('db_files'), array('id' => $block['image']));
		}
		// update block
		if( isset($id) && $id != '' )
		{
			$delete = null;
			if( $block['title'] != null || $block['text'] != null )
			{
				db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('data/image' => null), TRUE, array('data'));
			}
			else
			{
				db_delete(config('system/current/db_prefix').config('db_entries'), array('id' => $id));
				$delete = 'true';
			}
			echo json_encode(array('success' => true, 'delete' => $delete));
		}
	}
	// -----------------------------------
	// sort elements
	function sort_elements($elements, $index)
	{
		if( isset($elements) )
		{
			$indizies = array();
			foreach($elements as $id => $element)
			{
				if( isset($element[$index]) && $element[$index] != '' && !in_array($element[$index], $indizies) )
				{
					$output[$element[$index]] = $element;
					$indizies[] = $element[$index];
				}
				else
				{
					$rest[] = $element;
				}
			}
			//
			if( isset($rest) && isset($output))
			{
				$output = array_merge($output, $rest);
			}
			elseif( isset($rest) && !isset($output) )
			{
				$output = $rest;
			}
			ksort($output);
			//
			return $output;
		}
	}
	// -----------------------------------
	// sort block
	function sort_blocks( )
	{
		// get blocks from db and index by id
		$blocks = db_select(config('system/current/db_prefix').config('db_entries'), array('type' => config('homepage_type')), array('index' => 'id', 'index_single' => TRUE));
		// get columns
		$columns = json_decode($this->input->post('columns'), TRUE);
		// loop through columns
		foreach($columns as $c_id => $column)
		{
			if( !isset($done) || !in_array($c_id, $done) )
			{
				$done[] = $c_id;

				foreach($column as $pos => $block)
				{
					if( $pos+1 != $blocks[$block['id']]['position'] || $c_id != $blocks[$block['id']]['column'] )
					{
						db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $block['id']) , array('column' => $c_id, 'position' => $pos+1), TRUE, array('data'));
					}
				}
			}
		}
	}
	// -----------------------------------
	// upload image
	function upload_image($id, $thumb)
	{
		// if file is transmitted
		if( array_key_exists('file', $_FILES) && $_FILES['file']['error'] == 0 )
		{
			// get transfered file
			$file = $_FILES['file'];
			$file['ext'] = strtolower(substr(strrchr($file['name'], '.'), 1));
			$file['name'] = trim(substr($file['name'], 0, strrpos($file['name'],'.')));
			$filename = $file['filename'] = to_alphanum($file['name'], array(' ' => '-', '_' => '_'));
			// check if format is allowed
			if( !in_array( $file['ext'], config('upload_image_types')) )
			{
				exit_status('Only '.implode(', ',config('upload_image_types')).' files are allowed!');
			}
			// move image to upload dir
			while( file_exists(config('system/current/dir').'/'.config('upload_image_dir').'/'.$filename.'.'.$file['ext']) )
			{
				$filename = $file['filename'].'-'.rand(0,99);
			}
			// replace file
			move_uploaded_file($file['tmp_name'], config('system/current/dir').'/'.config('upload_image_dir').'/'.$filename.'_'.$thumb.'.'.$file['ext']);
			// resize image if needed
			$img = getimagesize(config('system/current/dir').'/'.config('upload_image_dir').'/'.$filename.'_'.$thumb.'.'.$file['ext']);
			$size = config('thumbs/'.$thumb);
			// check if size is correct
			if($img[0] != $size['width'] || $img[1] != $size['height'])
			{ 
				$img['width'] = $img[0];
				$img['height'] = $img[1];
				// if not, resize
				$small = ( $size['width'] >= $size['height'] ? 'height' : 'width');
				$img_small = ( $img['width'] >= $img['height'] ? 'height' : 'width');
				// set configs 
				// $this->setMemoryForImage(config('system/current/dir').'/'.config('upload_image_dir').'/'.$filename.'_'.$thumb.'.'.$file['ext']);
				$config['image_library'] = 'gd2';
				$config['source_image'] = config('system/current/dir').'/'.config('upload_image_dir').'/'.$filename.'_'.$thumb.'.'.$file['ext'];
				$config['quality'] = '100';
				// load config to lib
				$this->load->library('image_lib');	
				// image is higher than wide
				if( $img_small == 'width' )
				{
					$config['width'] 	= $size['width'];
					$config['height'] 	= $img['height']/($img['width']/$size['width']);
				}
				// image is wider than high
				else
				{
					$config['height'] 	= $size['height'];
					$config['width'] 	= $img['width']/($img['height']/$size['height']);
					
				}
				// init lib
				$this->image_lib->clear();
				$this->image_lib->initialize($config);
				//resize
				try {
					$this->image_lib->resize();
				}catch (Exception $e){
					throw $e;
				}
				// set crop proportions
				$config['source_image'] = config('system/current/dir').'/'.config('upload_image_dir').'/'.$filename.'_'.$thumb.'.'.$file['ext'];
				$config['width'] = $size['width'];
				$config['height'] = $size['height'];
				$config['maintain_ratio'] = FALSE;
				$config['x_axis'] = '0';
				$config['y_axis'] = '0';
				// init lib
				$this->image_lib->clear();
				$this->image_lib->initialize($config);
				// crop
				$this->image_lib->crop();
				$this->image_lib->clear();
			}
			$image_id = db_insert(config('db_files'), array('status' => '4', 'filename' => $filename, 'data' => array('ext' => $file['ext'])) );
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id) , array('data' => array('image' => $image_id)), TRUE, array('data'));
			// send result
			echo json_encode(array('success' => 'true', 'id' => $image_id, 'dir' => config('display_image_dir').'/'.$filename.'_'.$thumb.'.'.$file['ext']));
		}
		else
		{
			echo json_encode(array('error' => 'File could not be uploaded.'));
		}
	}
	// -----------------------------------	
	// set memory
	function setMemoryForImage( $filename )
	{
		$imageInfo = getimagesize($filename);
		$memoryLimitMB = 0;
		$MB = 1048576;  // number of bytes in 1M
		$K64 = 65536;    // number of bytes in 64K
		$TWEAKFACTOR = 8;  // Or whatever works for you
		$memoryNeeded = round( ( $imageInfo[0] * $imageInfo[1]
	                                           * $imageInfo['bits']
	                                           * (isset($imageInfo['channels']) ? $imageInfo['channels'] / 8 : 1)
	                             + $K64
	                           ) * $TWEAKFACTOR
	                         );
	    //ini_get('memory_limit') only works if compiled with "--enable-memory-limit" also
	    //Default memory limit is 8MB so well stick with that. 
	    //To find out what yours is, view your php.ini file.
	    $memoryLimit = 8 * $MB;
	    if (function_exists('memory_get_usage') && 
	        memory_get_usage() + $memoryNeeded > $memoryLimit) 
	    {
	        $newLimit = $memoryLimitMB + ceil( ( memory_get_usage()
	                                            + $memoryNeeded
	                                            - $memoryLimit
	                                            ) / $MB
	                                        );
	        ini_set( 'memory_limit', $newLimit . 'M' );
	        return true;
	    }
	    else{
	        return false;
	    }
	}
}