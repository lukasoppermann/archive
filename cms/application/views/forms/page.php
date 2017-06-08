<?php if (! defined('BASEPATH')) exit('No direct script access');

// ------------------------------
// define variables
$attributes			= isset($attributes) ? $attributes : null;
$hidden				= isset($hidden) ? $hidden : null;
$legend_left 		= isset($legend_left) ? $legend_left : null;
$attributes_left 	= isset($attributes_left) ? $attributes_left : null;
$legend_right 		= isset($legend_right) ? $legend_right : null;
$attributes_right 	= isset($attributes_right) ? $attributes_right : null;

$title 				= isset($title) ? $title : null;
$content			= isset($content) ? $content : null;


!isset($attributes_left['class']) ? $attributes_left['class'] = ' fieldset left' : $attributes_left['class'] .= ' fieldset left';
!isset($attributes_right['class']) ? $attributes_right['class'] = ' cms-box fieldset right' : $attributes_right['class'] .= ' cms-box fieldset right';
// ------------------------------
echo "<div id='notice'>".(!empty($notice) ? $notice : '')."</div>";
echo form_open_multipart(current_url(), $attributes, $hidden);
// ------------------------------
// Fieldset left
	echo form_fieldset($legend_left, $attributes_left)."\n\t";
	
		echo '<div class="form-box input-field input inlinelabel">';
		echo form_label('Überschrift', 'headline');
		echo form_input('headline', set_value('headline', $title));
		echo '</div>';

		echo '<div class="form-box textarea-field textarea">';
		echo form_label('Inhalt', 'content');
		echo form_textarea('content', set_value('content', $content), 'class = "wysiwyg" id="wysiwyg"');
		echo '</div>';
		
		echo '<div class="box-field link-box">';
			echo form_label("<h4>".form_checkbox('links[active]','Aktivieren', (isset($links['active']) && $links['active'] == TRUE ? TRUE : FALSE) )."Link Box</h4>");
			
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Titel der Box', 'links[box-title]');
			echo form_input('links[box-title]', set_value('links[box-title]', (isset($links['box-title']) ? $links['box-title'] : '') ));
			echo '</div>';
			
			echo '<hr class="line" />';
			
			$count_a = isset($links['title'] ) && is_array($links['title']) ? count($links['title']) : 1;
			$count_b = isset($links['url'] ) && is_array($links['url']) ? count($links['url']) : 1;
			$count = ($count_a > $count_b) ? $count_a : $count_b;
			
			$i = 0;
			while($i < $count)
			{
			echo '<div class="one-link-box">';
				echo '<div class="form-box input-field input inlinelabel">';
				echo form_label('Name des Links', 'links[title]['.$i.']');
				echo form_input('links[title]['.$i.']', set_value('links[title]['.$i.']', (isset($links['title'][$i]) ? $links['title'][$i] : '') ), "class = 'link-title'");
				echo '</div>';
			
				echo '<div class="form-box input-field input inlinelabel">';
				echo form_label('Link', 'links[url]['.$i.']');
				echo form_input('links[url]['.$i.']', set_value('links[url]['.$i.']', (isset($links['url'][$i]) ? $links['url'][$i] : '') ), "class = 'link-url'");
				echo '</div>';
			echo '</div>';
			++$i;
			}

		echo '</div>';

		echo '<div class="box-field float-right">';
		
		$i = 0;

			echo form_label("<h4>".form_checkbox('text[active]','Aktivieren', (isset($text['active']) && $text['active'] == TRUE ? TRUE : FALSE))."Text Box</h4>");

			while($i <= 2)
			{		
			echo ($i > 0 ? '<hr class="line" />' : '');
							
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Titel', 'text[title]['.$i.']');
			echo form_input('text[title]['.$i.']', set_value('text[title]['.$i.']', (isset($text['title'][$i]) ? $text['title'][$i] : '')));
			echo '</div>';
			
			echo '<div class="form-box textarea-field textarea inlinelabel">';
			echo form_label('Text', 'text[text]['.$i.']');
			echo form_textarea('text[text]['.$i.']', set_value('text[text]['.$i.']', (isset($text['text'][$i]) ? $text['text'][$i] : '')));
			echo '</div>';
			
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Name des Links', 'text[link_name]['.$i.']');
			echo form_input('text[link_name]['.$i.']', set_value('text[link_name]['.$i.']', (isset($text['link_name'][$i]) ? $text['link_name'][$i] : '')));
			echo '</div>';

			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Link', 'text[link]['.$i.']');
			echo form_input('text[link]['.$i.']', set_value('text[link]['.$i.']', (isset($text['link'][$i]) ? $text['link'][$i] : '')));
			echo '</div>';
			++$i;
		}	
				
		echo '</div>';

	echo form_fieldset_close("\n");
// ------------------------------
// Fieldset right
	echo form_fieldset($legend_right, $attributes_right)."\n\t";
		
		echo '<div class="button input float-left">'.form_submit('submit', 'speichern', 'class = "save tcd3 scd2"').'</div>'."\n\t\t";		

		echo '<div class="button input reset float-left"><a href="'.lang_url().'/entries">Abbrechen</a></div>'."\n\t\t";

		echo '<div class="form-box select-field select">';
		echo form_label('Status', 'status');
		echo form_dropdown('status', $status_array, $status);
		echo '</div>';
		
		echo '<div class="form-box select-field select">';
		echo form_label('Seitentyp', 'type');
		echo form_dropdown('type', $type_array, $type, 'id="type" class="select-field select"');
		echo '</div>';
		
		echo '<div class="form-box select-field select">';
		echo form_label('Sprache', 'language');
		echo form_dropdown('language', $lang_array, $language);
		echo '</div>';
		
		echo '<div class="form-box input-field input">';
		echo form_label('Kürzel', 'short_name');
		echo form_input('short_name', set_value('short_name', $short_name));
		echo '</div>';
		
		echo '<div class="form-box input">';
		echo form_label('Letzte Änderung', 'change');
		echo '<input type="text" value="'.$change.'" disabled=disabled />';
		echo '</div>';
		
		echo '<div class="var var-field form-box">';		
		echo '<h4>'.form_label('Menu', 'menu_id').'</h4>';
		echo $menu;
		echo '</div>';
		

	echo form_fieldset_close("\n");
// ------------------------------
echo form_close();
