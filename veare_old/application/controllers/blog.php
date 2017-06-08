<?php if (! defined('BASEPATH')) exit('No direct script access');

class Blog extends MY_Controller {

	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
		// load assets
		css_add('widget, blog');
		js_add('blog');
		$this->load->helper('date');
	}
	
	function index()
	{	
		// columns
		$column[0] = 'left';
		$column[1] = 'right';
		//
		$i = 0;
		// get entries from database
		$post = db_select( 'client_entries', array('type' => 2, 'status' => 1), array('limit' => 50, 'order' => 'date DESC', 'json' => 'data') );
		// process entries
		foreach( $post as $entry )
		{
			// time difference
			$entry['date'] = time_ago(mysql_to_unix($entry['date']));
			// assign posts to columns
			$posts[$column[$i]][] = $this->load->view('blog/post_teaser', $entry, TRUE);
			$i = ($i == 1) ? 0 : 1;
		}
		// merge posts
		if( isset($posts['left']) )
		{
			$posts['articles_left'] = implode('',$posts['left']);
		}
		if( isset($posts['right']) )
		{
			$posts['articles_right'] = implode('',$posts['right']);
		}
		if( isset($posts) )
		{
			$posts = array_merge($posts, $this->data);
		}
		// load view
		view('blog/index', $posts);

	}
	
	function entry( $permalink = null )
	{	
		// get entries from database
		$post = db_select( 'client_entries', array('type' => 2, 'permalink' => $permalink), array('json' => 'data', 'single' => TRUE));
		// -----------------------------------
		if( isset($post['css']) )
		{
			// get css from database
			$post['css'] = db_select( 'client_data', array('key' => 'css', 'type' => $post['css']), array('select' => 'data', 'json' => FALSE, 'single' => TRUE));
			$this->data['css'] = str_replace('[images]/', media('','images'), $post['css']['data']);
		}
		// time difference
		$post['date'] = time_ago(mysql_to_unix($post['date']));
		$post = array_merge($post, $this->data);
		$post['body_class'] = 'entry item-view';
		// prepare pre in post
		//replaces pre content with html entities
		function pre_entities($matches)
		{
			return str_replace($matches[1],htmlentities($matches[1]),$matches[0]);
		}
		// to html entities;  assume content is in the "content" variable
		$post['text'] = preg_replace_callback('/<pre.*?>(.*?)<\/pre>/imsu', 'pre_entities', $post['text']);
		// replace [images]
		$post['text'] = str_replace('[images]/', media('','images'), $post['text']);
		$post['text'] = str_replace('[images]', media('','images'), $post['text']);
		// load view
		view('blog/post', $post);
	}
	
}