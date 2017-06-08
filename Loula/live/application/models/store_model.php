<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * CodeIgniter store_model
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Model
 */
class store_model extends MY_Model {

	var $store_data, $products, $cur_products;

	// construct
	function __construct()
	{
		$this->store_data = $this->get_store_data();
		$products = $this->_get_products();
		//
		foreach($products as $key => $product)
		{
			// reset sale
			$sale = false;
			// get product data
			$this->products['all'][$key] = $product;
			// check for sale
			if( isset($product['sales_start']) && $product['sales_start'] != null)
			{
				// explode date dd/mm/yy
				$start 	= explode('/',$product['sales_start']);
				$end 	= explode('/',$product['sales_end']);
				// check if sale is active
				if( mktime(0,0,0, $start[1], $start[0], $start[2]) <= time() && ( count($end) == 0 || mktime(0,0,0, $end[1], $end[0], $end[2]) >= time() ) )
				{
					// set sale to true
					$sale = true;
					// add sale to product
					$product['on_sale'] = true;
					//
					$this->products['all'][$key]['on_sale'] = true;
					$this->products['sales'][$key] = $product;
					$this->products['sales']['all'][$key] = $product;
					$this->products['sales'][$product['designer']][$key] = $product;
				}
			}
			// if not on sale
			// add product to certain store
			if(isset($product['store']) && $sale == false)
			{
				foreach($product['store'] as $store)
				{
					$this->products[$store]['all'][$key] = $product;
					$this->products[$store][$product['designer']][$key] = $product;
				}
			}
		}
	}

	// ------------------------------------------------------------------------------------
	// get store data from db
	// --------------------
	function get_store_data()
	{
		// fetch data from db
		$_data = index_array($this->db_fetch('client_data', array('where' => array('key' => 'product'))), 'type', TRUE);
		// split data
		foreach($_data as $type => $vals)
		{
			$pos = array();
			foreach($vals as $id => $val)
			{
				if( !isset($tags) || !in_array($val['tag'], $tags) )
				{
					// check for position taken
					while(in_array($val['position'], $pos))
					{
						++$val['position'];
					}
					// save pos to array
					$pos[] = $val['position'];
					//
					$output[$type][$val['position']] = $val;
					//
					$tags[] = $val['tag'];
				}
			}
			// sort array
			ksort($output[$type]);
		}
		// return output
		return $output;
	}
	// ------------------------------------------------------------------------------------
	// get store data
	// --------------------
	function store_data($key)
	{
		if(array_key_exists($key, $this->store_data))
		{
			return $this->store_data[$key];
		}
		return array();
	}
	// ------------------------------------------------------------------------------------
	// create designer menu
	// --------------------
	function designers($store = 'all')
	{
		if( isset($this->products[$store]['all']) && is_array($this->products[$store]['all']) && count($this->products[$store]['all']) > 0 )
		{
			$current_designers = array_keys(index_array($this->products[$store]['all'],'designer',FALSE));
			//
			$active_designer = (active_item(2) != null) ? active_item(2) : $this->store_data['designer'][key($this->store_data['designer'])]['tag'];
			foreach($this->store_data['designer'] as $pos => $designer)
			{
				if(in_array($designer['tag'], $current_designers))
				{
					//check if is active
					$active = $active_designer == $designer['tag'] ? ' active' : '';
					//
					$output[$pos] = '<li class="item'.$active.'"><a href="'.active_url(1).'/'.$designer['tag'].'">'.$designer['label'].'</a></li>';
				}
			}
			// return
			return '<div id="subnav"><ul>'.implode('',$output).'</ul></div>';
		}
		else
		{
			return FALSE;
		}
	}
	// ------------------------------------------------------------------------------------
	// create tag menu
	// --------------------
	function tags($store = 'all')
	{
		if( isset($this->products[$store]) && count($this->products[$store]) > 0 )
		{
			$_d = index_array($this->store_data['designer'], 'tag');
			$_d_keys = array_keys($this->products[$store]);
			$_designer = active_item(2);
			$_designer_alt = array_intersect(array_keys($_d), $_d_keys);
			$designer = isset($_designer) ? $_designer : $_designer_alt[key($_designer_alt)];
			//
			if( isset($this->products[$store][$designer]) )
			{
				$current_tags = array_keys(index_array($this->products[$store][$designer],'product_type',FALSE));
			}
			//
			foreach($this->store_data['type'] as $pos => $type)
			{
				if(in_array($type['tag'], $current_tags))
				{
					$output[$pos] = '<li class="item filter" data-value="'.$type['tag'].'"><a href="#'.$type['tag'].'"><span>'.$type['label'].'</span></a></li>';
				}
			}
			// return
			return '<div id="tags" class="filter-list">
						<div class="group">
						<ul data-filter="type" class="filters type"><li class="item filter none" id="all_none">
						<a href="#all"><span data-text="none">all</span></a>
					</li>'.implode('',$output).'</ul></div></div>';
		}
	}
	// ------------------------------------------------------------------------------------
	// get products
	// --------------------
	function get_products($designer = null, $store = 'all')
	{
		if( isset($this->products[$store]) && count($this->products[$store]) > 0 )
		{
			// if designer is set
			if($designer != null)
			{
				$output = $this->products[$store][$designer];
			}
			else
			{
				$_d = index_array($this->store_data['designer'], 'tag');
				$_d_keys = array_keys($this->products[$store]);
				$_designer = active_item(2);
				$_designer_alt = array_intersect(array_keys($_d), $_d_keys);
				$designer = isset($_designer) ? $_designer : $_designer_alt[key($_designer_alt)];
				$output = $this->products[$store][$designer];
			}

			// retrieve images from db
			if(isset($output))
			{
				// set products to result
				$products = variable($output);
			}
			// return products
			return variable($products);
		}
	}
	// ------------------------------------------------------------------------------------
	// get product
	// --------------------
	function get_product($id = null)
	{
		if( isset($this->products['all'][$id]) )
		{
			return $this->products['all'][$id];
		}
		else
		{
			return FALSE;
		}
	}
	// ------------------------------------------------------------------------------------
	// check sales
	// --------------------
	function check_sales( )
	{
		if( isset( $this->products['sales'] ) && count( $this->products['sales'] ) > 0 )
		{
			return TRUE;
		}
		// no sales
		return FALSE;
	}
	// ------------------------------------------------------------------------------------
	// check instore
	// --------------------
	function check_instore( )
	{
		if( isset( $this->products['instore'] ) && count( $this->products['instore'] ) > 0 )
		{
			return TRUE;
		}
		// no sales
		return FALSE;
	}
	// ------------------------------------------------------------------------------------
	// get product
	// --------------------
	function _get_products()
	{
		$products = $this->db_fetch('client_entries', array('where' => array('type' => '2', 'status' => '1')));
		$products = index_array($products, 'id', FALSE);
		// add images
		foreach($products as $key => $product)
		{
			if( array_key_exists('images', $product) && count($product['images']) > 0 )
			{
				$images[$key] = $product['images'];
				foreach($product['images'] as $img_id)
				{
					$string[] = ' OR `id`="'.$img_id.'"';
				}
			}
		}
		// get images from db
		if( isset($string) && count($string) > 0 )
		{
			$where_string = '('.trim(implode('',$string), ' OR ').')';
			$this->db->select('*');
			$this->db->where($where_string);
			$_query = $this->db->get('client_files')->result_array();
			//
			foreach($_query as $_images)
			{
				$_images = array_merge($_images, json_decode($_images['data'], TRUE) );
				unset($_images['data']);
				$result_imgs[$_images['id']] = $_images;
			}
			// add images to products
			foreach($images as $product_key => $_imgs )
			{
				foreach($_imgs as $ik => $i)
				{
					$products[$product_key]['images'][$ik] = $result_imgs[$i];
					if($result_imgs[$i]['key'] == 'hero')
					{
						$products[$product_key]['images']['hero'] = $result_imgs[$i];
					}
				}
				// add hero if not exists
				if( !isset($products[$product_key]['images']['hero']) && count($products[$product_key]['images']) > 0 )
				{
					$products[$product_key]['images']['hero'] = $products[$product_key]['images'][key($products[$product_key]['images'])];
				}
			}
		}
		// return products including images
		return $products;
	}
	// ------------------------------------------------------------------------------------
	// get_products_cart
	// --------------------
	function get_products_cart( )
	{
		// get items from cart
		$prods = index_array($this->cart->contents(), 'id');
		$items = array_keys($prods);
		// get products from db
		$products = $this->get_products_array( $items );
		//
		$total = 0;
		// loop through items
		foreach( $products as $product )
		{
			if( $product != null && $product['product_stock'] > 0 )
			{
				$data = array_merge(array('images' => array()), $product, $prods[$product['id']]);
				$cart_items[] = $this->load->view('cart/cart-item', $data, TRUE);
			}
			$total += $prods[$product['id']]['subtotal'];
		}
		// check if items are set
		if(isset($cart_items))
		{
			$cart_items = implode('',$cart_items);
			// load cart
			return $this->load->view('cart/cart', array('items' => $cart_items, 'total' => $total), TRUE);
		}
		else
		{
			$this->cart->destroy();
		}
	}
	// get_products_array
	// --------------------
	function get_products_array( $items )
	{
		foreach( $items as $item )
		{
			$products[] = $this->get_product($item);
		}
		//
		return $products;
	}
// END class
}
