<?php 
if (! defined('BASEPATH')) exit('No direct script access');

// open class
class Entries extends Controller {

	var $configs = null;
	var $data = null;
	var $client = null;

	//php 5 constructor
	function __construct() 
 	{
		parent::Controller();
	}
	
	//php 4 constructor
	function Entries() 
	{
		parent::Controller();
	}
	
	// setup 
	function setup()
	{
		// client prefix
		$this->client['prefix'] = &$this->config->item('client_prefix');
		// -----------------
		// CSS		
		$this->stylesheet->add('base-0.0.1, form-0.0.1, entries-0.0.1, icons-0.0.1');
		$this->javascript->add('input_file');		
		// -----------------
		// Client Language
		$this->load->library('languages', array('prefix'=>$this->client['prefix']), 'client_lang');
		$this->client['languages'] 		= $this->client_lang->get();
		// $this->client['default_lang'] 	= $this->client_lang->def('id');	
		$this->client['cms_lang']		= $this->client_lang->get_by_abbr($this->config->item('lang_abbr'));
		// -----------------
		// Load Config files
		$this->config->load($this->config->item('client_config_default'), '','',$this->client['prefix']);
		$this->config->load($this->config->item('client_config_database'), '','',$this->client['prefix']);
		$this->config->load($this->config->item('client_config_language'), '','',$this->client['prefix']);	
		$this->config->load('cms_forms');
		// -----------------
		// Prepare Config 
		$this->configs = $this->config->item($this->client['prefix'].'config');	
		$this->configs['forms'] = $this->config->item('form_entries');	
		// -----------------
		// Validation
		$this->load->library('form_validation');
		// Rules
		$this->form_validation->set_rules('content', 'Inhalt', 'trim|required');
		$this->form_validation->set_rules('headline', 'Überschrift', 'trim|required');
		// -----------------
		// Load Language files
		$this->lang->load('form', $this->config->item('language'));
		// -----------------
		// Retrieve config from db
		fetch_config_from_db();
		// /*----------------------------------------------------------------------*/
		// Navigation
		$this->data['main_menu'] = $this->navigation->tree(array('menu' => 'main', 'lvl' =>'2'));
		$this->data['top_right'] = $this->navigation->tree(array('menu' => 'top_right', 'lvl' =>'1'));
		$this->data['footer'] = $this->navigation->tree(array('menu' => 'footer', 'lvl' =>'1'));
		//
		$this->data['breadcrumbs'] = $this->navigation->path(array('path_class' => 'left', 
										'path_before' => '<a href="http://www.veare.net">veare</a>'));
	}
	// --------------------------------------------------------------------
	/**
	 * Overview
	 *
	 * @description	overview over entries, user can edit, add and delete entries as well as filter them
	 */
	function overview($function = null, $id = null)
	{
		// Needs ro be reworked 
		//
		// -----------------
		// run function
		if($function == 'delete-entry'){
			$this->data['message'] = "<div class='notice success'><p>".$this->_delete_entry($id)."</p></div>";
		}
		elseif($function == 'cleanup')
		{
			$this->data['message'] = "<div class='notice success'><p>".$this->_cleanup()."</p></div>";			
		}
		// run setup
		$this->setup();
		// add js
		$this->javascript->add('filter-entries');
		// -----------------
		// define variables
		$display = NULL;
		$max_length = '100';
		$entries = array(NULL);
		$items = $this->get_entry();
		$types = _split_data_new($this->config->item('entry-types'));
		$status = _split_data_new($this->config->item('status'));
		
		$no_add = array('Übersichtsseiten','Inhaltsseiten');
		$no_delete = array('1','2');
		
		
		$languages = $this->client['languages'];
		//
		foreach($types as $group => $item)
		{
			foreach($item as $key => $value)
			{
				$type_by_id[$key] = $value;
			}
		}
		// -----------------
		// Retrive & prepare from Database 
		foreach($types as $value)
		{
			foreach($value as $v)
			{
				if( isset($_COOKIE[replace_accents($v)]) )
				{
					$cur_types[] = replace_accents($v);
				}
			}
		}
		foreach($status as $value)
		{
			foreach($value as $v)
			{
				if( isset($_COOKIE[replace_accents($v)]) )
				{
					$cur_states[] = replace_accents($v);
				}
			}
		}
		foreach($languages as $value)
		{
			if( isset($_COOKIE[replace_accents($value['abbr'])]) )
			{
				$cur_lang[] = replace_accents($value['abbr']);
			}
		}
		
		foreach($items as $id => $item)
		{
			$item['data'] = json_decode($item['data'], TRUE);

			$hidden = '';
			$hidden_style = '';
			
			if(isset($cur_lang) && !in_array(replace_accents($languages[$item['language']]['abbr']), $cur_lang))
			{
				$hidden = ' hidden';
				$hidden_style = 'style="display: none;"';
			}
			if(isset($cur_states) && !in_array(replace_accents($status[$item['status']]['label']), $cur_states))
			{
				$hidden = ' hidden';
				$hidden_style = 'style="display: none;"';
			}
			if(isset($cur_types) && !in_array(replace_accents($type_by_id[$item['type']]), $cur_types))
			{
				$hidden = ' hidden';
				$hidden_style = 'style="display: none;"';
			}
						
			$tmp = "<div class='item id-".$id." ".replace_accents($languages[$item['language']]['abbr'])." ".replace_accents($status[$item['status']]['label'])." ".replace_accents($type_by_id[$item['type']]).$hidden."' ".$hidden_style.">\n
						<div class='header-bar'>\n
							<span class='title'>".$item['title']."</span>
							<span class='last-changed'>".(isset($item['data']['change']) ? 
							"".$item['data']['change'] : ''
							).(isset($item['data']['short_name']) && !empty($item['data']['short_name'])  ? 
							" / <b>".$item['data']['short_name']."</b>" : ''
							)."</span>
							<div class='options'>
								<div class='actions'>
									<a href='".lang_url()."/entries/edit-entry/".$id."'><span class='icon edit float-left' title='bearbeiten'></span></a>
									".((!in_array($item['type'], $no_delete)) ?
										"<a href='".lang_url()."/entries/delete-entry/".$id."'><span class='icon delete float-left' title='löschen'></span></a>" : ''
									)."
								</div>
								<span class='language ".$languages[$item['language']]['abbr']."' title='".$languages[$this->client['cms_lang']['id']]['labels'][$item['language']]."'></span>
								<span class='status' style='background-color:#".$status[$item['status']]['color']."; color:#".$status[$item['status']]['text-color'].";' 
								title='status: ".$status[$item['status']]['label']."'>".$status[$item['status']]['label'].
								"</span>
							</div>
						</div>\n
					</div>\n";
			
			if(isset($entries[$item['type']]))
			{
				$entries[$item['type']] .= $tmp;
			}
			else
			{
				$entries[$item['type']] = $tmp;
			}
		}
		// -----------------
		foreach($types as $category => $types)
		{
			$hidden = '';
			$hidden_style = '';
			// -----------------
			// types
			foreach($types as $id => $type)
			{
				if(isset($entries[$id]))
				{
					$tmp_entries[] = $entries[$id];
				}
				if(isset($cur_types) && !in_array(replace_accents($type), $cur_types))
				{
					$hidden = ' hidden';
					$hidden_style = 'style="display: none;"';
				}
			}
			// check if entry exists
			if(isset($tmp_entries))
			{
				$display .= "<div class='header ".$hidden."' ".$hidden_style.">\n
	
								<h3>".$category."</h3>\n";
								if(!in_array($category, $no_add))
								{
									$display .= "<a href='".lang_url()."/entries/new-entry/".$id."'><span class='icon add float-right'></span></a>\n";
								}
				$display .= "</div>";
				// -----------------
				// items
				$display .= "<div class='items ".strtolower($category)."'>\n"
							.implode($tmp_entries,'').
							"</div>\n";
			// -----------------
			}
			unset($tmp_entries);
		}
		// -----------------
		// Filter Navigation
		$filter = "<div id='entry_filter'>
		<div id='cleanup'><a href='".lang_url()."/entries/cleanup'>Endgültig löschen</a></div>
		<h3>Filter</h3>
		<ul id='filter-all'>
			<li class='deselect'><a href='#all' class='all'>Alles anzeigen</a></li>
		</ul>
		<ul id='filter-status'>
		<lh>Status</lh>";
		foreach($status as $key => $status)
		{
			$cur = isset($_COOKIE[replace_accents($status['label'])]) && $_COOKIE[replace_accents($status['label'])] == true ? ' class="current"' : '';
			$filter.= "<li".$cur."><a href='#".$status['label']."' class='".replace_accents($status['label'])."'>".$status['label']."</a></li>";
		}
		$filter.= "</ul>
		<ul id='filter-language'>
		<lh>Sprache</lh>";
		foreach($languages as $key => $language)
		{
			$cur = isset($_COOKIE[replace_accents($languages[$key]['abbr'])]) && $_COOKIE[replace_accents($languages[$key]['abbr'])] == true ? ' class="current"' : '';
			$filter.= "<li".$cur."><a href='#".$languages[$key]['abbr']."' class='".replace_accents($languages[$key]['abbr'])."'>".$languages[$this->client['cms_lang']['id']]['labels'][$key]."</a></li>";
		}
		$filter.= "</ul>
		<ul id='filter-type'>
		<lh>Seitentyp</lh>";
		foreach($type_by_id as $key => $type)
		{
			$cur = isset($_COOKIE[replace_accents($type)]) && $_COOKIE[replace_accents($type)] == true ? ' class="current"' : '';			
			$filter.= "<li".$cur."><a href='#".$type."' class='".replace_accents($type)."'>".$type."</a></li>";
		}
		$filter.= "</ul></div>";
		// -----------------
		// Title
		$this->data['title'] = "Überblick über die Einträge";		
		// Content	
		$this->data['content'] = "<div id='entries_overview'>".$display."</div>".$filter;
		// -----------------
		// Load Template
		$this->load->view('custom/entries_page', $this->data);	
	}
	// --------------------------------------------------------------------
	/**
	 * New Entry
	 *
	 * @description	
	 */
	function new_entry($type = 3)
	{
		// run setup
		$this->setup();
		// -----------------
		// check if form is submitted		
		if($this->input->post('submit'))
		{
			if( $this->input->post('validate') )
			{
				if( $this->form_validation->run() == FALSE )
				{
					// prepare links	
					$array = $this->input->post('links');
					// prepare links	
					$array['links'] = $this->input->post('links');
					$array['links_download'] = $this->input->post('links_download');
					$array['links_extra'] = $this->input->post('links_extra');
					$array['links_support'] = $this->input->post('links_support');
					
					if(isset($array['links']) && is_array($array['links']))
					{
						foreach($array as $key => $array)
						{
							if(isset($array['url']))
							{
								$count = count($array['url']);
							}
							elseif(isset($array['title']))
							{
								$count = count($array['url']);
							}
						
							$num = $count;
							while($count >= 0)
							{				
								if(!empty($array['url'][$num-$count]) && !empty($array['title'][$num-$count]))
								{
									$data[$key]['url'][] 		= $array['url'][$num-$count];
									$data[$key]['title'][] 		= $array['title'][$num-$count];				
								}
								--$count;
							}
							$data[$key]['active'] = isset($array['active']) ? $array['active'] : '';
							$data[$key]['box-title'] = isset($array['box-title']) ? $array['box-title'] : '';
						}
						// prepare texts
						unset($array);	
						$array = $this->input->post('text');
						$data['text']['active'] = isset($array['active']) ? $array['active'] : '' ;
						$count = is_array($array['title']) ? count($array['title']) : 1;
						while($count >= 0)
						{				
							if(!empty($array['url'][$count]) || !empty($array['title'][$count]) || !empty($array['link'][$count]))
							{
								$data['text']['title'][$count] 		= $array['title'][$count];
								$data['text']['text'][$count] 		= $array['text'][$count];
								$data['text']['link'][$count] 		= $array['link'][$count];		
								$data['text']['link_name'][$count] 	= $array['link_name'][$count];													
							}
							--$count;
						}
					}
					// prepare other values
					$data['artnr'] 			= $this->input->post('artnr');
					$data['description'] 	= $this->input->post('description');
					$data['info_link'] 		= $this->input->post('info_link');
					$data['tool_link'] 		= $this->input->post('tool_link');					
					$data['header_img'] 	= $this->input->post('header_img');			
					$data['product_cat'] 	= $this->input->post('product_cat');	
					$data['family']		 	= $this->input->post('family');
					$data['faq_link'] 		= $this->input->post('faq_link');							
					$data['status'] 		= $this->input->post('status');
					$data['language'] 		= $this->input->post('language');
					$data['short_name'] 	= $this->input->post('short_name');																	
					// error
					$data['notice'] = validation_errors('<div class="notice error"><p>', '</p></div>');
				}
				else
				{	
					$data['notice'] = $this->save();					
					redirect(base_url_lang().'/entries/overview');
				}
			}
			else
			{
				// $data['notice'] = $this->upload_file();
				// $this->save();
				$data['notice'] = $this->save();
				redirect(base_url_lang().'/entries/overview');
			}
		}	
		// -----------------
		// load javascript
		$this->javascript->add('ckeditor/ckeditor');
		$this->javascript->add('ckeditor/adapters/jquery');
		$this->javascript->add('cms.wysiwyg');			
		// -----------------
		// define variables
		foreach($this->client['languages'] as $id => $lang)
		{
			$data['lang_array'][$id] = $this->client['cms_lang']['labels'][$id];								
		}		
		$data['type_array'] 	= array(1 => 'Übersichtsseite', 2 => 'Inhaltsseite', 3 => 'Produktseite', 4 => 'Jobseite', 
										5 => 'Workshop', 6 => 'Aktuelles', 7 => 'Klima Produktkategorie', 8 => 'Spa Produktkategorie', 9 => 'FAQ Seite', 10 => 'Zubehörseite');
		$data['status_array'] 	= array(1 => 'veröffentlicht', 3 => 'gelöscht', 2 => 'Entwurf');

		// defaults
		$data['type'] 			= $type;
		!empty($data['status']) ? '' :	$data['status']	= 1;
		!empty($data['language']) ? '' : $data['language'] = 73;
		!empty($data['family']) ? '' : $data['family'] = 1;
		!empty($data['short_name']) ? '' : $data['short_name'] = '';
		!empty($data['change']) ? '' : $data['change'] = date( "d.m.Y, H:i", time() );

		if($type == 7 || $type == 8)
		{
			$tmp = $this->product_cat($data['language'], '', $type);
			
			$c = count($tmp)+2;
			$data['pos'] = count($tmp)+1;
			
			for($i = 1; $i < $c; $i++ ){
				$data['pos_array'][$i] = $i;
			}
			
		}

		$data['hidden']			= array('validate' => 'true');
		// -----------------
		// forms
		$form[1] = array('title' => 'Neue Übersichtsseite anlegen', 'form' => 'overview_page');
		$form[2] = array('title' => 'Neue Inhaltsseite anlegen', 'form' => 'page');	
		$form[3] = array('title' => 'Neues Produkt anlegen', 'form' => 'product');
		$form[4] = array('title' => 'Neuen Job anlegen', 'form' => 'text_form');
		$form[5] = array('title' => 'Neuen Workshop anlegen', 'form' => 'text_form');
		$form[6] = array('title' => 'Neue Aktuelles erstellen', 'form' => 'text_form');
		$form[7] = array('title' => 'Neue Produktkategorie erstellen', 'form' => 'textarea');
		$form[8] = array('title' => 'Neue Produktkategorie erstellen', 'form' => 'textarea');
		$form[9] = array('title' => 'FAQ Seite bearbeiten', 'form' => 'faq_form');					
		$form[10] = array('title' => 'Zubehörseite bearbeiten', 'form' => 'zubehoer');			
		// -----------------	
		if($type == 2 || $type == 1)
		{
			$data['menu'] = $this->_get_client_menu(array('menu_id' => null, 'entry_lang' => 73));
			$data['status_array'] 	= array(1 => 'veröffentlicht', 3 => 'gelöscht');			
		}
		elseif($type == 3)
		{
			$data['product_cat'] = !empty($entry['product_cat']) ? $entry['product_cat'] : 1;
			$data['product_cat_array'] = $this->product_cat($data['language']);
		}
		// -----------------
		$this->data['title'] = $form[$type]['title'];		
		$this->data['content'] = $this->load->view('forms/'.$form[$type]['form'], $data, TRUE);
		// Page View
		$this->load->view('custom/entries_page', $this->data);
	}
	// --------------------------------------------------------------------
	/**
	 * Edit Entry
	 *
	 * @description	
	 */
	function edit_entry($id)
	{
		// run setup
		$this->setup();
		// -----------------
		// check if form is submitted		
		if($this->input->post('submit'))
		{
			if( $this->input->post('validate') )
			{
				if( $this->form_validation->run() == FALSE )
				{
					// prepare links	
					$array['links'] = $this->input->post('links');
					$array['links_download'] = $this->input->post('links_download');
					$array['links_extra'] = $this->input->post('links_extra');
					$array['links_support'] = $this->input->post('links_support');
					
					if(isset($array['links']) && is_array($array['links']))
					{
						foreach($array as $key => $array)
						{
							$count = count($array['url']);
							$num = $count;
							while($count >= 0)
							{				
								if(!empty($array['url'][$num-$count]) && !empty($array['title'][$num-$count]))
								{
									$data[$key]['url'][] 		= $array['url'][$num-$count];
									$data[$key]['title'][] 		= $array['title'][$num-$count];				
								}
								--$count;
							}
							$data[$key]['active'] = isset($array['active']) ? $array['active'] : '';
							$data[$key]['box-title'] = isset($array['box-title']) ? $array['box-title'] : '';
						}
						// prepare texts
						unset($array);	
						$array = $this->input->post('text');
						$data['text']['active'] = isset($array['active']) ? $array['active'] : '' ;
						$count = is_array($array['title']) ? count($array['title']) : 1;
						while($count >= 0)
						{				
							if(!empty($array['url'][$count]) || !empty($array['title'][$count]) || !empty($array['link'][$count]))
							{
								$data['text']['title'][$count] 		= $array['title'][$count];
								$data['text']['text'][$count] 		= $array['text'][$count];
								$data['text']['link'][$count] 		= $array['link'][$count];
								$data['text']['link_name'][$count] 	= $array['link_name'][$count];				
							}
							--$count;
						}
					}
					// error
					$data['notice'] = validation_errors('<div class="notice error"><p>', '</p></div>');
				}
				else
				{
					// $data['notice'] = $this->upload_file();	
					// $this->save($id);				 
					$data['notice'] = $this->save($id);
				}
			}
			else
			{
				// $data['notice'] = $this->upload_file();	
				// $this->save($id);
				$data['notice'] = $this->save($id);
			}
		}
		// -----------------
		// load javascript
		$this->javascript->add('ckeditor/ckeditor');
		$this->javascript->add('ckeditor/adapters/jquery');
		$this->javascript->add('cms.wysiwyg');			
		// -----------------
		// Retrieve entry from Database		
		$entry 		= $this->get_entry($id);
		$type 		= $entry['type'];
		$db_data	= json_decode($entry['data'], true);
		if(isset($data) && isset($db_data))
		{
			$data = array_merge($db_data, $data);			
		}
		elseif(isset($db_data))
		{
			$data = $db_data;				
		}
		elseif(!isset($db_data) && !isset($data))
		{
			$data = '';
		}
		// -----------------
		// define variables
		foreach($this->client['languages'] as $id => $lang)
		{
			$data['lang_array'][$id] = $this->client['cms_lang']['labels'][$id];								
		}		
		$data['type_array'] 	= array(1 => 'Übersichtsseite', 2 => 'Inhaltsseite', 3 => 'Produktseite', 4 => 'Jobseite', 
										5 => 'Workshop', 6 => 'Aktuelles', 7 => 'Klima Produktkategorie', 8 => 'Spa Produktkategorie', 9 => 'FAQ Seite', 10 => 'Zubehörseite');
		$data['status_array'] 	= array(1 => 'veröffentlicht', 3 => 'gelöscht', 2 => 'Entwurf');
		
		if($type == 7 || $type == 8)
		{
			$tmp = $this->product_cat($entry['language'], '', $type);
			
			$c = count($tmp)+1;
			for($i = 1; $i < $c; $i++ ){
				$data['pos_array'][$i] = $i;
			}
			empty($data['pos']) ? $data['pos'] = count($tmp) : '';
		}
		elseif($type == 3)
		{
			$tmp = $this->get_entry('', $type);
			$i = 0;
			foreach($tmp as $id => $v)
			{
				if($v['data']['product_cat'] == $data['product_cat'])
				{
					$i++;
					$data['pos_array'][$i] = $i;
				}
			}
			empty($data['pos']) ? $data['pos'] = $i : '';
		}
		
		// data from entry
		$data['title'] 			= $entry['title'];
		$data['content'] 		= $entry['content'];		
		$data['excerpt'] 		= $entry['excerpt'];		
		$data['type'] 			= $type;
		$data['status']			= $entry['status'];
		$data['language']		= $entry['language'];
		$data['hidden']			= array('validate' => 'true');	
		empty($data['change']) ? $data['change'] = date( "d.m.Y, H:i", time() ) : '';
		empty($data['short_name']) ? $data['short_name'] = '' : $data['short_name'] = $data['short_name'];	
		// -----------------
		// forms
		$form[1] = array('title' => 'Übersichtsseite bearbeiten', 'form' => 'overview_page');
		$form[2] = array('title' => 'Inhaltsseite bearbeiten', 'form' => 'page');	
		$form[3] = array('title' => 'Produkt bearbeiten', 'form' => 'product');
		$form[4] = array('title' => 'Job bearbeiten', 'form' => 'text_form');
		$form[5] = array('title' => 'Workshop bearbeiten', 'form' => 'text_form');
		$form[6] = array('title' => 'Aktuelles bearbeiten', 'form' => 'text_form');
		$form[7] = array('title' => 'Produktkategorie bearbeiten', 'form' => 'textarea');
		$form[8] = array('title' => 'Produktkategorie bearbeiten', 'form' => 'textarea');
		$form[9] = array('title' => 'FAQ Seite bearbeiten', 'form' => 'faq_form');		
		$form[10] = array('title' => 'Zubehörseite bearbeiten', 'form' => 'zubehoer');	
		// -----------------	
		if($type == 2 || $type == 1)
		{
			$data['menu'] = $this->_get_client_menu(array('menu_id' => $entry['menu_id'], 'entry_lang' => $entry['language']));
			$data['status_array'] 	= array(1 => 'veröffentlicht', 3 => 'gelöscht');			
		}
		elseif($type == 3)
		{
			$data['product_cat_array'] = $this->product_cat($entry['language']);
			$data['product_cat'] = !empty($data['product_cat']) ? $data['product_cat'] : key($data['product_cat_array']);
		}
		// -----------------
		$this->data['title'] = $form[$type]['title'];		
		$this->data['content'] = $this->load->view('forms/'.$form[$type]['form'], $data, TRUE);
		// Page View
		$this->load->view('custom/entries_page', $this->data);
	}
	// --------------------------------------------------------------------
	/**
	 * Templates
	 *
	 * @description	
	 */
	function templates()
	{
		// run setup
		$this->setup();
		// -----------------
		// Title
		$this->data['title'] = "Vorlagen";
		//				
		$this->data['content'] = "<h1 class='tcd1 icl1'>Vorlagen</h1>
		<p>Hier können später Templates verwaltet und erstellt werden.</p>";		
		$this->load->view('custom/entries_page', $this->data);
	}
	// --------------------------------------------------------------------
	/**
	 * Channels
	 *
	 * @description	
	 */	
	function channels()
	{
		// run setup
		$this->setup();
		// -----------------
		// Title
		$this->data['title'] = "Kanäle";
		//		
		$this->data['breadcrumbs'] .= "<span class='arrow'>&raquo;</span><span>".$this->data['title']."</span>";		
		$this->data['content'] = "<h1 class='tcd1 icl1'>Kanäle</h1>
		<p>Hier können später \"Kanäle\" für Inhalte erstellt werden. Ein Kanal eine Sammlung in die Sie immer neue Einträge Hinzufügen können, ähnlich eines Blogs oder einer Newsseite.</p>";	
		
			
		$this->load->view('custom/entries_page', $this->data);
	}
	// ############################################################################################################################
	// functions
	// --------------------------------------------------------------------
	/**
	 * Delete Entry
	 *
	 * @description	
	 */
	function _delete_entry($id = NULL)
	{
		if( isset($id) )
		{
			// run setup
			$this->setup();
			// ----------------
			// run queries
			//
			// :::::::: use active record for queries and build query method to optimize ::::::
			//
			// retrieve entry data
			$this->db->where('id', $id);
			$query = $this->db->get($this->config->item('client_prefix').$this->config->item('db_entries'));
			$result = $query->result();
			// update menu table
			$this->db->where('id', $result[0]->menu_id);
			$this->db->update($this->config->item('client_prefix').$this->config->item('db_menu'), array('status' => 3));	
			// update entry table
			$this->db->where('id', $id);	
			$this->db->update($this->config->item('client_prefix').$this->config->item('db_entries'), array('status' => 3));		
			// -----------------		
			// message
			if($result[0]->title)
			{
				return sprintf(lang('deleted'), $result[0]->title);
			}
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Save Entry
	 *
	 * @description	saves the edited or new entry to the database
	 */
	function save($id = null)
	{ 	
		$data['type'] 			= $this->input->post('type');
		$data['menu_id'] 		= $this->input->post('menu_id');
		$data['date'] 			= date("Y-m-d G:i:s");
		$data['title'] 			= $this->input->post('headline');
		$data['content'] 		= $this->input->post('content');
		$data['excerpt'] 		= $this->input->post('excerpt');
		$data['status']		 	= $this->input->post('status');	
		$data['language']		= $this->input->post('language');			

		$head_img_tmp			= $this->upload_file();
		// -----------------
		// Retrieve entry from Database		
		$entry 		= $this->get_entry($id);
		if(isset($entry['data']))
		{
			$tmp_data = json_decode($entry['data'], true);				
		}
		else
		{
			$tmp_data = NULL;
		}
		
		if(is_array($head_img_tmp)) // && empty($tmp_data['header_img']['name'])
		{
			$tmp['header_img'] 		= $head_img_tmp;
		}
		else
		{
			$tmp['header_img'] 	= !empty($tmp_data['header_img']) ? $tmp_data['header_img'] : '';
		}

		$tmp['keywords']	 	= $this->input->post('keywords');
		$tmp['template']	 	= $this->input->post('template');
		$tmp['channel']		 	= $this->input->post('channel');
		$tmp['description'] 	= $this->input->post('description');
		$tmp['artnr'] 			= $this->input->post('artnr');
		$tmp['info_link'] 		= $this->input->post('info_link');
		$tmp['tool_link'] 		= $this->input->post('tool_link');
		$tmp['product_cat'] 	= $this->input->post('product_cat');
		$tmp['family'] 			= $this->input->post('family');
		$tmp['pos'] 			= $this->input->post('pos');
		$tmp['short_name'] 		= $this->input->post('short_name');	
		$tmp['change'] 			= date( "d.m.Y, H:i", time() );
		
		$_faq = $this->input->post('faq_link');
		
		if(!empty($id))
		{
			if($data['language'] == 73)
			{
				$tmp['faq_link'] 		= $this->config->item('client_base_url').'de/support/faq/'.$id;
			}
			else
			{
				$tmp['faq_link'] 		= $this->config->item('client_base_url').'en/support/faq/'.$id;
			}	
		}
		else
		{
			if($data['language'] == 73)
			{
				$tmp['faq_link'] 		= $this->config->item('client_base_url').'/de/support/faq/';
			}
			else
			{
				$tmp['faq_link'] 		= $this->config->item('client_base_url').'en/support/faq/';
			}
							
		}
				
		// prepare links	
		$tmp_array['links'] = $this->input->post('links');
		$tmp_array['links_download'] = $this->input->post('links_download');
		$tmp_array['links_extra'] = $this->input->post('links_extra');
		$tmp_array['links_support'] = $this->input->post('links_support');
		
		if(isset($tmp_array) && is_array($tmp_array))
		{
			foreach($tmp_array as $key => $array)
			{
				$count = count($array['url']);

				while($count > 0)
				{			
					--$count;
					if(!empty($array['url'][$count]) && !empty($array['title'][$count]))
					{
						$tmp[$key]['url'][$count] 		= $array['url'][$count];
						$tmp[$key]['title'][$count] 	= $array['title'][$count];				
					}
				}
				if(isset($tmp[$key]) && is_array($tmp[$key]['url']) && is_array($tmp[$key]['title']) )
				{
					array_values($tmp[$key]['url']);
					array_values($tmp[$key]['title']);
				}
			
				$tmp[$key]['active'] = isset($array['active']) ? $array['active'] : '';
				$tmp[$key]['box-title'] = isset($array['box-title']) ? $array['box-title'] : '';
			}	
			// prepare text	
			$array = $this->input->post('text');
			$count = count($array['title']);
			$num = $count;		
			while($count >= 0)
			{				
				if(!empty($array['text'][$num-$count]) && !empty($array['text'][$num-$count]))
				{
					$tmp['text']['text'][] 		= $array['text'][$num-$count];
					$tmp['text']['title'][] 	= $array['title'][$num-$count];
					$tmp['text']['link'][] 		= $array['link'][$num-$count];		
					$tmp['text']['link_name'][] = $array['link_name'][$num-$count];														
				}
				--$count;
			}
			$tmp['text']['active'] = isset($array['active']) ? $array['active'] : '';		
		}
		// json_encode data
		$data['data'] = json_encode($tmp);
		
		//		
		if(isset($id))
		{
			// ----------------
			// run queries			
			// update entry table
			$this->db->where('id', $id);	
			$this->db->update($this->config->item('client_prefix').$this->config->item('db_entries'), $data);
			// update menu table
			$this->db->where('id', $data['menu_id']);	
			$this->db->update($this->config->item('client_prefix').$this->config->item('db_menu'), array('status' => 1));
		}
		else
		{
			// insert into entry table
			$this->db->insert($this->config->item('client_prefix').$this->config->item('db_entries'), $data); 
			//
			$tmp['faq_link'] .= $this->db->insert_id();
			$data['data'] = json_encode($tmp);
			$this->db->where('id', $this->db->insert_id());
			$this->db->update($this->config->item('client_prefix').$this->config->item('db_entries'), array('data' => $data['data']));
			// update menu table
			$this->db->where('id', $this->input->post('menu_id'));	
			$this->db->update($this->config->item('client_prefix').$this->config->item('db_menu'), array('status' => 1));
			

		}

		
		//
	}
	// --------------------------------------------------------------------
	/**
	 * Get Entry
	 *
	 * @description	gets entries from database
	 */		
	function get_entry($id = null, $type = null)
	{
		$this->db->select('id, menu_id, title, type, status, language, content, excerpt, date, data');		
		if(isset($id) && !empty($id))
		{
			if(is_array($id))
			{
				$this->db->where_in('id', $id);
			}
			else
			{
				$this->db->where('id', $id);
			}
		}
		
		if(isset($type) && !empty($type))
		{
			$this->db->where('type', $type);
		}
		$this->db->order_by('title');
		$this->db->from($this->config->item('client_prefix').$this->config->item('db_entries'));
		
		$query = $this->db->get();
		
		foreach ($query->result() as $row)
		{
			// indexed by id only
			$items[$row->id] = array(
				'id' 		=> $row->id,
				'menu_id' 	=> $row->menu_id,
				'title' 	=> $row->title,
				'type' 		=> $row->type,
				'status' 	=> $row->status,
				'language' 	=> $row->language,								
				'content' 	=> $row->content,
				'excerpt' 	=> $row->excerpt,
				'date' 		=> $row->date,
				'data' 		=> $row->data							
			);
			if(isset($type) && !empty($type))
			{
				$items[$row->id]['data'] = json_decode($row->data, true);
			}
		}
		//
		if(!is_array($id) && !empty($id))
		{
			return $items[$id];
		}
		else
		{
			return $items;			
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Cleanup
	 *
	 * @description	deletes all delete entries from trash
	 */
	function _cleanup()
	{
		$this->db->where('status', 3);	
		$this->db->delete($this->config->item('client_prefix').$this->config->item('db_entries'));
		// cleanup database ids
		$this->db->query('ALTER TABLE '.$this->config->item('client_prefix').$this->config->item('db_entries').' AUTO_INCREMENT = 1');	
		//
		return "Trash cleared.";
	}
	// --------------------------------------------------------------------
	/**
	 * Get Client Menu
	 *
	 * @description	gets the clients menu
	 */	
	function _get_client_menu($params = null)
	{
		// -----------------
		// Load Libraries
		$this->load->library('navigation',array('db_table' => $this->client['prefix'].$this->configs['db_menu']),'client_menu');
		// -----------------
		// initialize $client_menu
		$client_menu = null;
		// get menus by language
		foreach($this->client['languages'] as $id => $lang)
		{
			$array = array('language' => $lang['id'], 'list_id' => 'menu_'.$lang['abbr'], 'fn' =>'_item_select', 'menu_id' => $params['menu_id'], 'menu' => 'main', 'status' => 'all');
			$params['entry_lang'] == $lang['id'] ? $array['list_class'] = 'menu active' : '';
			//
			$client_menu .= $this->client_menu->tree($array);
			//
			$array['menu'] = 'footer';
			$client_menu .= $this->client_menu->tree($array);
			//
			$array['menu'] = 'meta';
			$client_menu .= $this->client_menu->tree($array);
		}
		// -----------------
		return $client_menu;
	}
	// --------------------------------------------------------------------
	/**
	 * Upload File
	 *
	 * @description	uploads file
	 */	
	function upload_file()
	{
		// --------------
		$config['upload_path'] = $this->config->item('client_folder').'/media/images/';
		$config['allowed_types'] = 'jpg|png';
		$config['max_size']	= '10000';
		$config['max_width']  = '900';
		$config['max_height']  = '300';
				
		$this->load->library('upload', $config);
		
		if ( $this->upload->do_upload('header_img'))
		{
			$data = $this->upload->data();

			$insert = array(
					'name' 	=> $this->input->post('headline'),
					'file' 	=> $data['file_name'],
					'type' 	=> $data['file_type']
				);
				
			$insert['data']	= json_encode(array(
				'relative_path' => $config['upload_path'],
				'size' 			=> $data['file_size'],
				'width' 		=> $data['image_width'],
				'height' 		=> $data['image_height']
				));
			// -------------------------------------------------
			// insert into entry table
			$this->db->insert($this->config->item('client_prefix').$this->config->item('db_files'), $insert); 
			return array('id' => $this->db->insert_id(), 'name' =>$data['file_name']);
		}
	}
	// --------------------------------------------------------------------
	/**
	 * Get Product Kategory
	 *
	 * @description
	 */
	function product_cat($lang, $id = Null, $type = null)
	{
		$this->db->select('id, title, type, language');	
		if($type == null || empty($type))
		{	
			$this->db->where('type', 7);	
			$this->db->or_where('type', 8);	
		}
		else
		{
			$this->db->where('type', $type);			
		}
		$this->db->order_by('title','asc');
		$this->db->from($this->config->item('client_prefix').$this->config->item('db_entries'));
		
		$query = $this->db->get();
		
		foreach ($query->result() as $row)
		{
			// indexed by id only
			if($lang == $row->language)
			{
				($row->type == 8) ? $type = 'Spa: ' : $type = 'Klima: ';
				$items[$row->id] = $type.$row->title;										
			}
			
		}
					
		if($id != NULL)
		{
			return $items['id'];	
		}
		elseif(isset($items))
		{
			return $items;
		}
		else
		{
			return FALSE;
		}
	}
// close controller
}

/* End of file pages.php */
/* Location: ./application/formandsystem/controllers/pages.php */		