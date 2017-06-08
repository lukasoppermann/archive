<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Ipn extends MY_Controller {
	
	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	
	function index()
	{
		// tell PHP to log errors to ipn_errors.log in this directory
		ini_set('log_errors', true);
		ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');
		// intantiate the IPN listener
		$this->load->library('paypal');
		// try to process the IPN POST
		try
		{
			$this->paypal->require_post_method();
			$verified = $this->paypal->process_ipn();
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			exit(0);
		}
		// 
		if ($verified) 
		{			
			$query = $this->db->where('order_id',$_POST['item_number'])->get('client_orders');
			if( $query->num_rows() == 1 )
			{
				$results = $query->row_array();
				$amount = number_format($results['total'],2,'.','');
				// products
				$items = json_decode($results['items'], TRUE);
				$products = '<table border="0">';
				$products .= '<tr><td></td><td>Product Name</td><td>Quantity</td><td>New Stock (after deduction)</td><td>Size</td></tr>';
				foreach( $items as $id => $item )
				{
					$product = $this->db->where('id',$id)->get('client_entries');
					$product = $product->row_array();
					$_pd = $product['data'] = json_decode($product['data'], TRUE);
					// ---------------------------------
					// update stock
					if( $_POST['payment_status'] != 'Denied' )
					{
						$_pd['product_stock'] = "".$_pd['product_stock']-$item['qty']."";
					}
					$_data['data'] = json_encode($_pd);
					$this->db->where('id',$id)->update('client_entries', $_data);
					// ---------------------------------
					$item['name'] = $product['title'].' ('.$product['data']['product_type'].' by '.$product['data']['designer'].')';
					// ---------------------------------
					if( isset($_pd['sales_start']) && $_pd['sales_start'] != null)
					{
						// explode date dd/mm/yy
						$start 	= explode('/',$_pd['sales_start']);
						$end 	= explode('/',$_pd['sales_end']);
						// check if sale is active
						if( mktime(0,0,0, $start[1], $start[0], $start[2]) <= time() && 
						( count($end) == 0 || mktime(0,0,0, $end[1], $end[0], $end[2]) >= time() ) )
						{
							$store = 'sale';
						}
						else
						{
							$store = $product['data']['store'][key($product['data']['store'])];
						}
					}
					else
					{
						$store = $product['data']['store'][key($product['data']['store'])];
					}
					// ---------------------------------
					if( $_POST['payment_status'] != 'Denied' )
					{
						$qty = $product['data']['product_stock']-$item['qty'];
					}
					else
					{
						$qty = $product['data']['product_stock']+$item['qty'];
					}
					
					$products .= '<tr><td><a href="'.base_url().$store.'/#'.$id.'"><img src="'.$item['img'].'" /></a></td><td><a href="'.base_url().$store.'/#'.$id.'">'.$item['name'].'</a></td><td>'.$item['qty'].'</td><td>'.$qty.'</td><td>'.$item['size'].'</td></tr>';
				}
				$products .= '</table>';
			}
			//
			$ipn_check = $this->paypal->ipn_fraud_check(array(
					'receiver_email' 	=> $this->config->item('paypal_seller'),
					'total' 			=> $amount,
					'currency' 			=> $this->config->item('paypal_currency'),
					'paypal_id' 		=> 'client_orders'
				),
				$_POST
			);
			// send mail
			if( $ipn_check === TRUE )
			{
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				// Additional headers
				$headers .= 'To: '.$this->config->item('email').', '.$this->config->item('email_order').''."\r\n";
				mail($this->config->item('email').', '.$this->config->item('email_order'), 'New Order by '.$_POST['payer_email'].' - '.$_POST['payment_status'], $this->load->view('emails/order', 
					array('products' => $products, 'post' => $_POST, 'order_id' => $_POST['item_number'], 'total' => $_POST['mc_gross'], 'customer' => $_POST['payer_email'], 'status' => $_POST['payment_status']), TRUE), $headers); // add item_number to email and DB
			}
			else
			{
				error_log('error', 'IPN CHECK FALSE');
			}
		}
		else
		{
		    // manually investigate the invalid IPN
			error_log('error', 'Invalid IPN: '.$this->get_text_report());
		}

	}
}
/* End of file ipn.php */
/* Location: ./application/controllers/ipn.php */