<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media extends MY_Controller {

	public function index($method = null, $id = null)
	{
		if($method == 'edit')
		{
			echo $this->edit($id);
		}
		elseif($method == 'edit_hero')
		{
			$this->edit_hero($id);
		}
		elseif($method == 'delete')
		{
			echo $this->delete($id);
		}
		elseif($method == 'status')
		{
			$this->status($id);
		}
		elseif($method == 'column')
		{
			$this->column($id);
		}
		elseif($method == 'upload')
		{
			$this->file_upload();
		}
		else
		{
			$this->load->model('media_model');
			//
			$this->data['content'] = $this->upload();
			//
			$this->data['content'] .= $this->media_model->get_images();
			// load into template
	        view('default', $this->data);
		}
	}
	// ----------------------------------------------------------------
	// upload images
	function upload()
	{
		$output = '<div class="media-uploader">
				<div class="uploader" id="media_upload" data-dir="'.
				config('client_root', true).config('client_media', true).config('client_images', true).'">
				</div>
    		</div>';
		//
		return $output;
	}
	// ----------------------------------------------------------------
	// delete images
	function delete($id)
	{
		// fetch image
		$this->load->model('media_model');
		$data = $this->media_model->delete($id);
		//return 
		return json_encode(array('success' => TRUE));
	}
	// ----------------------------------------------------------------
	// status images
	function status($id)
	{
		// fetch image
		$this->load->model('media_model');
		$data = $this->media_model->get_image($id);
		// update status
		$data['status'] = $this->input->post('status');
		// jsonify data
		$data['data'] = json_encode($data['data']);
		// update db
		$this->db->where('id',$id);
		$this->db->update('client_files', $data);
		//return 
		return json_encode(array('success' => TRUE));
	}
	// ----------------------------------------------------------------
	// column images
	function column($id)
	{
		// fetch image
		$this->load->model('media_model');
		$data = $this->media_model->get_image($id);
		// update status
		$data['data']['column'] = $this->input->post('column');
		// jsonify data
		$data['data'] = json_encode($data['data']);
		// update db
		$this->db->where('id',$id);
		$this->db->update('client_files', $data);
		//return 
		return json_encode(array('success' => TRUE));
	}
	// ----------------------------------------------------------------
	// edit image name
	function edit($id)
	{
		// ---------------------
		// fetch image
		$this->load->model('media_model');
		$data = $this->media_model->get_image($id);
		// ---------------------
		// edit hero
		$hero = $this->input->post('hero');
		if($hero != false && $hero != 'false' && $hero != '' && isset($hero) )
		{
			$data['data']['link'] 		= $hero;
			$data['data']['eboutique'] 	= FALSE;
			$data['data']['instore'] 	= FALSE;
			$data['key'] 				= 'hero';
		}
		else
		{
			$data['data']['link'] 	= FALSE;
			$data['key'] 			= '';
		}
		// ---------------------
		// if label is changed		
		if($data['data']['label'] != $this->input->post('label'))
		{
			// ---------------------
			// edit label
			$data['data']['label'] = $this->input->post('label');
			$data['data']['alt'] = $this->input->post('label');
			// ---------------------
			// new path
			$_new_path = substr($data['data']['cms_full_path'],0,strrpos($data['data']['cms_full_path'],'/')).'/';
			// ---------------------
			// edit file
			$filename = str_replace(array(' ', '&'), array('-','+'), replace_accents($this->input->post('label')));
			// ---------------------
			while(file_exists($_new_path.$filename.'.'.$data['data']['ext']))
			{
				$filename .= '-'.rand(10, 99);
			}
			//
			$data['data']['filename'] = $filename.'.'.$data['data']['ext'];
			$data['data']['filename_no_ext'] = $filename;			
			$data['data']['full_path'] = substr($data['data']['full_path'],0,
						strrpos($data['data']['full_path'],'/')).'/'.$filename.'.'.$data['data']['ext']; 
			$data['data']['thumb_150_path'] = substr($data['data']['thumb_150_path'],0,
						strrpos($data['data']['thumb_150_path'],'/')).'/'.$filename.'_thumb_150.'.$data['data']['ext']; 			
			// ---------------------
			// rename files
			$rename_files = array(	'file_path' 		=> '',
									'thumb_150' 		=> '_thumb_150',
									'thumb_350' 		=> '_thumb_350',
									'thumb_1column' 	=> '_1column',
									'thumb_2columns' 	=> '_2columns');
			// loop through files
			foreach( $rename_files as $key => $file)
			{
				$old = $data['data'][$key];
				$new = $filename.$file.'.'.$data['data']['ext'];
				// update db
				$data['data'][$key] = $new;
				// rename
				rename($_new_path.$old, $_new_path.$new);
			}
			// ---------------------
			// cms paths
			$data['data']['cms_full_path'] = $_new_path.$filename.'.'.$data['data']['ext'];		
			$data['data']['cms_thumb_150'] = $_new_path.$filename.'_thumb_150.'.$data['data']['ext'];			
			// prep db update
		}
		// jsonify data
		$_data = $data['data'];
		$data['data'] = json_encode($data['data']);
		// update db

		$this->db->where('id',$id);
		$this->db->update('client_files', $data);
		//return 
		return json_encode(array('success' => TRUE, 'filename' => $_data['filename'], 'file' => $_data['cms_full_path'], 'thumb' => $_data['cms_thumb_150']));
	}
	// ----------------------------------------------------------------
	// edit image hero
	function edit_hero($id)
	{
		// fetch image
		$this->load->model('media_model');
		$data = $this->media_model->get_image($id);
		//
		$hero = $this->input->post('hero');
		if($hero == 'eboutique')
		{
			$data['data']['eboutique'] 	= 'TRUE';
			$data['data']['instore'] 	= FALSE;
			$data['data']['link'] 		= FALSE;
			$data['key'] 				= 'hero';
		}
		elseif($hero == 'instore')
		{
			$data['data']['eboutique'] 	= FALSE;
			$data['data']['instore'] 	= 'TRUE';
			$data['data']['link'] 		= FALSE;
			$data['key'] 				= 'hero';
		}
		else
		{
			$data['data']['eboutique'] 	= FALSE;
			$data['data']['instore'] 	= FALSE;
			$data['data']['link'] 		= FALSE;
			$data['key'] 				= '';			
		}
		// jsonify data
		$data['data'] = json_encode($data['data']);
		// clean heros
		if($hero == 'eboutique' || $hero == 'instore')
		{
			// fetch heros
			$heros = $this->media_model->get_heros();
			// loop through heros
			foreach($heros as $_hero)
			{
				if(isset($_hero['data'][$hero]) && $_hero['data'][$hero] != FALSE)
				{
					$_hero['data'][$hero] = FALSE;
					$_hero['key'] = '';
					$_hero['data'] = json_encode($_hero['data']);
					$this->db->where('id',$_hero['id']);
					$this->db->update('client_files', $_hero);
				}
			}
		}
		// update db
		$this->db->where('id',$id);
		$this->db->update('client_files', $data);
	}
	// ----------------------------------------------------------------
	// cleanup images
	function cleanup()
	{
		// cleanup
		$this->load->model('media_model');
		$data = $this->media_model->cleanup();	
	}
	// ----------------------------------------------------------------
	// file_uplaod
	function file_upload()
	{
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array('jpeg','png','gif','jpg');
		// max file size in bytes
		$sizeLimit = 8 * 1024 * 1024;
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload($this->input->get('dir'));
		// to pass data through iframe you will need to encode all html tags
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}
// end of media	
}
// ------------------------------------------------------------------------------------------------------------------------------
// qqUploadeder Functions
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) 
	{    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize())
		{            
            return false;
        }
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

	function getName()
	{
        return $_GET['qqfile'];
    }

	function getSize()
	{
        if (isset($_SERVER["CONTENT_LENGTH"]))
		{
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } 
		else
		{
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}
/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm{  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path))
		{
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader extends MY_Controller  {
    private $allowedExtensions = array();
    private $sizeLimit = 2621440; // 10485760
    private $file;
	private $CI;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){  
		$this->CI =& get_instance();
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();       

        if(isset($_GET['qqfile']))
		{
            $this->file = new qqUploadedFileXhr();
        }elseif(isset($_FILES['qqfile']))
		{
            $this->file = new qqUploadedFileForm();
        }
		else
		{
            $this->file = false; 
        }
    }

    private function checkServerSettings()
	{        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit)
		{
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }

    private function toBytes($str)
	{
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE)
	{
        if (!is_writable($uploadDirectory))
		{
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file)
		{
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0)
		{
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) 
		{
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = str_replace(array(' ', '&'), array('-','+'), replace_accents($_GET['filename']));
		$result['filename'] = $filename = substr($filename,0 ,strrpos($filename,'.'));
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions))
        {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
		// if replace file is false -> do not replace files
        if(!$replaceOldFile)
        {
			// don't overwrite previous files that were uploaded
			while(file_exists($uploadDirectory . $filename . '.' . $ext))
			{
				$result['filename'] = $filename .= '-'.rand(10, 99);
			}
		}
		// try to upload file
		if ($this->file->save($uploadDirectory . $filename . '.' . $ext))
		{
    		$result['path'] = $filename . '.' . $ext;
			$up_dir = str_replace('../','',$uploadDirectory);
			$result['full_path'] = $_GET['dir'].$result['path'];
			$result['size'] = $size;
			// ------------------------------------------------
			// creating thumbs and more
			// file
			$file_base = $_GET['dir'].$filename;
			// set configs for lib
			$_size = getimagesize($_GET['dir'].$filename.'.'.$ext);
			//
			if($_size[0] > 1000 || $_size[1] > 1000)
			{
				if($_size[0] > $_size[1])
				{
				
					$width = 1000;
					$height = $_size[1]/($_size[0]/1000);
				}
				else
				{
					$height = 1000;
					$width = $_size[0]/($_size[1]/1000);
				}
			}
			else
			{
				$width = $_size[0];
				$height = $_size[1];
			}
			// resize to good mesaure
			$config['image_library'] = 'gd2';
			$config['source_image'] = $file_base.'.'.$ext;
			$config['maintain_ratio'] = TRUE;
			$config['height'] = $height;
			$config['width'] = $width;
			// load config to lib
			$this->CI->load->library('image_lib', $config);
			// resize
			$this->CI->image_lib->resize();
			//
			$_size = getimagesize($_GET['dir'].$filename.'.'.$ext);
			if($_size[0] < $_size[1])
			{
				$width = 150;
				$height = $_size[1]/($_size[0]/150);
			}
			else
			{
				$height = 150;
				$width = $_size[0]/($_size[1]/150);
			}
			//
			$config['image_library'] = 'gd2';
			$config['source_image'] = $file_base.'.'.$ext;
			$config['new_image'] = $file_base.'_thumb_150.'.$ext;
			$config['maintain_ratio'] = TRUE;
			$config['height'] = $height;
			$config['width'] = $width;
			// load config to lib
			$this->CI->image_lib->initialize($config);
			// resize
			$this->CI->image_lib->resize();
			//
			$config['image_library'] = 'gd2';
			$config['source_image'] = $file_base.'_thumb_150.'.$ext;
			$config['maintain_ratio'] = FALSE;			
			$config['height'] = 150;
			$config['width'] = 150;
			$config['x_axis'] = 0;
			// apply settings
			$this->CI->image_lib->initialize($config); 
			// crop
			$this->CI->image_lib->crop();
			// -----------------------
			// 1 column (300X350)
			if($_size[0] < $_size[1])
			{
				$width = 300;
				$height = $_size[1]/($_size[0]/300);
			}
			else
			{
				$height = 350;
				$width = $_size[0]/($_size[1]/350);
			}
			//
			$config['image_library'] = 'gd2';
			$config['source_image'] = $file_base.'.'.$ext;
			$config['new_image'] = $file_base.'_1column.'.$ext;
			$config['maintain_ratio'] = TRUE;
			$config['height'] = $height;
			$config['width'] = $width;
			// load config to lib
			$this->CI->image_lib->initialize($config);
			// resize
			$this->CI->image_lib->resize();
			//
			$config['image_library'] = 'gd2';
			$config['source_image'] = $file_base.'_1column.'.$ext;
			$config['maintain_ratio'] = FALSE;			
			$config['height'] = 350;
			$config['width'] = 300;
			$config['x_axis'] = 0;
			// load config to lib
			$this->CI->image_lib->initialize($config);
			// resize
			$this->CI->image_lib->crop();
			// -----------------------
			// 2 columns (610x350)
			if($_size[0] < $_size[1])
			{
				$width = 610;
				$height = $_size[1]/($_size[0]/610);
			}
			else
			{
				$height = 350;
				$width = $_size[0]/($_size[1]/350);
			}
			//
			$config['image_library'] = 'gd2';
			$config['source_image'] = $file_base.'.'.$ext;
			$config['new_image'] = $file_base.'_2columns.'.$ext;
			$config['maintain_ratio'] = TRUE;
			$config['height'] = $height;
			$config['width'] = $width;
			// load config to lib
			$this->CI->image_lib->initialize($config);
			// resize
			$this->CI->image_lib->resize();
			//
			$config['image_library'] = 'gd2';
			$config['source_image'] = $file_base.'_2columns.'.$ext;
			$config['maintain_ratio'] = FALSE;			
			$config['height'] = 350;
			$config['width'] = 610;
			$config['x_axis'] = 0;
			// load config to lib
			$this->CI->image_lib->initialize($config);
			// resize
			$this->CI->image_lib->crop();
			// ------------------------------------------------
			// upload to DB
			$img['type'] = 'media';
			// data
			$img['data']['dir'] = $up_dir;
			$img['data']['filename'] = $filename.'.'.$ext;
			$img['data']['filename_no_ext'] = $filename;
			$img['data']['ext'] = $ext;
			$img['data']['label'] = $filename;
			$img['data']['alt'] = $filename;
			$img['data']['file_path'] = $result['path'];
			$img['data']['thumb_150'] = $filename.'_thumb_150.'.$ext;
			$img['data']['thumb_1column'] = $filename.'_1column.'.$ext;			
			$img['data']['thumb_2columns'] = $filename.'_2columns.'.$ext;											
			$img['data']['full_path'] = $up_dir.$result['path'];
			$img['data']['thumb_150_path'] = $up_dir.$filename.'_thumb_150.'.$ext;	
			$img['data']['cms_full_path'] = $uploadDirectory.$result['path'];
			$img['data']['cms_thumb_150'] = $uploadDirectory.$filename.'_thumb_150.'.$ext;
			// prepare data
			$img['data'] = json_encode($img['data']);
			// insert into DB
			$this->CI->db->insert('client_files', $img);
			// if db entry successful
			if($this->CI->db->insert_id() != null) 
			{
				// return ID & success = true
				$result['thumb_150'] = $filename.'_thumb_150.'.$ext;
				$result['id'] = $this->CI->db->insert_id();
				$result['success'] = true;
			}
			// if db error
			else
			{
				// return error
				$result['error'] = 'Could not save uploaded file. The upload was cancelled, or server error encountered';             
			}
		}
		else
		{
			$result['error'] = 'Could not save uploaded file. The upload was cancelled, or server error encountered';
		}
		// return results
		return $result;
	}    
}
/* End of file media.php */
/* Location: ./application/controllers/media.php */