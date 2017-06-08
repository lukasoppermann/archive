<?php

class Checkout extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->config->load('paypal');
	}
	
	function index()
	{
		$data['AMT'] = '10.00';
		$data['CURRENCY'] = 'AUD';		
		$this->load->view('paypal/checkout', $data);
	}
		
	function payment() {
			 
		$this->load->library('form_validation');
		$this->form_validation->set_rules('BILLTOFIRSTNAME', 'BillTo Firstname', 'trim|required');
		$this->form_validation->set_rules('BILLTOLASTNAME', 'BillTo Lastname', 'trim|required');
		$this->form_validation->set_rules('BILLTOSTREET', 'BillTo Street', 'trim|required');
		$this->form_validation->set_rules('BILLTOCITY', 'BillTo City', 'trim|required');
		$this->form_validation->set_rules('BILLTOSTATE', 'BillTo State', 'trim|required');
		$this->form_validation->set_rules('BILLTOZIP', 'BillTo Zip', 'trim|required');
		$this->form_validation->set_rules('BILLTOCOUNTRY', 'BillTo Country', 'trim|required');

		if($this->form_validation->run() == FALSE)
		{
			$data['AMT'] = '10.00';
			$data['CURRENCY'] = 'AUD';		
			$this->load->view('paypal/checkout', $data);
		}
		else
		{
			//would also enter customer_id here
			$data = array(
				'BILLTOFIRSTNAME' => $this->input->post('BILLTOFIRSTNAME'),
				'BILLTOLASTNAME' => $this->input->post('BILLTOLASTNAME'),
				'BILLTOSTREET' => $this->input->post('BILLTOSTREET'),
				'BILLTOCITY' => $this->input->post('BILLTOCITY'),
				'BILLTOSTATE' => $this->input->post('BILLTOSTATE'),
				'BILLTOZIP' => $this->input->post('BILLTOZIP'),
				'BILLTOCOUNTRY' => $this->input->post('BILLTOCOUNTRY'),
				'SHIPTOFIRSTNAME' => $this->input->post('SHIPTOFIRSTNAME'),
				'SHIPTOLASTNAME' => $this->input->post('SHIPTOLASTNAME'),
				'SHIPTOSTREET' => $this->input->post('SHIPTOSTREET'),
				'SHIPTOCITY' => $this->input->post('SHIPTOCITY'),
				'SHIPTOSTATE' => $this->input->post('SHIPTOSTATE'),
				'SHIPTOZIP' => $this->input->post('SHIPTOZIP'),
				'SHIPTOCOUNTRY' => $this->input->post('SHIPTOCOUNTRY'),
				'AMT' => $this->input->post('AMT'),
				'CURRENCY' => $this->input->post('CURRENCY'),
				'logged' => date('Y-m-d H:i:s', time())
			);
			
			$this->db->insert('soda_payflow_invoices', $data);
			$INVNUM = $this->db->insert_id();			
			
			$secureTokenID = uniqid('',true);
			$post_data = "USER=".$this->config->item('user', 'paypal')
			."&VENDOR=".$this->config->item('vendor', 'paypal')
			."&PARTNER=".$this->config->item('partner', 'paypal')
			."&PWD=".$this->config->item('pwd', 'paypal')
			."&CREATESECURETOKEN=Y"
			."&SECURETOKENID=".$secureTokenID
			."&TRXTYPE=S"
			."&AMT=".$this->input->post('AMT')
			."&CURRENCY=".$this->input->post('CURRENCY')
			."&BILLTOFIRSTNAME=".$this->input->post('BILLTOFIRSTNAME')
			."&BILLTOLASTNAME=".$this->input->post('BILLTOLASTNAME')
			."&BILLTOSTREET=".$this->input->post('BILLTOSTREET')
			."&BILLTOCITY=".$this->input->post('BILLTOCITY')
			."&BILLTOSTATE=".$this->input->post('BILLTOSTATE')
			."&BILLTOZIP=".$this->input->post('BILLTOZIP')
			."&BILLTOCOUNTRY=".$this->input->post('BILLTOCOUNTRY')
			."&SHIPTOFIRSTNAME=".$this->input->post('SHIPTOFIRSTNAME')
			."&SHIPTOLASTNAME=".$this->input->post('SHIPTOLASTNAME')
			."&SHIPTOSTREET=".$this->input->post('SHIPTOSTREET')
			."&SHIPTOCITY=".$this->input->post('SHIPTOCITY')
			."&SHIPTOSTATE=".$this->input->post('SHIPTOSTATE')
			."&SHIPTOZIP=".$this->input->post('SHIPTOZIP')
			."&SHIPTOCOUNTRY=".$this->input->post('SHIPTOCOUNTRY')
			."&INVNUM=".$INVNUM
			."&ORDERID=".$INVNUM
			."&CUSTOM=".$INVNUM;
	  
			$curl = curl_init($this->config->item('host_address', 'paypal'));
    		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    		curl_setopt($curl, CURLOPT_POST, TRUE);
    		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    		$resp = curl_exec($curl);
    
			//var_dump($resp);
			
			if(!$resp) {
				$this->load->view('paypal/error');	
			} else {
				
				parse_str($resp, $arr);
			
				if($arr['RESULT'] != 0) {
					$this->load->view('paypal/error');	
				} else {
					
					$data['paypal_iframe'] =  '<iframe src="https://payflowlink.paypal.com?SECURETOKEN='.$arr['SECURETOKEN'].'&SECURETOKENID='.$secureTokenID.'&mode='.$this->config->item('mode', 'paypal').'" width="490" height="565" border="0" frameborder="0" scrolling="no" allowtransparency="true">\n</iframe>';
					$this->load->view('paypal/payment', $data);
				}
			}
		}
	}
	
	function return_pre() {
		
		$index_suffix = '';
		if(index_page() != '') {
			$index_suffix = '/';
		}
		echo '<script type="text/javascript">parent.location = "'.base_url().index_page().$index_suffix.'checkout/approved";</script>';
	}
	
	function error_pre() {
		
		$index_suffix = '';
		if(index_page() != '') {
			$index_suffix = '/';
		}
		echo '<script type="text/javascript">parent.location = "'.base_url().index_page().$index_suffix.'checkout/error";</script>';
	}
	
	function cancel_pre() {
		
		$index_suffix = '';
		if(index_page() != '') {
			$index_suffix = '/';
		}
		echo '<script type="text/javascript">parent.location = "'.base_url().index_page().$index_suffix.'checkout/cancelled";</script>';
	}
	
	function approved() {
		
		$this->load->view('paypal/approved');
	}
	
	function error() {
		
		$this->load->view('paypal/error');
	}
	
	function cancelled() {
		
		$this->load->view('paypal/cancelled');
	}
	
	function silent() {//callback
		
  		$INVNUM = $this->input->post('INVNUM');
		$RESULT = $this->input->post('RESULT');
  
		$data = array(
			'INVNUM' => $this->input->post('INVNUM'),
			'AMT' => $this->input->post('AMT'),
			'CURRENCY' => $this->input->post('CURRENCY'),
			'TYPE' => $this->input->post('TYPE'),
			'TRANSTIME' => $this->input->post('TRANSTIME'),
			'PNREF' => $this->input->post('PNREF'),
			'RESPMSG' => $this->input->post('RESPMSG'),
			'RESULT' => $this->input->post('RESULT')
		);
			
		$this->db->insert('soda_payflow_txns', $data);
		if(!$RESULT) {
			$this->db->query("update soda_payflow_invoices set status = 'Paid' where id = $INVNUM");
		}
	}
	
	function IPN() {
		
		$req = 'cmd=' . urlencode('_notify-validate');
		 
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www.paypal.com'));
		$res = curl_exec($ch);
		curl_close($ch);
 
		/*$item_name = $_POST['item_name'];
		$item_number = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];*/
 
		if (strcmp ($res, "VERIFIED") == 0) {//TESTING ONLY
			
			$to      = 'anthonylongton@yahoo.com';
			$subject = 'ipn verified';
			$message = 'ipn verified';
			$headers = 'From: webmaster@example.com' . "\r\n" .
			'Reply-To: webmaster@example.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			mail($to, $subject, $message, $headers);
		}
		else if (strcmp ($res, "INVALID") == 0) {
			
			$to      = 'anthonylongton@yahoo.com';
			$subject = 'ipn invalid';
			$message = 'ipn invalid';
			$headers = 'From: webmaster@example.com' . "\r\n" .
			'Reply-To: webmaster@example.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			mail($to, $subject, $message, $headers);
		}
	}
}