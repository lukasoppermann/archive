<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Orders extends MY_Controller {

	function __construct()
	{
		parent::__construct();		
	}

	public function index( $method = null, $id = null )
	{
		if( method_exists($this,$method) )
		{
			$this->$method($id);
		}
		elseif( $method == null )
		{
			$this->overview();
		}
	}	
	/* -----------------------------------
	* overview
	*
	* @description show all orders
	*
	*/
	public function overview()
	{
		// load assets
		css_add('orders');
		js_add('fs.gui, orders');
		// define vars
		$product_types = config('product_type');
		$client_url = index_array(config('system/system'), 'name', true);
		$client_url = $client_url['client'][key($client_url['client'])];
		// ----------------------------------------
		// get item ids
		$ids = array();
		// loop through orders
		foreach($this->data['orders'] as $status => $orders)
		{
			// loop through individual orders
			foreach($orders as $pos => $order)
			{
				// loop through order data
				if( isset($order['data']) && is_array($order['data']) && count($order['data']) > 0 )
				{
					// get item data from db 
					foreach( $order['data'] as $id => $item )
					{
						// add item id
						if( !isset($item['build']) && isset($item['id']) )
						{
							$ids[] = $item['id'];
						}
						// check for build
						if( isset($item['build']) )
						{
							// add boat id
							if( isset($item['build']['boat']) && isset($item['build']['boat']['id']) )
							{
								$ids = array_merge((array) $item['build']['boat']['id'], $ids);
							}
							// loop through modules
							if( isset($item['build']['modules']) && count($item['build']['modules']) > 0 )
							{
								foreach( $item['build']['modules'] as $mods )
								{
									foreach( $mods as $mod )
									{
										$ids = array_merge((array) $mod['id'], $ids);
									}
								}
							}
						}
					}
				}
			}
		}
		// get producst from db
		$products = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $ids), array('index' => 'id', 'index_single' => true));
		// ----------------------------------------
		// build view data
		//
		// loop through orders
		foreach($this->data['orders'] as $status => $orders)
		{
			// start group
			$this->data['content'][$status] = "<div class='group ".str_replace(' ','-',$status)."' ".(strtolower($status) == 'closed' ? "style='display: none;'" : "")."><ul class='order-list ".str_replace(' ','-',$status)."'>";
			
			// loop through individual orders
			foreach($orders as $pos => $order)
			{
				// get customer
				if( isset( $this->data['customers'][$order['customer_id']]) )
				{
					$order['customer'] = $this->data['customers'][$order['customer_id']];
				}
				// add content
				$this->data['content'][$status] .= "<li data-id='".variable($order['id'])."' class='order ".str_replace(' ','-',strtolower($order['status']))."'>
					<div class='item'>
						<span class='order-id'>
							<span class='zeros'>".str_replace($order['id'],'',str_pad($order['id'], 10, "0", STR_PAD_LEFT))."</span>".
							$order['id']."
						</span>
						<div class='content'>
							<h3 class='customer'>
								<span class='label'></span>
								<span class='text'>".text_limiter(variable($order['customer']['billing_name']),20,'...')."</span>
							</h3>
							<span class='time'>
								<span class='label'></span>
								<span class='text'>".date('d/m/Y',strtotime($order['order_time']))."</span>
							</span>";
							// check if not closed
							if( strtolower($status) != 'closed' )
							{
								$this->data['content'][$status] .= "
								<input class='tracking' type='text' value='".variable($order['tracking'])."' placeholder='tracking code' />
								<button class='button'>save tracking</button>";
							}
							else
							{
								$this->data['content'][$status] .= "
								<span class='tracking-span'>".$order['tracking']."</span>";
							}
						$this->data['content'][$status] .= "<span class='price'>$".number_format($order['total'],'2','.',' ')."</span></div>
					</div>
					<div class='information' style='display: none;'>
						<div class='customer-information'>
						<span class='label'>status</span>".form_dropdown('status',
							array( 	'payment pending' => 'Payment pending',
											'paid' => 'Paid',
											'closed' => 'closed')
							,$status)."<br />
						".(isset($order['data']['closed_date']) ? '<span class="closed">Closed on '.date('d/m/Y H:i', $order['data']['closed_date']).'</span><br />': '')."
						".variable($order['customer']['company_name'])."<br />
							".variable($order['customer']['billing_name'])."<br />
							".variable($order['customer']['billing_street'])."<br />
							".variable($order['customer']['billing_city'])." ".variable($order['customer']['billing_state'])." ".variable($order['customer']['billing_zip'])."<br />
							".variable($order['customer']['billing_country'])."<br />
							<span class='label'>email: </span><span class='customer-email'>".variable($order['customer']['email'])."</span><br />
							<span class='label'>phone: </span>".variable($order['customer']['phone'])."<br />";
							if( isset($order['customer']['billing_street']) && isset($order['customer']['shipping_street']) && isset($order['customer']['billing_name']) && isset($order['customer']['shipping_name']) && $order['customer']['shipping_name'] != $order['customer']['billing_name'] 
									|| isset($order['customer']['billing_street']) && isset($order['customer']['shipping_street']) && isset($order['customer']['billing_name']) && isset($order['customer']['shipping_name']) && $order['customer']['shipping_street'] != $order['customer']['billing_street'] )
							{
								$this->data['content'][$status] .=  "<span class='label shipping'>Shipping address</span>
								".variable($order['customer']['shipping_name'])."<br />
								".variable($order['customer']['shipping_name'])."<br />
								".variable($order['customer']['shipping_street'])."<br />
								".variable($order['customer']['shipping_city'])." ".variable($order['customer']['shipping_state'])." ".variable($order['customer']['shipping_zip'])."<br />
								".variable($order['customer']['shipping_country'])."<br />";
							}
						$this->data['content'][$status] .= "</div><div class='order-information'>";
						// // loop through order data
						if( isset($order['data']) && is_array($order['data']) && count($order['data']) > 0 )
						{
							// ---------------------------------------
							// display items
							$ord = array();
							$i = 1;
							foreach( $order['data'] as $id => $item )
							{
								$i = 1-$i;
								// check for custom system
								if( ( !isset($item['build']) || !isset($item['build']['boat']) || !isset($item['build']['modules']) ) && isset($item['id']) && isset($products[$item['id']]) )
								{
									$ord[$i][] = "<li>".variable($item['qty'])."&times <a class='name' target='_blank' href='".$client_url['base_url'].'/products/'.$product_types[$products[$item['id']]['product_type']]['path'].$products[$item['id']]['permalink']."'>".$item['name']."</a> at $".$item['price']."/unit</li>";
								}
								elseif( isset($item) && isset($item['qty']) && isset($item['name']) && isset($item['price']) )
								{
									// add item and open list
									$_ord = "<li>".$item['qty']."&times <span class='name'>".$item['name']."</span> at $".$item['price']."/unit
									<br />";
									// check if boat exists
									if( isset($item['build']) && isset($item['build']['boat']) && isset($products[$item['build']['boat']['id']]['product_type']) )
									{
										// add boat
										$_ord .= "<ul class='system-boat'>
																<li class='boat'>
																<a class='name' target='_blank' href='".$client_url['base_url'].'/products/'.$product_types[$products[$item['build']['boat']['id']]['product_type']]['path'].$products[$item['build']['boat']['id']]['permalink']."'>".$products[$item['build']['boat']['id']]['title']."</a> at $".$item['build']['boat']['price']."
																</li>
															</ul>";
										// loop through modules
										if( isset($item['build']['modules']) && count($item['build']['modules']) > 0 )
										{
											foreach( $item['build']['modules'] as $row => $mods )
											{
												if( isset($mods) && count($mods) > 0 && isset($products[$mod['id']]) )
												{
													// open ul
													$_ord .= "<ul class='system-modules'><lh>Row ".$row."</lh>";
													// loop through items
													foreach( $mods as $pos => $mod )
													{
														$_ord .= "<li class='module'><span class='pos'>Pos ".$pos.":</span> <a class='name' target='_blank' href='".$client_url['base_url'].'/products/'.$product_types[$products[$mod['id']]['product_type']]['path'].$products[$mod['id']]['permalink']."'>".$products[$mod['id']]['title']."</a> at $".$mod['price']."</li>";
													}
													// close ul
													$_ord .= "</ul>";
												}
											}
										}
										// close ul
									}
									$_ord .= "</li>";
									// add order
									$ord[$i][] = $_ord;
								}
							}
						}
						if( isset($ord[0]) ){
							$this->data['content'][$status] .= "<ul class='list'>".implode('',$ord[0])."</ul>";
						}
						if( isset($ord[1]) ){
							$this->data['content'][$status] .= "<ul class='list'>".implode('',$ord[1])."</ul>";
						}
						$this->data['content'][$status] .= "<textarea class='notes' placeholder='your note'>".variable($order['data']['note'])."</textarea></div>";
						// check if not closed
						if( strtolower($status) != 'closed' )
						{
							$this->data['content'][$status] .= "<div class='delete-box'>
								<a class='delete'>delete this order</a>
								<a style='display: none;' class='cancel-delete'>cancel!</a>
								<a style='display: none;' class='confirm-delete'>delete!</a>
							</div>";
						}
					$this->data['content'][$status] .= "</div>
				</li>";
			}
			// end group
			$this->data['content'][$status] .= "</div></ul>";
		}
		// merge content
		$this->data['content'] = implode('',$this->data['content']);
		// load into template
		view('orders/overview', $this->data);
	}
	/* -----------------------------------
	* delete
	*
	* @description delete order
	*
	*/
	function delete()
	{
		// get id
		$id = $this->input->post('id');
		// delete order
		db_delete(config('db_orders'), array('id' => $id));
		// return json
		echo json_encode(array('success' => true));
	}
	/* -----------------------------------
	* update_status
	*
	* @description update the status
	*
	*/
	function update_status()
	{
		// get post data
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		// update db
		db_update(config('db_orders'), array('id' => $id), array('status' => $status));
	}
	/* -----------------------------------
	* update_note
	*
	* @description update the note
	*
	*/
	function update_note()
	{
		// get post data
		$id 		= $this->input->post('id');
		$value 	= $this->input->post('value');
		// update db
		db_update(config('db_orders'), array('id' => $id), array('data' => array('note' => $value)));
		//
		echo json_encode(array('success' => true));
	}
	/* -----------------------------------
	* close
	*
	* @description close order
	*
	*/
	function close()
	{
		$contact = db_select(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => 'contact'), array('json' => array('data'), 'single' => true));
		// get id
		$id = $this->input->post('id');
		// get tracking
		$tracking = $this->input->post('tracking');
		// get email
		$email = trim($this->input->post('email'));
		// update order
		db_update(config('db_orders'), array('id' => $id), array('tracking' => $tracking, 'status' => 'closed', 'data/closed_date' => time()));
		// get customer
		$customer = db_select(config('db_customers'), array('email' => $email), array('single' => true));
		$customer['tracking'] 	= $tracking;
		$customer['id']					= $id;
		$customer['closed_time']	= time();
		// ----------------
		// load email lib
		$this->load->library('email');
		// ----------------
		// email to customer
		$this->email->from($contact['email'], $contact['company']);
		$this->email->to($email);
		$this->email->subject('Your order at '.$contact['company'].' has been shipped.');
		$this->email->message($this->load->view('orders/customer_email', $customer, TRUE));	
		// send email
		$this->email->send();
		// ----------------
		// email to buchla
		$this->email->from($contact['email'], $contact['company']);
		$this->email->to($contact['email']);
		$this->email->subject('Order '.str_pad($id, 10, "0", STR_PAD_LEFT).' has been shipped.');
		$this->email->message($this->load->view('orders/buchla_order_mail', $customer, TRUE));	
		// send email
		$this->email->send();
	}
}