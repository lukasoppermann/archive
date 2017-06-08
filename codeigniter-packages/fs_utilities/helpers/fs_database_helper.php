<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form&System Database Helper
 *
 * @package			Form&System
 * @subpackage		Helpers
 * @category		Helpers
 * @author			Lukas Oppermann - veare.net
 * @link			http://formandsystem.com/database
 * @dependencies 	array & my_array helper
 */

// --------------------------------------------------------------------
/**
 * db_delete - delete data from DB and cleans up 
 *
 * @param string
 * @param array
 * @return string
 */
function db_delete($db, $deletes)
{
	$CI = &get_instance();
	// loop through delte arguments
	foreach($deletes as $key => $delete)
	{
		// if no array, add to delete
		if( !is_array($delete) )
		{
			$CI->db->where($key, $delete);
		}
		// if array, build and && statement
		else
		{
			$i = 0;
			foreach($delete as $k => $index)
			{
				// use or for first key
				if($i == 0 || is_numeric($k))
				{
					// if key is numeric, use parent key
					is_numeric($k) ? $k = $key : '';
					// set $i to 1
					$i = 1;
					// add or where
					$CI->db->or_where($k, $index);
				}
				else
				{
					$CI->db->where($k, $index);
				}
			}
		}
	}
	// delete files
	$CI->db->delete($db);
	// clean up
	$CI->db->query('ALTER TABLE '.$db.' AUTO_INCREMENT = 1');
}
// --------------------------------------------------------------------
/**
 * db_insert - inserts into DB
 *
 * @param string
 * @param array
 * @return int
 */
function db_insert( $db, $data = null, $json = null, $batch = FALSE )
{
	$CI = &get_instance();
	// if only one entry is to be inserted
	if( $batch == FALSE )
	{
		// check if json fields are specified
		if( is_array($json) )
		{
			// loop through json
			foreach($json as $field)
			{
				//json_encode
				$data[$field] = json_encode($data[$field]);
			}
		}
		// if not, loop through all fields
		else
		{
			foreach($data as $key => $d)
			{
				// check if field is array
				if( is_array($d) )
				{
					// if true - json_encode
					$data[$key] = json_encode($d);
				}
			}
		}
		// insert entry
		$CI->db->insert($db, $data); 
		// return id
		return $CI->db->insert_id();
	}
	// if multiple entries are supposed to be inserted
	else
	{
		if( is_array($json) )
		{
			foreach($json as $field)
			{
				foreach($data as $key => $d)
				{
					if( isset($d[$field]) )
					{
						$data[$key][$field] = json_encode($d[$field]);
					}
				}
			}
		}
		else
		{
			foreach($data as $field => $array)
			{
				foreach($array as $key => $d)
				{
					// check if field is array
					if( is_array($d) )
					{
						// if true - json_encode
						$data[$field][$key] = json_encode($d);
					}
				}
			}
		}
		// insert entries
		$CI->db->insert_batch($db, $data);
		// count inserts
		$inserts = count($data);
		// get first id of batch
		$insert_ids[] = $CI->db->insert_id();
		// add following ids to array
		for($i=1;$i < $inserts; $i++)
		{
			$insert_ids[] = $CI->db->insert_id()+$i;
		}
		// return id array
		return $insert_ids;
	}
}
// --------------------------------------------------------------------
/**
 * db_update_insert - update data from DB (OR insert if it does not exist)
 *
 * @param string
 * @param array
 * @return string
 */
function db_update($db, $condition, $updates = array(), $params = array())
{
	$CI = &get_instance();
	// merge params
	$params = array_merge(array(
		'merge' 	=> TRUE,
		'json' 		=> null,
		'create'	=> FALSE
	), $params);
	// cache where statement
	$CI->db->start_cache();
	// loop through where statements
	foreach($condition as $key => $where)
	{
		// if only one value for key
		if( !is_array($where) )
		{
			$CI->db->where($key, $where);
		}
		// multiple values for key
		else
		{
			// can only be created with specific params
			$params['create'] = FALSE;
			// define I
			$i = 0;
			// loop through where statements
			foreach( $where as $k => $w )
			{
				// check if key is given
				if( !is_string($k) )
				{
					// else set key to $key
					$k = $key;
				}
				// for first argument
				if( $i == 0 )
				{
					// set $i to 1
					$i = 1;
					// add key-value pair
					$string = '(`'.$k.'` = \''.$w.'\'';
				}
				// for following argument
				else
				{
					// add or_where
					$string .= ' OR `'.$k.'` = \''.$w.'\'';
				}
			}
			// add string to where and close )
			$CI->db->where($string.')');
		}
	}
	// stop caching
	$CI->db->stop_cache();
	// get data from db
	$CI->db->select('*');
	$db_entries = db_query_results($CI->db->get($db), $params['json'], FALSE, FALSE);
	// check if data exists in db
	if( isset($db_entries) && is_array($db_entries) )
	{
		// flush cache
		$CI->db->flush_cache();
		// if entries are supposed to be merged
		if($params['merge'] == TRUE)
		{
			// load array helper
			$CI->load->helper('array');
			// loop through entries
			foreach($db_entries as $entry)
			{
				// update entry
				foreach($updates as $key => $update)
				{
					// if field exists in entry
					if( array_key_exists($key, $entry) )
					{
						// if fiel is empty
						if( !is_array($entry[$key]) )
						{
							// replace with new content
							$entry[$key] = $update;
						}
						// if content exists in field
						else
						{
							// update field and merge content
							$entry = _add_array($entry, array($key), $update);
						}
					}
					// if key does not exists
					// try if it is a multi-level-key with '/'
					elseif( strpos($key,'/') != FALSE )
					{
						// if so, update using multi-level-key
						$entry = add_array($entry, array($key => $update));
					}
					// if key just does not exists add to data update file
					else
					{
						$_update[$key] = $update; 
					}
				}
				// loop through entry-fields
				foreach($entry as $key => $e)
				{
					// if field is array
					if( is_array($e) )
					{
						// check if any updates are still pending
						if( isset($_update) )
						{
							// loop through updates
							foreach($_update as $k => $value)
							{
								// if key exists on 2nd level, update
								$e[$k] = $value;
								unset($_update[$k]);
							}
						}
						// json encode
						$entry[$key] = json_encode($e);
					}
				}
				// where condition
				$CI->db->where('id', $entry['id']);
				// update db	
				$CI->db->update($db, $entry);
			}
		}
		// if entries are just supposed to be overwritten
		else
		{
			// json decode stuff
			foreach($updates as $id => $update)
			{
				if( is_array($update) )
				{
					$updates[$id] = json_encode($$update);
				}
			}
			// update db
			$CI->db->update($db, $updates);
		}
		// return effect rows count
		return $CI->db->affected_rows();
	}
	// if entry does not exist create it
	elseif( $params['create'] !== FALSE )
	{
		// merge data
		if( is_array($condition) )
		{
			$updates = array_merge($condition, $updates);
		}
		// flush cache
		$CI->db->flush_cache();
		// insert data into db
		return db_insert($db, $updates, $params['json']);
	}
	// if not exists && can't be created
	else
	{
		// flush cache
		$CI->db->flush_cache();
		// return FALSE
		return FALSE;
	}
}
// --------------------------------------------------------------------
/**
 * db_query_results - turns db->get() into array
 *
 * @param object
 * @return string
 */
 function db_query_results($query, $json = null, $single = FALSE, $unstack = TRUE, $index = FALSE, $index_single = FALSE)
 {
 	if($query->num_rows() > 0)
 	{	
 		$array = $query->result_array();
 		foreach($array as $key => $arr)
 		{
 			// check if json fields are identified
 			if( is_array($json) )
 			{
 				foreach($json as $field)
 				{
 					if( array_key_exists($field, $arr) )
 					{
 						// decode field
 						$_array = json_decode($arr[$field], TRUE);
 						// check for array
 						if( is_array($_array) )
 						{
 							// unset json into parent level
 							if($unstack == TRUE)
 							{
 								// unset field
 								unset($array[$key][$field]);
 								// merge field
 								$array[$key] = array_merge($_array, $array[$key]);
 							}
 							// keep json inside field
 							else
 							{
 								$array[$key][$field] = $_array;
 							}
 						}
 					}
 				}	
 			}
 			// try all fields for json
 			else
 			{
 				foreach($array[$key] as $field => $value)
 				{
 					// decode field
 					$_array = json_decode($value, TRUE);
 					// if is array
 					if( is_array($_array) )
 					{
 						// unset json into parent level
 						if($unstack == TRUE)
 						{
 							// unset field
 							unset($array[$key][$field]);
 							// merge field
 							$array[$key] = array_merge($_array, $array[$key]);
 						}
 						// keep json inside field
 						else
 						{
 							$array[$key][$field] = $_array;
 						}
 					}
 				}
 			}
 			//
 			if( $index != false && isset($array[$key][$index]) && $index_single == FALSE )
 			{
 				$result[$array[$key][$index]][] = $array[$key];
 			}
 			elseif( $index != false && isset($array[$key][$index]) && $index_single == TRUE )
 			{
 				$result[$array[$key][$index]] = $array[$key];
 			}
 			else
 			{
 				$result[$key] = $array[$key];
 			}
 		}	
 		// if only one result is expected
 		if($single == TRUE)
 		{
 			return $result[key($result)];
 		}
 		// return results in array
 		return $result;
 	}
 	else
 	{	
 		return FALSE;
 	}
 }
// --------------------------------------------------------------------
/**
 * db_select - retrieves DB data from db and returns array
 *
 * @param string
 * @param array
 * @param array
 * @return array
 */ 
 function db_select( $db, $condition = array(), $params = array() )
 {
 	$CI = &get_instance();
 	// merge params
 	$params = array_merge(array('select' => '*', 'order' => null, 'limit' => null, 'json' => null, 'single' => FALSE, 
 	'unstack' => TRUE, 'index' => FALSE, 'index_single' => FALSE), $params);
 	// if condition is string
 	if( !is_array($condition) && $condition != null )
 	{
 		$CI->db->where($condition, NULL, FALSE);
 	}
 	elseif( $condition != null )
 	{
 		// loop through where statements
 		foreach($condition as $key => $where)
 		{
 			if( !is_array($where) )
 			{
 				$CI->db->where($key, $where);
 			}
 			else
 			{
 				$i = 0;
 				foreach($where as $k => $w)
 				{
 					// check if key is given
 					if( !is_string($k) )
 					{
 						$k = $key;
 					}
 					//
 					if($i == 0)
 					{
 						// set $i to 1
 						$i = 1;
 						// add and_where
 						if( !is_array($w) )
 						{
 							$string = '(`'.$k.'` = \''.$w.'\'';
 						}
 						// if multiple values
 						else
 						{
 							// start string
 							$string = '(';
 							// loop through array
 							foreach($w as $_w)
 							{
 								$string .= '`'.$k.'` = \''.$_w.'\' OR ';	
 							}
 							// remove last or
 							$string = substr($string, 0, -3);
 						}
 					}
 					else
 					{
 						// add or_where
 						if( !is_array($w) )
 						{
 							$string .= ' OR `'.$k.'` = \''.$w.'\'';
 						}
 						// if multiple values
 						else
 						{
 							// loop through array
 							foreach($w as $_w)
 							{
 								$string .= ' OR `'.$k.'` = \''.$_w.'\'';	
 							}
 						}
 					}
 				}
 				// add string to where
 				$CI->db->where($string.')');
 			}
 		}
 	}
 	// order
 	if( isset($params['order']) )
 	{
 		$CI->db->order_by($params['order']);
 	}
 	// limit results
 	if(isset($params['limit']))
 	{
 		$CI->db->limit($params['limit']);
 	}
 	// select entries from db
 	$CI->db->select($params['select']);
 	// return array
 	return db_query_results($CI->db->get($db), $params['json'], $params['single'], $params['unstack'], $params['index'], $params['index_single']);	
 }

// --------------------------------------------------------------------
/**
 * db_select_row - retrieves single row from db and returns array
 *
 * @param string
 * @param array
 * @param array
 * @return array
 */ 
function db_select_row( $db, $condition = null, $params = array() )
{
	// merge params
	$params = array_merge(array('single' => TRUE), $params);
	// return row
	return db_select( $db, $condition, $params );
}
// --------------------------------------------------------------------
/**
 * db_select_raw - retrieves raw from db and returns array
 *
 * @param string
 * @param array
 * @param array
 * @return array
 */ 
function db_select_raw( $db, $condition = null, $params = array() )
{
	// merge params
	$params = array_merge(array('unstack' => FALSE), $params);
	// return row
	return db_select( $db, $condition, $params );
}
// --------------------------------------------------------------------
/**
 * db_prepare_data - prepares data according to scheme
 *
 * @param string
 * @param array
 * @param array
 * @return array
 */
function db_prepare_data( $scheme, $encode = TRUE )
{
	// load assets
	$CI = &get_instance();
	$CI->load->helper(array('array','fs_variable'));
	// check scheme
	if( isset($scheme) && is_array($scheme) )	
	{
		// loop through scheme
		foreach($scheme as $key => $field)
		{
			// if normal scheme entry
			if( is_int($key) && strlen($key) <= 2 && is_string($field) )
			{
				$data[$field] = trim($CI->input->post($field, TRUE));
			}
			// if scheme entry is array
			elseif( !is_int($key) && is_array($field) )
			{
				// add key to json array if it does not exists
				$json = add_array((isset($json) ? $json : ''), array($key));
				// loop through keys in array
				foreach( $field as $k => $f )
				{
					// if normal key in array
					if( is_int($k) && strlen($k) <= 2 && is_string($f) )
					{
						// add to parent key
						$data[$key][$f] = trim($CI->input->post($f, TRUE));
					}
					else
					{
						$data[$key] = add_array($data[$key], array($k => trim($CI->input->post($f, TRUE))));
					}
				}
			}
			// if scheme entry has a key specifier
			else
			{
				// add key to json array if it does not exists
				$json = add_array((isset($json) ? $json : ''), array($key));
				// add data to array
				$data = add_array($data, array($key => trim($CI->input->post($field, TRUE))));
			}
		}
		// check if enode is TRUE
		if( $encode === TRUE )
		{
			// json encode everything
			foreach($json as $j)
			{
				$data[$j] = json_encode($data[$j]);
			}
		}
		// if fields option, return fields
		elseif( $encode === 'fields' )
		{
			$data['json_fields'] = $json;
		}
		// return encoded data
		return $data;
	}
	// if wrong scheme is given
	else
	{
		// log error
		log_message('error','wrong db prepare scheme used: '.(isset($scheme) ? $scheme : ''));
		// return FALSE
		return FALSE;
	}
}
/* End of file database_helper.php */
/* Location: ./system/helpers/database_helper.php */