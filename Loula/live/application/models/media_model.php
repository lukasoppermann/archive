<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * CodeIgniter media_model
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Model
 * @author		Lukas Oppermann - veare.net
 */
class media_model extends MY_Model {

	var $store_data;

	// construct
	function __construct()
	{

	}

	// ------------------------------------------------------------------------------------
	// get media items
	// --------------------
	function get_items()
	{
		// fetch data & index by position
		$media = $this->db_fetch('client_files', array('where' => array('type' => 'media', 'status' => '1'), 'order' => 'date'));
		//
		if(is_array($media))
		{
			foreach($media as $img)
			{
				if($img['key'] == null || $img['key'] == '')
				{
					if(!file_exists($img['cms_full_path']))
					{
						$this->db->where('id', $img['id']);
						$this->db->delete('client_files');
					}
					else
					{
						$output[] = $this->load->view('custom/media_item', $img, TRUE);
					}
				}
			}
			// return
			return implode('',$output);
		}
	}
	// ------------------------------------------------------------------------------------
	// get images for home
	// --------------------
	function get_home_images()
	{
		// fetch data & index by position
		$media = $this->db_fetch('client_files', array('where' => array('type' => 'media', 'status' => '1'), 'order' => 'date', 'order_dir' => 'DESC'));
		//
		foreach($media as $key => $m)
		{
			if( !isset($m['column']) || $m['column'] == 'column-one' )
			{
				if( isset($m['thumb_1column']) )
				{
					$m['home_image'] = $m['thumb_1column'];
				}
			}
			else
			{
				if( isset($m['thumb_2columns']) )
				{
					$m['home_image'] = $m['thumb_2columns'];
				}
			}
			//
			if(isset($m['instore']) && $m['instore'] != false && $m['instore'] != 'false')
			{
				$output['instore'] = $m;
			}
			elseif(isset($m['eboutique']) && $m['eboutique'] != false && $m['eboutique'] != 'false')
			{
				$output['eboutique'] = $m;
			}
			elseif( $m['key'] == 'hero' )
			{
				$output['media'][$key] = $m;
			}
		}
		return $output;
	}
	// ------------------------------------------------------------------------------------
	// get products for home
	// --------------------
	function get_home_products()
	{
		$designers = index_array($this->store_model->store_data('designer'), 'tag');
		// fetch data & index by position
		$entries = $this->db_fetch('client_entries', array('where' => array('type' => '2', 'menu_id' => 999, 'status' => '1'),
		'order' => 'date', 'order_dir' => 'DESC'));
		// fetch data & index by position
		$media = index_array($this->db_fetch('client_files', array('where' => array('type' => 'image', 'key' => 'hero', 'status' => '1'),
		'order' => 'date', 'order_dir' => 'DESC')),'id');
		$m_keys = array_keys($media);
		//
		if( is_array($entries) && count($entries) > 0 )
		{
			foreach( $entries as $entry )
			{
				$key = array_intersect($entry['images'], $m_keys);
				//
				if( is_array($key) && count($key) > 0 )
				{
					if( ( isset($entry['store']) && count($entry['store']) > 0 ) || isset($entry['sales_start']) )
					{
						// check store or sales
						if( isset($entry['store']) && is_array($entry['store']) )
						{
							$store = $entry['store'][key($entry['store'])];
						}
						else
						{
							unset($store);
						}
						if( isset($entry['sales_start']) && $entry['sales_start'] != null)
						{
							// explode date dd/mm/yy
							$start 	= explode('/',$entry['sales_start']);
							if( !isset($product['sales_end']) || $product['sales_end'] === null )
							{
								$end[0] = $start[0];
								$end[1] = $start[1];
								$end[2] = $start[2]+1;
							}
							else
							{
								$end 	= explode('/',$product['sales_end']);
							}
							// check if sale is active
							if( mktime(0,0,0, $start[1], $start[0], $start[2]) <= time() && ( count($end) == 0 || mktime(0,0,0, $end[1], $end[0], $end[2]) >= time() ) )
							{
								$store = 'sale';
							}
						}
						// add media
						$media[$key[key($key)]]['link'] = $store.'/#'.$entry['id'];
						$media[$key[key($key)]]['column'] = ' product-item';
						$media[$key[key($key)]]['home_image'] = $media[$key[key($key)]]['thumb_350'];
						$media[$key[key($key)]]['label'] = '<span class="product-designer">'.$designers[$entry['designer']]['label'].'</span>'.$entry['title']; //$entry['title']

						$output['media'][] = $media[$key[key($key)]];
					}
				}
			}
		}
		// return images
		return variable($output);
	}
// END class
}
