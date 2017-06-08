<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Tickets extends MY_Controller {

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
	* @description show all tickets
	*
	*/
	public function overview()
	{
		// load assets
		css_add('datepicker, tickets');
		js_add('jquery.datepicker, tickets');
		// fetch users
		$tickets = $this->data['tickets'];
		// fetch stores
		$stores = db_select(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => 'store'), array('json' => array('data'), 'index' => 'id'));
		//
		if( isset($stores) && is_array($stores) )
		{
			foreach($stores as $key => $store)
			{
				$stores[$key] = $store[0];
				$_stores[$key] = $store[0]['name'];
				$ticket_group[$key] = '';
			}
		}
		if( isset($tickets) && is_array($tickets) && count($tickets) > 0)
		{
			// build tickets
			foreach( $tickets as $ticket )
			{
				$ticket['stores'] = $_stores;
				$column = 'default';
				if($ticket['status'] == '3' || $ticket['status'] == '6')
				{
					$column = 'deleted';
				}
			
				$ticket_group[$ticket['store_id']][$column][] = $this->load->view('tickets/list_item', $ticket, TRUE);
			}	
		}	
		// merge tickets
		if( isset($ticket_group) && is_array($ticket_group) )
		{
			$i = 1;
			foreach($ticket_group as $store => $list )
			{
				$items['default'] = (isset($list['default']) ? implode($list['default'], '') : '');
				$items['deleted'] = (isset($list['deleted']) ? implode($list['deleted'], '') : '');
				$i = 1 - $i;
				if( ( user('store') == 'all' || user('store') == $store ) && isset($stores[$store]))
				{
					$this->data['content'][$i][] = $this->load->view('tickets/ticket_group', array('group_id' => $store, 'group' => $stores[$store]['name'], 'items' => $items), TRUE);
				}
			}
		}
		// show for specific users
		$column_one = null;
		$column_two = null;
		if( isset($this->data['content'][0]) )
		{
			$column_one = implode($this->data['content'][0], '');
		}
		if( isset($this->data['content'][1]) )
		{
			$column_two = implode($this->data['content'][1], '');
		}
		// create content
		$this->data['content'] = '<div class="ticket-column">'.$column_one.'</div><div class="ticket-column">'.$column_two.'</div>';
		// load into template
		view('tickets/overview', $this->data);
	}
	/* -----------------------------------
	* save
	*
	* @description save ticket
	*
	*/
	function save( )
	{
		// prepare data
		$data = db_prepare_data(array('customer_name', 'customer_email', 'customer_phone', 'status', 'customer_address', 'notes', 'resolved', 'pickup_time', 'store_id'), FALSE);
		//
		$data['price'] = str_replace(',','.',$this->input->post('price'));
		// update db
		db_update(config('system/current/db_prefix').config('db_tickets'), array('id' => $this->input->post('id')), $data, array('merge' => TRUE));
		//
		$ticket = db_select(config('system/current/db_prefix').config('db_tickets'),array('id' => $this->input->post('id')), array('json' => array('data'), 'single' => true));
		//
		if( $this->input->post('notify') == 'notify' )
		{ 
			$contact = db_select(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => 'contact'), array('json' => array('data'), 'single' => true));
			$data['company'] = $contact['company'];
			$data['ticket_nr'] = $ticket['ticket_nr'];
			// send email
			$this->load->library('email');
			$this->email->from($contact['email'], $contact['company']);
			$this->email->to($data['customer_email']);
			$this->email->subject('Your inquiry at '.$contact['company'].' has been modified.');
			$this->email->message($this->load->view('tickets/inquiry', $data, TRUE));	

			$this->email->send();
		}
		//
		echo json_encode(array('success' => true));
	}
	/* -----------------------------------
	* new
	*
	* @description create new ticket
	*
	*/
	function new_ticket( $group )
	{
		if($group != null)
		{
			// fetch stores
			$stores = db_select(config('system/current/db_prefix').config('db_data'), array('key' => 'settings', 'type' => 'store'), array('json' => array('data'), 'index' => 'id'));
			// insert new ticket
			$id = db_insert( config('system/current/db_prefix').config('db_tickets'), array('store_id' => $group, 'status' => 1));
			// update ticket nr
			$ticket_nr = $stores[$group][0]['store_nr'].str_pad($id, 7, "0", STR_PAD_LEFT);
			db_update(config('system/current/db_prefix').config('db_tickets'), array('id' => $id), array('ticket_nr' => $ticket_nr));
			// loop through stores
			foreach($stores as $key => $store)
			{
				$stores[$key] = $store[0];
				$_stores[$key] = $store[0]['name'];
			}
			//
			$ticket = $this->load->view('tickets/list_item', array('id' => $id, 'status' => 1, 'store_id' => $group, 'stores' => $_stores, 'ticket_nr' => $ticket_nr ), TRUE);
			// return data
			echo json_encode(array('success' => true, 'ticket' => $ticket));
		}
	}
	/* -----------------------------------
	* delete
	*
	* @description delete ticket
	*
	*/
	function delete()
	{
		$id = $this->input->post('id');
		if( $id != null && $this->input->post('status') != 3)
		{
			db_update(config('system/current/db_prefix').config('db_tickets'), array('id' => $id), array('status' => '3'));
			// return id
			echo $id;
		}
		elseif( $id != null && $this->input->post('status') == 3)
		{
			db_delete(config('system/current/db_prefix').config('db_tickets'), array('id' => $id));
			// return id
			echo 'deleted';
		}
	}
}