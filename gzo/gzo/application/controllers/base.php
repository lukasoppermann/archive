<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Base extends MY_Controller {

	//php 5 constructor
	function __construct() 
 	{
		parent::__construct();
	}
	
	function index($page = null)
	{
		$language = $this->config->item('languages');
		$language = index_array($language['array'],'abbr');
		$this->lang->load('text', $language[config('lang_abbr')]['name']);
		$data = $this->data;
		// get posts
		$post_data = index_array(get_db_data(config('prefix').config('db_entries'), array('where' => array('type' => '2'), 'select' => '*')), 'language', TRUE);
		// get current lang posts by channel
		$posts = index_array($post_data[config('lang_id')], 'channel', TRUE);	
		// reset variables
		$data['header'] = null;
		$data['page'] = "";
		// special cases
		if( $this->navigation->current('path') === '/kontakt/jobs' )
		{
			if(isset($posts[1]))
			{
				$posts = index_array($posts[1], 'date');
				ksort($posts);
				foreach($posts as $key => $post)
				{
					$content[] = $this->load->view('custom/entry', $post, true);
				}
				$data['page'] = '<div class="with-teaser">'.implode($content, '').'</div>';
			}
			$data['page'] .= $this->load->view('custom/job-teaser', '', TRUE);
			$this->config->set_item('title', 'Jobs');
			// get header image
			$data['header'] = "kontakt.jpg";
			$news = 'true';
		}
		elseif( $this->navigation->current('path') === '/kontakt' )
		{
			$data['page'] = $this->load->view('custom/kontakt', '', TRUE);
			$data['header'] = "kontakt.jpg";
			$this->config->set_item('title', 'Kontakt');
		}
		elseif( $this->navigation->current('path') === '/contact' )
		{
			$data['page'] = $this->load->view('custom/kontakt_en', '', TRUE);
			$data['header'] = "kontakt.jpg";
			$this->config->set_item('title', 'Contact');
		}
		elseif( $this->navigation->current('path') === '/home' || $this->navigation->current('path') === '/' || $this->navigation->current('path') === '' )
		{
			// add contenmt
			// $data['page'] = '<div class="entry">'.$this->entries->get($this->navigation->current('id'), $this->data, TRUE).'</div>';
			$data['body_id'] = ' id="homepage"';
			// preapre news
			$news = index_array(get_db_data(config('prefix').'news', array('where' => array('language' => $this->config->item('lang_id')), 'select' => '*')), 'type', TRUE);
			// 1 = news, 2 = blog-posts
			$news[2] = sort_array($news[2], 'date');
			ksort($news[2]);
			// build news block
			$data['news'] = '<div class="column news"><a href="'.lang_url().'news"><h4>Neuigkeiten</h4><span class="span-link">Archiv</span></a><div class="items">';
			if( isset($news[1]) && count($news[1]) > 0 )
			{
				// 1 = news, 2 = blog-posts
				$news[1] = sort_array($news[1], 'date');
				ksort($news[1]);
				for($i=0; $i<2; $i++)
				{
					if( isset($news[1][$i]) )
					{
						$nws_items[] = $this->load->view('custom/entry', $news[1][$i], TRUE);
					}
				}
				ksort($nws_items);
				$data['news'] .= implode($nws_items,'');
			}
			$data['news'] .= '</div></div>';
			// build blog block
			$data['blog'] = '<div class="column blog-previews last-child"><a href="'.lang_url().'blog"><h4>Artikel</h4><span class="span-link">Alle Artikel</span></a><div class="items">';
			for($i=0; $i<3; $i++)
			{
				if( isset($news[2][$i]) )
				{
					if( isset($news[2][$i]['long_text']) && $news[2][$i]['long_text'] != "" )
					{
						$news[2][$i]['text'] .= '<a href="./blog/'.$news[2][$i]['id'].'">Artikel öffnen</a>';
					}
					$blog_items[] =  $this->load->view('custom/entry', $news[2][$i], TRUE);
				}
			}
			ksort($blog_items);
			$data['blog'].= implode($blog_items,'').'</div></div>';
			
			$data['main_content'] = $this->entries->get($this->navigation->current('id'), $this->data, TRUE);
			// load view
			$data['page'] = $this->load->view('homepage_'.$this->config->item('lang_abbr'), $data, TRUE);
			
		}
		// news archive
		elseif( $this->navigation->current('path') === '/news' )
		{
			if( $this->navigation->variables(1) !== null )
			{
				$news = get_db_data(config('prefix').'news', array('where' => array('id' => $this->navigation->variables(1)), 'select' => '*'));
				$news[0]['back_link'] = $this->config->item('lang_id') == 1 ? 'Back to articles' : 'Zur Übersicht der Newseinträge';
				$data['page'] = $this->load->view('custom/full_entry', $news[0], TRUE);
				if( isset($news[0]['header']) && $news[0]['header'] !== null )
				{
					$data['header'] = $news[0]['header'];
				}
				$this->config->set_item('title', $news[0]['title']);
			}
			else
			{
				// preapre news
				$news = get_db_data(config('prefix').'news', array('where' => array('language' => $this->config->item('lang_id'), 'type' => 1), 'select' => '*', 'limit' => 100));
				// 1 = news, 2 = blog-posts
				$news = sort_array($news, 'date');
				krsort($news);
				// build news block
				$data['page'] = '<h1>News-Archiv</h1><div class="items news-list">';
				for($i=0; $i<3; $i++)
				{
					if( isset($news[$i]) )
					{
						$nws_items[] = $this->load->view('custom/archive-entry', $news[$i], TRUE);
					}
				}
				krsort($nws_items);
				$data['page'] .= implode($nws_items,'').'</div>';
			}
		}
		// blog archive
		elseif( $this->navigation->current('path') === '/blog' )
		{
			if( $this->navigation->variables(1) !== null )
			{
				$news = get_db_data(config('prefix').'news', array('where' => array('id' => $this->navigation->variables(1)), 'select' => '*'));
				$news[0]['back_link'] = $this->config->item('lang_id') == 1 ? 'Back to articles' : 'Zur Übersicht der Artikel';
				$data['page'] = $this->load->view('custom/article', $news[0], TRUE);
				if( $news[0]['header'] !== null )
				{
					$data['header'] = $news[0]['header'];
				}
				$this->config->set_item('title', $news[0]['title']);
			}
			else
			{
				// preapre news
				$news = get_db_data(config('prefix').'news', array('where' => array('language' => $this->config->item('lang_id'), 'type' => 2), 'select' => '*'));
				// 1 = news, 2 = blog-posts
				$news = sort_array($news, 'date');
				krsort($news);
				// build news block
				$data['page'] = '<h1>Blogartikel</h1><div class="blog-items items">';
				for($i=0; $i<count($news); $i++)
				{
					if( isset($news[$i]) )
					{
						if( isset($news[2][$i]['long_text']) && $news[2][$i]['long_text'] != "" )
						{
							$news[$i]['title'] = '<a href="'.lang_url().'blog/'.$news[$i][id].'">'.$news[$i]['title'].'</a>';
							$news[$i]['text'] = '<a class="no-underlined" href="'.lang_url().'blog/'.$news[$i]['id'].'">'.$news[$i]['text'].'</a>';
						}
						else
						{
							$news[$i]['title'] = $news[$i]['title'];
						}
						$news[$i]['text'] = $news[$i]['text'];
						$nws_items[] =  $this->load->view('custom/entry', $news[$i], TRUE);
					}
				}
				krsort($nws_items);
				$data['page'] .= implode($nws_items,'').'</div>';
			}
		}
		// default case
		if(!isset($data['page']) || $data['page'] == null)
		{
			$data['page'] = '<div class="entry">'.$this->entries->get($this->navigation->current('id'), $this->data, TRUE).'</div>';
			// set description
			if($desc = $this->entries->get_element('description', $this->navigation->current('id')))
			{
			 	$this->config->set_item('description', $desc);		
			}
			$this->config->set_item('title', $this->entries->get_element('title', $this->navigation->current('id')));
			
			if($header = $this->entries->get_element('header', $this->navigation->current('id')))
			{
			 	$data['header'] = $header;
			}
		}
		
		// load view
		view('', $data);
	}
//
}	
/* End of file Base.php */
/* Location: ./application/controllers/Base.php */