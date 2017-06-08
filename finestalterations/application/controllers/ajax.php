<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class ajax extends MY_Controller {
	
	function submit_form()
	{
		// validateion
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'email', 'trim|xss_clean|required|valid_email');
		$this->form_validation->set_rules('name', 'name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('message', 'message', 'trim|xss_clean|required');
		// validate form
		if($this->form_validation->run() === TRUE)
		{
			// success
			echo json_encode(array("success" => 'true'));
			// send email
			$this->load->library('email');
			$this->email->from($this->input->post('email'), $this->input->post('name'));
			$this->email->to(config('contact/email'));
			$this->email->subject('Contact email from: '.$this->input->post('name'));
			$this->email->message($this->input->post('message'));	

			$this->email->send();
		}
		else
		{
			foreach( array('email','name','message') as $type )
			{
				if( form_error($type) != null )
				{
					$errors[$type] = form_error($type);
				}
			}
			echo json_encode(array('errors' => $errors));
		}
	}
	// -----------------------------------
	// newsletter
	function newsletter(){
		echo $this->load->view('newsletter', '', TRUE);
	}
	// -----------------------------------
	// ticket
	function ticket()
	{
		$data = db_prepare_data(array('customer_name', 'customer_email', 'customer_phone','text', 'store_id'), FALSE);
		// validateion
		$this->load->library('form_validation');
		$this->form_validation->set_rules('customer_email', 'customer_email', 'trim|xss_clean|required|valid_email');
		$this->form_validation->set_rules('customer_name', 'customer_name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('text', 'text', 'trim|xss_clean|required');
		// validate form
		if($this->form_validation->run() != TRUE)
		{
			foreach( array('customer_email','customer_name','text') as $type )
			{
				if( form_error($type) != null )
				{
					$errors['fields'][$type] = $type;
				}
			}
			($data['customer_name'] == "Full name (required)" ? $errors['fields']['customer_name'] = 'customer_name' : '');
			($data['text'] == "Full name (required)" ? $errors['fields']['text'] = 'text' : '');
			//
			$errors['error'] = TRUE;
			//
			echo json_encode($errors);
		}
		elseif( $data['customer_name'] == "Full name (required)" || $data['text'] == "Your message (required)" )
		{
			($data['customer_name'] == "Full name (required)" ? $errors['fields']['customer_name'] = 'customer_name' : '');
			($data['text'] == "Full name (required)" ? $errors['fields']['text'] = 'text' : '');			
			//
			$errors['error'] = TRUE;
			//
			echo json_encode($errors);
		}
		else
		{
			if($data['customer_phone'] == 'Phone number (if preferred contact)')
			{
				$data['customer_phone'] = "";
			}
			// success
			$data['status']	= 1;
			// insert data into db
			$id = db_insert( config('db_prefix').config('db_tickets'), $data);
			$store = db_select( config('db_prefix').config('db_data'), array('id' => $data['store_id']), array('single' => TRUE));
			// email
			$this->load->library('email');
			$this->email->from(config('contact/email'), config('contact/name'));
			// send email to customer
			$this->email->to($data['customer_email']);
			$this->email->subject('Your inquire at '.$store['name']);
			$this->email->message($this->load->view('customer_new_inquiry',$data,TRUE));	
			$this->email->send();
			// send email to store
			$this->email->to($store['email']);
			$this->email->subject('New inquire for '.$store['name']);
			$this->email->message($this->load->view('new_inquiry',$data,TRUE));	
			$this->email->send();
			// 
			echo json_encode(array('success' => TRUE));
			// update ticket nr
			db_update( config('db_prefix').config('db_tickets'), array('id' => $id), array('ticket_nr' => $store['store_nr'].str_pad($id, 7, "0", STR_PAD_LEFT)));
		}
	}
}