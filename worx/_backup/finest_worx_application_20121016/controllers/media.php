<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Media extends MY_Controller {

	public function index( $method = null, $id = null, $thumb = null )
	{	
		if( method_exists($this,$method) )
		{
			$this->$method( $id, $thumb );
		}
		else
		{
			$this->overview();
		}
	}
	// --------------------------------------------------------------------
	/**
	 * upload
	 *
	 * @description	upload file & move to dir
	 * 
	 */
	function upload( $id = null )
	{
		if( strtolower($_SERVER['REQUEST_METHOD']) != 'post' )
		{
			exit_status('Error! Wrong HTTP method!');
		}
		// check if any image is added
		if( array_key_exists('pic', $_FILES) && $_FILES['pic']['error'] == 0 )
		{
			$file = $_FILES['pic'];
			// prepare file name
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
			$file['filename'] = $filename;
			//
			if(move_uploaded_file($file['tmp_name'], config('system/current/dir').'/'.config('upload_image_dir').'/'.$file['filename'].'.'.$file['ext']))
			{
				$this->create_thumb($file['filename'], $file['ext'], config('system/current/dir').'/'.config('upload_image_dir').'/');
				// insert into DB and return id
				$image_id = db_insert(config('db_files'), 
					array('filename' => $file['filename'], 'data' => array('ext' => $file['ext'])), array('data'));
				// add to current entry
				if( $id != null )
				{
					db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), 
					array('data/images' => array($image_id)), TRUE, array('data'));
				}
				// exit with message
				exit_status(array('status' => 'File was uploaded successfuly!', 'id' => $image_id));
			}
		}
		// error
		exit_status('Something went wrong with your upload!');
	}
	// --------------------------------------------------------------------
	/**
	 * delete
	 *
	 * @description	delete files from entry
	 * 
	 */
	function delete( $id = null )
	{
		// get image id
		$image_id = $this->input->post('image_id');
		// check if id is set
		if( isset($image_id) && $image_id != null )
		{
			// get entry
			$data = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $id), array('select' => 'data', 'json' => array('data'), 
			'single' => TRUE));
			// remove image from post
			$key = array_search($image_id, $data['images']);
			unset($data['images'][$key]);
			$update['data'] = array('images' => $data['images']);
			// update db
			db_update(config('system/current/db_prefix').config('db_entries'), array('id' => $id), $update, TRUE, array('data'));
		}
	}
	// --------------------------------------------------------------------
	/**
	 * image settings
	 *
	 * @description	add uploaded edit to db
	 * 
	 */	
	function image_settings( $id )
	{
		$image = db_select(config('db_files'), array('id' => $id), array('json' => array('data'), 'single' => TRUE));
		echo $this->load->view('media/image_settings', $image, TRUE);
	}
	// --------------------------------------------------------------------
	/**
	 * image_to_homepage
	 *
	 * @description display image as banner on homepage
	 * 
	 */
	 function image_to_homepage()
	 {
		 $id = $this->input->post('id');
		 // retrieve from db
		 $update = db_select(config('system/current/db_prefix').config('db_data'), array('type' => 'banner'), array('json' => array('data'), 'single' => TRUE, 'unstack' => FALSE));
		 $update = $update['data'];
		 $exists = 'false';
		 foreach( $update as $key => $value)
		 {
			 if( $value['id'] == $id )
			 {
			 		$update[$key] = array('id' => $id, 'caption' => variable($this->input->post('value')), 'link' => $this->input->post('link'));
					$exists = 'true';
			 }
		 }
		 // update data
		 if( $exists != 'true' )
		 {
			 $update[$id] = array('id' => $id, 'caption' => variable($this->input->post('value')), 'link' => $this->input->post('link'));
		 }
		 // update config
		 db_update(config('system/current/db_prefix').config('db_data'), array('type' => 'banner'), array('data' => json_encode($update)));
		 //
		 echo json_encode(array('success' => 'saved'));
	 }
	// --------------------------------------------------------------------
	/**
	 * remove_image_homepage
	 *
	 * @description remove image from banner on homepage
	 * 
	 */
	 function remove_image_homepage()
	 {
		 $id = $this->input->post('id');
		 // retrieve from db
		 $update = db_select(config('system/current/db_prefix').config('db_data'), array('type' => 'banner'), array('json' => array('data'), 'single' => TRUE, 'unstack' => FALSE));
		 $update = $update['data'];
		 // update data
		 foreach($update as $key => $value)
		 {
			 if($value['id'] == $id)
			 {
				 unset($update[$key]);
			 }
		 }
		 // update config
		 db_update(config('system/current/db_prefix').config('db_data'), array('type' => 'banner'), array('data' => json_encode($update)));
	 }
	// --------------------------------------------------------------------
	/**
	 * edit image
	 *
	 * @description	add uploaded edit to db
	 * 
	 */	
	function edit_image()
	{
		$old_filename = db_select(config('db_files'), array('id' => $this->input->post('id')), array('json' => array('data'), 'single' => TRUE));
		// create filename
		$_filename = $filename = to_alphanum($this->input->post('value'), array(' ' => '-', '_' => '_'));
		if( $old_filename['filename'] != $filename )
		{
			// check filename
			while( db_select(config('db_files'), array('filename' => $filename), array('select' => array('filename'), 'single' => TRUE)) )
			{
				$filename = $_filename.'-'.rand(0,99);	
			}
			// update db
			db_update(config('db_files'), array('id' => $this->input->post('id')), array('filename' => $filename), TRUE, array('data'));
			// dir
			$dir = config('system/current/dir').'/'.config('upload_image_dir').'/';
			// rename original file
			rename($dir.$old_filename['filename'].'.'.$old_filename['ext'], $dir.$filename.'.'.$old_filename['ext']);
			// rename thumbs
			foreach( config('thumbs') as $key => $thumb )
			{
				// rename
				rename($dir.$old_filename['filename'].'_'.$key.'.'.$old_filename['ext'], $dir.$filename.'_'.$key.'.'.$old_filename['ext']);
			}
		}
		echo json_encode(array('success' => true, 'filename' => $filename));
		
	}
	// --------------------------------------------------------------------
	/**
	 * replace image
	 *
	 * @description	add uploaded edit to db
	 * 
	 */	
	function replace_image($id, $thumb)
	{
		// if file is transmitted
		if( array_key_exists('file', $_FILES) && $_FILES['file']['error'] == 0 )
		{
			// get file from db
			$db_file =  db_select(config('db_files'), array('id' => $id), array('json' => array('data'), 'single' => TRUE));
			// get transfered file
			$file = $_FILES['file'];
			// replace file
			move_uploaded_file($file['tmp_name'], config('system/current/dir').'/'.config('upload_image_dir').'/'.$db_file['filename'].'_'.$thumb.'.'.$db_file['ext']);
			// resize image if needed
			$img = getimagesize(config('system/current/dir').'/'.config('upload_image_dir').'/'.$db_file['filename'].'_'.$thumb.'.'.$db_file['ext']);
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
				// $this->setMemoryForImage(config('system/current/dir').'/'.config('upload_image_dir').'/'.$db_file['filename'].'_'.$thumb.'.'.$db_file['ext']);
				$config['image_library'] = 'gd2';
				$config['source_image'] = config('system/current/dir').'/'.config('upload_image_dir').'/'.$db_file['filename'].'_'.$thumb.'.'.$db_file['ext'];
				$config['quality'] = '100';
				// load config to lib
				$this->load->library('image_lib');	
				// image is higher than wide
				if( $img_small == 'width' )
				{
					$config['width'] 	= $size['width'];
					$config['height'] = $img['height']/($img['width']/$size['width']);
					// check dimensions
					while( $config['height'] < $size['height'])
					{
						$config['width'] = intval($config['width']) + 50;
						$config['height'] = $img['height']/($img['width']/$config['width']);
					}
				}
				// image is wider than high
				else
				{
					$config['height'] = $size['height'];
					$config['width'] 	= $img['width']/($img['height']/$size['height']);
					// check dimensions
					while( $config['width'] < $size['width'])
					{
						$config['height'] = intval($config['height'])+50;
						$config['width'] = $img['width']/($img['height']/$config['height']);
					}
				}
				// init lib
				$this->image_lib->clear();
				$this->image_lib->initialize($config);
				//resize
				$this->image_lib->resize();
				// set crop proportions
				$config['source_image'] = config('system/current/dir').'/'.config('upload_image_dir').'/'.$db_file['filename'].'_'.$thumb.'.'.$db_file['ext'];
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
			}
			// send result
			echo true;
		}
		else
		{
			echo json_encode(array('error' => 'File could not be uploaded.'));
		}
	}
	// set memory
	function setMemoryForImage( $filename )
	{
		$this->image_lib->clear();
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
	// --------------------------------------------------------------------
	/**
	 * create_thumb
	 *
	 * @description	create_thumbs from config['thumbs']
	 * 
	 */
	function create_thumb($file, $ext, $dir)
	{
		// set configs 
		$config['image_library'] = 'gd2';
		$config['source_image'] = $dir.$file.'.'.$ext;
		$config['quality'] = '100';
		// load config to lib
		$this->load->library('image_lib');
		// get image proportions
		// $this->setMemoryForImage($dir.$file.'.'.$ext);
		$image = getimagesize($config['source_image']);
		$img['width'] = $image_width = $image[0];
		$img['height'] = $image_height = $image[1];
		// get thumb sizes
		foreach( config('thumbs') as $name => $sizes )
		{
			// if not, resize
			$small = ( $sizes['width'] >= $sizes['height'] ? 'height' : 'width');
			$img_small = ( $img['width'] >= $img['height'] ? 'height' : 'width');
			//
			$config['source_image'] = $dir.$file.'.'.$ext;
			$config['maintain_ratio'] = TRUE;
			// set name for thumb
			$config['new_image'] = $file.'_'.$name.'.'.$ext;
			// check proportions
			// ---
			// image is higher than wide
			if( $img_small == 'width' )
			{
				$config['width'] 	= $sizes['width'];
				$config['height'] = $img['height']/($img['width']/$sizes['width']);
				// check dimensions
				while( $config['height'] < $sizes['height'])
				{
					$config['width'] = intval($config['width']) + 50;
					$config['height'] = $img['height']/($img['width']/$config['width']);
				}
			}
			// image is wider than high
			else
			{
				$config['height'] = $sizes['height'];
				$config['width'] 	= $img['width']/($img['height']/$sizes['height']);
				// check dimensions
				while( $config['width'] < $sizes['width'])
				{
					$config['height'] = intval($config['height'])+50;
					$config['width'] = $img['width']/($img['height']/$config['height']);
				}
			}
			// init lib
			$this->image_lib->clear();
			$this->image_lib->initialize($config);
			//resize
			$this->image_lib->resize();
			// set crop proportions
			$config['source_image'] = $dir.$file.'_'.$name.'.'.$ext;
			$config['width'] = $sizes['width'];
			$config['height'] = $sizes['height'];
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
		return TRUE;
	}
}
/* End of file media.php */