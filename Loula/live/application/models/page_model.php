<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * CodeIgniter page_model
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Model
 * @author		Lukas Oppermann - veare.net
 */
class page_model extends MY_Model {
	
	// construct
	function __construct()
	{

	}
	// ------------------------------------------------------------------------------------
	// get page from db
	// --------------------
	function get_page($id)
	{
		$page = $this->db_fetch('client_entries', array('where' => array('menu_id' => $id, 'status' => '1')));
		return $page;
	}
	// ------------------------------------------------------------------------------------
	// get news from db
	// --------------------
	function get_news($limit = 5)
	{
		$news = $this->db_fetch('client_entries', array('where' => '(`type` = 1 OR `type` = 2) AND `status` = 1', 'order' => 'date', 'order_dir' => 'desc'));
		// loop through news
		$c = 0;
		foreach($news as $new)
		{
			if($c <= $limit)
			{
				if(	$new['type'] == '1' || 
					( isset($new['news']) && isset($new['news_update']) && $new['news'] != null && 
					( (isset($new['store']) && $new['store'] != null ) || isset($new['sales_start']) )) 
				)
				{
					if(isset($new['publication_date']) && $new['publication_date'] != null)
					{
						$e = explode('/',$new['publication_date']);
						$publish_val = intval(mktime(0,0,0,$e[1],$e[0],$e[2])) - intval(time());
					}
					if(!isset($new['publication_date']) || $new['publication_date'] != null || $publish_val < 0 )
					{
						if($new['type'] == '1')
						{
							$text = $new['text'];
							$title = '<h2>'.$new["title"].'</h2>';							
						}
						else
						{
							$text = null;
							$title = null;
							$_store = null;
							if( isset($new['store']) && is_array($new['store']) )
							{
								$_store = $new['store'][key($new['store'])];
							}
							if( isset($new['sales_start']) && $new['sales_start'] != null)
							{
								// explode date dd/mm/yy
								$start 	= explode('/',$new['sales_start']);
								$end 	= explode('/',$new['sales_end']);
								// check if sale is active
								if( mktime(0,0,0, $start[1], $start[0], $start[2]) <= time() && ( count($end) == 0 || mktime(0,0,0, $end[1], $end[0], $end[2]) >= time() ) )
								{
									$_store = 'sale';
								}
							}
							if( isset($_store) )
							{
								$text = $new['news_update'];
								$title = '<h2><a href="'.base_url().$_store.'/#'.$new['id'].'">'.$new["title"].'</a></h2>';
							}
						}
						if( $title != null && $text != null )
						{
							$items[] = '<div class="news-item">'.$title.'<span class="date">'.date('d.m.Y - h:i A',strtotime($new['date'])).'</span>'.$text.'</div>';
							$c++;
						}
					}
				}
			}
			else
			{
				return implode('', $items);
			}
		}
		return implode('', $items);
	}
	// ------------------------------------------------------------------------------------
	// get images from db
	// --------------------
	function get_images($ids)
	{
		//
		if(is_array($ids))
		{
			$this->db->select('*');
			$this->db->from('client_files');
			foreach($ids as $id)
			{
				$this->db->or_where('id', $id); 
			}
			// run query
			$query = $this->db->get();
			// prep result: loop through rows
			$c = 0;
			foreach ($query->result_array() as $row)
			{
				// loop through columns
				foreach($row as $key => $value)
				{
					// check if column is json
					$json = json_decode($value, TRUE);
					// if is json
					if( is_array($json) )
					{
						// unset column
						unset($row[$key]);
						// add decoded values to row !same keys in json will be overwritten
						$row = array_merge($json, $row);
					}
				}
				$string[] = "[\"".base_url().'media/images/'.$row['file_path']."\"]";
				//
				if($c < 3)
				{
					$c++;
					// add row to array
					$array[] = '<div class="image num-'.$c.'"><img src="'.base_url().'/media/images/'.$row['thumb_150'].'" alt="'.$row['label'].'" /></div>';
				}
			}
			// return 
			return '<script type="text/javascript" charset="utf-8">
			var slide_images = ['.implode(',',$string).'];
			</script><div id="footer_images">'.implode('',$array).'</div>';
		}
	}
}