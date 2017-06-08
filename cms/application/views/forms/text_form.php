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

	echo form_fieldset_close("\n");
// ------------------------------
echo form_close();
