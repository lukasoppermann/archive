<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * PayPal IPN Listener Class
 *
 * @version		0.1
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Cms
 * @author		Lukas Oppermann - veare.net
 *
 * @original 	Based on Micah Carrick PHP-PayPal-IPN class (https://github.com/Quixotix/PHP-PayPal-IPN)
 */
class CI_paypal {
	// If true use cURL to send the post to PayPal. If false use fsockopen()
	public $use_curl = true;
	// If true, explicitly sets cURL to use SSL version 3. Use this if cURL is compiled with GnuTLS SSL.
	public $force_ssl_v3 = true;     
	// If true, cURL will use the CURLOPT_FOLLOWLOCATION to follow any "Location: ..." headers in the response.
	public $follow_location = false;     
	// If true, SSL secure connection (port 443) is used for post to PayPal. If false, a standard HTTP (port 80) connection
	public $use_ssl = true;      
	// If true, use sandbox URI www.sandbox.paypal.com. If false, use live URI www.paypal.com.
	public $use_sandbox = true; // false
	// time in sec, to wait for the PayPal server to respond before timing out.
	public $timeout = 30; // default 30 = 30 seconds
	// define variables
	private $post_data = array();
	private $post_uri = '';     
	private $response_status = '';
	private $response = '';
	var $CI = null;
	// define urls in constants
	private $paypal_host = 'www.paypal.com';
	private $sandbox_host = 'www.sandbox.paypal.com';
	
	// construct
	function __construct()
	{		
		$this->CI = &get_instance();
		$this->CI->config->load('paypal');
		$this->use_sandbox = $this->CI->config->item('paypal_sandbox');
	}
	/**
	*  Post Back Using cURL
	*
	*  Sends the post back to PayPal using the cURL library. Called by
	*  the processIpn() method if the use_curl property is true. Throws an
	*  exception if the post fails. Populates the response, response_status,
	*  and post_uri properties on success.
	*
	*  @param  string  The post data as a URL encoded string
	*/
	protected function curlPost( $encoded_data )
	{
		// check for ssl
		if( $this->use_ssl )
		{
			$uri = 'https://'.$this->get_paypal_host().'/cgi-bin/webscr';
			$this->post_uri = $uri;
		}
		else
		{
			$uri = 'http://'.$this->get_paypal_host().'/cgi-bin/webscr';
			$this->post_uri = $uri;
		}
		// in cUrl
		$ch = curl_init();
		// set cUrl options
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->follow_location);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		// check to force ssl Version 3
		if ($this->force_ssl_v3)
		{
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		}
		// fetch ssl response
		$this->response = curl_exec($ch);
		$this->response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
		// check response
		if( $this->response === false || $this->response_status == '0' )
		{
			$errno = curl_errno($ch);
			$errstr = curl_error($ch);
			throw new Exception("cURL error: [$errno] $errstr");
		}
	}
	/**
	*  Post Back Using fsockopen()
	*
	*  Sends the post back to PayPal using the fsockopen() function. Called by
	*  the processIpn() method if the use_curl property is false. Throws an
	*  exception if the post fails. Populates the response, response_status,
	*  and post_uri properties on success.
	*
	*  @param  string  The post data as a URL encoded string
	*/
	protected function fsockPost( $encoded_data )
	{
		// check for ss use
		if( $this->use_ssl )
		{
			$uri = 'ssl://'.$this->get_paypal_host();
			$port = '443';
			$this->post_uri = $uri.'/cgi-bin/webscr';
		}
		else
		{
			$uri = $this->get_paypal_host(); // no "http://" in call to fsockopen()
			$port = '80';
			$this->post_uri = 'http://'.$uri.'/cgi-bin/webscr';
		}
		// setup fsock
		$fp = fsockopen($uri, $port, $errno, $errstr, $this->timeout);
		// check for errors
		if( !$fp )
		{ 
			// fsockopen error
			throw new Exception("fsockopen error: [$errno] $errstr");
		} 
		// set headers
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: ".strlen($encoded_data)."\r\n";
		$header .= "Connection: Close\r\n\r\n";
		// send via fputs
		fputs($fp, $header.$encoded_data."\r\n\r\n");
		// fetch response
		while( !feof($fp) )
		{ 
			if( empty($this->response) )
			{
				// extract HTTP status from first line
				$this->response .= $status = fgets($fp, 1024); 
				$this->response_status = trim(substr($status, 9, 4));
			}
			else
			{
				$this->response .= fgets($fp, 1024); 
			}
		} 
		// close connection
		fclose($fp);
	}
 	/**
	*  Get Paypal Host
	*
	*  Returns correct host base on settings
	*/
	private function get_paypal_host()
	{
		if( $this->use_sandbox )
		{
			return $this->sandbox_host;
		}
		else
		{
			return $this->paypal_host;
		}
	}
    /**
     *  Get POST URI
     *
     *  Returns the URI that was used to send the post back to PayPal. This can
     *  be useful for troubleshooting connection problems. The default URI
     *  would be "ssl://www.sandbox.paypal.com:443/cgi-bin/webscr"
     *
     *  @return string
     */
	public function get_post_uri() 
	{
		return $this->post_uri;
	}    
    /**
     *  Get Response
     *
     *  Returns the entire response from PayPal as a string including all the
     *  HTTP headers.
     *
     *  @return string
     */
    public function getResponse() {
        return $this->response;
    }
    /**
     *  Get Response Status
     *
     *  Returns the HTTP response status code from PayPal. This should be "200"
     *  if the post back was successful. 
     *
     *  @return string
     */
	public function get_response_status()
	{
		return $this->response_status;
	}    
	/**
	*  Get Text Report
	*
	*  Returns a report of the IPN transaction in plain text format. This is
	*  useful in emails to order processors and system administrators. Override
	*  this method in your own class to customize the report.
	*
	*  @return string
	*/
	public function get_text_report()
	{   
		$r = '';
        // date and POST url
        for ($i=0; $i<80; $i++) { $r .= '-'; }
        $r .= "\n[".date('m/d/Y g:i A').'] - '.$this->get_post_uri();
        if ($this->use_curl) $r .= " (curl)\n";
        else $r .= " (fsockopen)\n";
        
        // HTTP Response
        for ($i=0; $i<80; $i++) { $r .= '-'; }
        $r .= "\n{$this->getResponse()}\n";
        
        // POST vars
        for ($i=0; $i<80; $i++) { $r .= '-'; }
        $r .= "\n";
        
        foreach ($this->post_data as $key => $value) {
            $r .= str_pad($key, 25)."$value\n";
        }
        $r .= "\n\n";
        
        return $r;
    }
    
	/**
	*  Process IPN
	*
	*  Handles the IPN post back to PayPal and parsing the response. Call this
	*  method from your IPN listener script. Returns true if the response came
	*  back as "VERIFIED", false if the response came back "INVALID", and 
	*  throws an exception if there is an error.
	*
	*  @param array
	*  @return boolean
	*/    
	public function process_ipn( $post_data = null )
	{
		// set encoded data
		$encoded_data = 'cmd=_notify-validate';
		//
		if( $post_data === null )
		{ 
			// use raw POST data 
			if( !empty($_POST) )
			{
				$this->post_data = $_POST;
				$encoded_data .= '&'.file_get_contents('php://input');
			} 
			else
			{
				throw new Exception("No POST data found.");
			}
		}
		else
		{ 
			// use provided data array
			$this->post_data = $post_data;
			
			foreach( $this->post_data as $key => $value )
			{
				$encoded_data .= "&$key=".urlencode($value);
			}
		}
		// post data
		if ($this->use_curl) 
		{
			$this->curlPost($encoded_data); 
		}
		else
		{
			$this->fsockPost($encoded_data);
		}
		// check if response status == 200
		if( strpos($this->response_status, '200') === false )
		{
			throw new Exception("Invalid response status: ".$this->response_status);
		}
		// check response
		if( strpos($this->response, "VERIFIED") !== false )
		{
			return true;
		} 
		elseif( strpos($this->response, "INVALID") !== false ) 
		{
			return false;
		} 
		else 
		{
			throw new Exception("Unexpected response from PayPal.");
		}
	}
	/**
	*  Require Post Method
	*
	*  Throws an exception and sets a HTTP 405 response header if the request
	*  method was not POST. 
	*/    
	public function require_post_method()
	{
		// require POST requests
		if( $_SERVER['REQUEST_METHOD'] && $_SERVER['REQUEST_METHOD'] != 'POST')
		{
			header('Allow: POST', true, 405);
			throw new Exception("Invalid HTTP request method.");
		}
	}
	/**
	*  ipn_fraud_check
	*
	*  check all the returned data for fraud
	*/    
	public function ipn_fraud_check( $args = null, $post = null )
	{
		// tell PHP to log errors to ipn_errors.log in this directory
		ini_set('log_errors', true);
		ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');
		// if arguments given, start checking
		//
		if( is_array( $args ) && $post != null )
		{
			$completed = false;
			$denied = false;
			// check payment status
			if( $post['payment_status'] == 'Denied' )
			{
				$status = "The payment is denied, products will be added to Stock again.";
				$denied = true;
			}
			elseif( $post['payment_status'] == 'Completed' )
			{
				$status = "The payment is completed";
				$completed = true;
			}
			else
			{
				$status = "Currently the payment is ".$post['payment_status']."\n you will be notified once the status changes.";
			}
			// check receiver email
			if( isset($args['receiver_email']) && $post['receiver_email'] != $args['receiver_email'] )
			{
				$error[] = "'receiver_email' does not match: ".$post['receiver_email']."\n";
			}
			// check total
			if( isset($args['total']) && $post['mc_gross'] != $args['total'] )
			{
				$error[] = "'total' (mc_gross) does not match: ".$post['mc_gross']."\n";
			}
			// check currency
			if( isset($args['currency']) && $post['mc_currency'] != $args['currency'] )
			{
				$error[] = "'currency' (mc_currency) does not match: ".$post['mc_currency']."\n";
			}
			// ---------------
			// check txn_id
			if( isset($args['paypal_id']) )
			{
				$paypal_id = 'paypal_id';
			}
			elseif( isset($args['txn_id']) )
			{
				$paypal_id = 'txn_id';
			}
			// compare paypal_id with db
			if( $paypal_id != null )
			{
				$paypal = $this->CI->db->where($paypal_id, mysql_real_escape_string($post['txn_id']))->get($args[$paypal_id]);
				// check if txn exists
				if( $paypal->num_rows() > 0 )
				{
					$error[] = "'txn_id' has already been processed: ".$post['txn_id']."\n";
				}
			}
			// ---------------
			// check if errors
			if( is_array($error) && count($error) > 0 )
			{
				// manually investigate errors from the fraud checking
				$body = "IPN failed fraud checks: \n".implode('',$error)."\n\n";
				$body .= $this->get_text_report();
				// add order_id to db
				$this->CI->db->where('order_id',$post['item_number']);
				$this->CI->db->update($args[$paypal_id], array('status' => 'failed'));
				//
				error_log('error: IPN Fraud Warning '.$body);
			}
			else 
			{
				// add this order to a table of completed orders
				$payer_email = mysql_real_escape_string($post['payer_email']);
				$mc_gross = mysql_real_escape_string($post['mc_gross']);
				$data = array(
					'paypal_id' 			=> mysql_real_escape_string($post['txn_id']),
					'total' 				=> $mc_gross,
					'customer_email' 		=> $payer_email,
					'status' 				=> $status
				);
				// check for status, add id
				if( $completed === true )
				{
					// add order_id to db
					$this->CI->db->where('order_id',$post['item_number']);
					$data['status'] = 'completed';
					$this->CI->db->update($args[$paypal_id], $data);
				}
				// reset stock
				elseif( $denied === true )
				{
					// add order_id to db
					$results = $this->CI->db->where('order_id',$post['item_number'])->get($args[$paypal_id])->row_array();
					$items = json_decode($results['items'], TRUE);
					//
					error_log($results['items']);
					foreach( $items as $id => $item)
					{
					
						$result = $this->CI->db->where('id',$id)->get('client_entries')->row_array();
						$data = json_decode($result['data'], TRUE);
						$test = intval($data['product_stock'])+intval($item['qty']);
						$data['product_stock'] = $test;
						$data = json_encode($data);
						//
						$this->CI->db->where('id',$id);
						$this->CI->db->update('client_entries', array('data' => $data));
					}
					// update status
					$this->CI->db->where('order_id', $post['item_number']);
					$this->CI->db->update($args[$paypal_id], array('status' => $status));
				}
				else
				{
					$this->CI->db->where('order_id',$post['item_number']);
					$this->CI->db->update($args[$paypal_id], array('status' => $status));
				}
				//
				return TRUE;
			}
		}
		// if no arguments given
		else
		{
			return FALSE;
		}
	}
// End of Class
}
/* End of file paypal.php */
/* Location: ./application/controllers/paypal.php */