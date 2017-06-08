<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller {

	public function index()
	{

	}
	// get page
	function get_page()
	{
		$this->load->model('page_model');
		$page = $this->page_model->get_page( $this->input->post('id') );
		echo $this->load->view('custom/dialog', $page[0], TRUE);
	}
	// --------------------------------------------
	// send contact form
	function sendform()
	{
		$email 		= $this->config->item('email_inquire');
		$subject 	= 'Contact Form ('.$this->input->post('name').')';
		$body 		= '<div> <p>Name: %1$s </p><p>Email: <a href="mailto:%3$s">%2$s</a> </p> <p> Message: %3$s </p><p> Page: %4$s </p></div>';
		//
		$ve = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
		if( ereg( $ve, $this->input->post('email') ) )
		{	
			$rn = "\r\n";
			
			$body = sprintf( $body, $this->input->post('name'), $this->input->post('email'), $this->input->post('text'), $this->input->post('page_url') );
							 
			$header	= 'From: '.$this->input->post('name').' <'. $this->input->post('email') .'> '.$rn; 				
			$header .= 'MIME-Version: 1.0'. $rn; 
			$header .= 'Content-type: text/html; charset=utf-8';
			//
			if( !mail($email, $subject, $body, $header) )
			{	
				echo json_encode(array('error' => 'There was a problem and the message was probably not sent.'));
			}
			else
			{	
				sleep(2); // send animation 	
				echo json_encode(array('sent' => 'true'));
			}
		}
		else
		{
			echo json_encode(array('error' => 'not sent'));
		}
	}
	// ------------------------------------------------------------------------------------------------------------------------------------
	// Shopping Cart
	// --------------------------------------------
	// add to cart
	function add_cart( $id = null )
	{
		if($id != null)
		{
			$this->load->model('store_model');
			// retrieve item from database
			$product = $this->store_model->get_product($id);
			// get price
			if(isset($product['on_sale']) && $product['on_sale'] === true)
			{
				$price = $product['sales_price'];
			}
			else
			{
				$price = $product['price'];
			}
			//
			$data = array(
				'id'      => $id,
				'qty'     => 1,
				'price'   => number_format($price, 2),
				'name'    => $product['title'],
				'options' => array('size' => $this->input->post('size'))
			);
			// add to cart
			$this->cart->insert($data);
			// 
			echo json_encode(array('product' => $data['name'], 'size' => $data['options']['size'], 'price' => number_format($data['price']), 'id' => $id, 
			'amount' => count($this->cart->contents()), 'image' => ''));
		}
		else
		{
			echo json_encode(array('error' => TRUE));
		}
		//
	}
	// --------------------------------------------
	// get cart
	function get_cart()
	{
		$this->load->model('store_model');
		// get items
		echo $this->store_model->get_products_cart();
	}
	// --------------------------------------------
	// update cart
	function update_cart()
	{
		// update products
		$products = $this->input->post('products');
		// update in cart
		$this->cart->update_options($products);
		// return count
		echo json_encode( array('amount' => count($this->cart->contents())) );
	}
	// --------------------------------------------
	// update order
	function insert_order()
	{
		$price = 0;
		//
		$products = $this->input->post('products');
		//
		foreach( $products as $key => $prod )
		{
			$this->db->or_where('id', $prod['id']);
			$items[$prod['id']] = array('qty' => $prod['qty'], 'size' => $prod['options']['size'], 'img' => $prod['img']);
		}
		$query = $this->db->get('client_entries');
		$res = $query->result_array();
		// calc
		foreach( $res as $values )
		{
			$data = json_decode($values['data'], TRUE);
			// check for sale
			if( isset($data['sales_start']) && $data['sales_start'] != null)
			{
				// explode date dd/mm/yy
				$start 	= explode('/',$data['sales_start']);
				$end 	= explode('/',$data['sales_end']);
				// check if sale is active
				if( mktime(0,0,0, $start[1], $start[0], $start[2]) <= time() && ( count($end) == 0 || mktime(0,0,0, $end[1], $end[0], $end[2]) >= time() ) )
				{
					$price += $products[$values['id']]['qty'] * $data['sales_price'];
				}
				else
				{
					$price += $products[$values['id']]['qty'] * $data['price'];
				}
			}
			// if no sale
			else
			{
				$price += $products[$values['id']]['qty'] * $data['price'];
			}
		}
		// data for update
		$db_data['order_id'] 	= $this->input->post('order_id');
		$db_data['total'] 		= number_format($price, 2, '.', '');
		$db_data['items'] 		= json_encode($items);
		// update db
		$this->db->insert('client_orders', $db_data);
		//
		echo number_format($price, 2, '.', '');
	}
	// --------------------------------------------
	// get price
	function get_price()
	{
		$price = 0;
		//
		$products = $this->input->post('products');
		//
		foreach( $products as $key => $prod )
		{
			$this->db->or_where('id', $prod['id']);
		}
		$query = $this->db->get('client_entries');
		$res = $query->result_array();
		// calc
		foreach( $res as $values )
		{
			$data = json_decode($values['data'], TRUE);
			// check for sale
			if( isset($data['sales_start']) && $data['sales_start'] != null)
			{
				// explode date dd/mm/yy
				$start 	= explode('/',$data['sales_start']);
				$end 	= explode('/',$data['sales_end']);
				// check if sale is active
				if( mktime(0,0,0, $start[1], $start[0], $start[2]) <= time() && ( count($end) == 0 || mktime(0,0,0, $end[1], $end[0], $end[2]) >= time() ) )
				{
					$price += $products[$values['id']]['qty'] * $data['sales_price'];
				}
				else
				{
					$price += $products[$values['id']]['qty'] * $data['price'];
				}
			}
			// if no sale
			else
			{
				$price += $products[$values['id']]['qty'] * $data['price'];
			}
		}
		// update db
		echo number_format($price, 2, '.', '');
	}
	// --------------------------------------------
	// delete order
	function delete_order()
	{
		// delete db
		$this->db->where('order_id',$this->input->post('order_id'));
		$this->db->delete('client_orders');
	}
	// --------------------------------------------
// END OF CLASS
}

/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */