<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * Media model
 *
 * @author		Lukas Oppermann - veare.net
 */
class Media_model extends MY_Model {

	// ----------------------------------------------------------------
	// get images
	function get_images()
	{
		// fetch data & index by position
		$media = $this->db_fetch('client_files', array('where' => array('type' => 'media'), 'order' => 'date'));
		//
		if(is_array($media))
		{
			foreach($media as $img)
			{
				if(!file_exists($img['cms_full_path']))
				{
					$this->db->where('id', $img['id']);
					$this->db->delete('client_files');
				}
				else
				{
					$img['link_class'] 			= variable($img['link']) != false && variable($img['link']) != 'false'  ? ' active' : ' passive';
					$img['link']				= variable($img['link']) != false && variable($img['link']) != 'false'  ? $img['link'] : '';
					$img['instore_class'] 		= variable($img['instore']) != false ? ' active' : '';
					$img['eboutique_class'] 	= variable($img['eboutique'])  != false ? ' active' : '';
					$img['btn_hide']			= variable($img['link']) != false && variable($img['link']) != 'false' ? 'style="display: none;"' : '';
					$img['column_one']			= variable($img['column']) == 'column-one' ? ' active' : '';
					$img['column_two']			= variable($img['column']) == 'column-two' ? ' active' : '';
					// set status
					if($img['status'] == 1)
					{
						$img['status'] = ' visible';
					}
					else
					{
						$img['status'] = ' hidden';						
					}
					//
					$output[] = $this->load->view('custom/media_item', $img, TRUE);
					unset($img);
				}
			}
			// return 
			return implode('',$output);
		}
	}
	// ----------------------------------------------------------------
	// get image
	function get_image($id)
	{
		// fetch data & index by position
		$media = $this->db_fetch('client_files', array('where' => array('type' => 'media', 'id' => $id)), FALSE);
		// return 
		return $media[0];
	}
	// ----------------------------------------------------------------
	// get hero
	function get_heros()
	{
		// fetch data & index by position
		$media = $this->db_fetch('client_files', array('where' => array('type' => 'media', 'key' => 'hero')), FALSE);
		// return 
		return $media;
	}	
	// ----------------------------------------------------------------
	// delete
	function delete($id)
	{
		// fetch file from db
		$media = $this->get_image($id);
		// delete fils
		unlink($media['data']['cms_full_path']);
		unlink($media['data']['cms_thumb_150']);
		// delete from db
		$this->db->where('id', $id);
		$this->db->delete('client_files');
		$this->db->query('ALTER TABLE `client_files` AUTO_INCREMENT=1');
	}
	// ----------------------------------------------------------------
	// cleanup
	function cleanup()
	{
		// fetch file from db
		// $media = $this->get_images();
		// //
		// foreach($media as $img)
		// {
		// 	
		// }
	}
// end of media model	
}