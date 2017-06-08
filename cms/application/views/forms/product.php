<?php if (! defined('BASEPATH')) exit('No direct script access');

// ------------------------------
// define variables
$attributes			= isset($attributes) ? $attributes : null;
$hidden				= isset($hidden) ? $hidden : null;
$legend_left 		= isset($legend_left) ? $legend_left : null;
$attributes_left 	= isset($attributes_left) ? $attributes_left : null;
$legend_right 		= isset($legend_right) ? $legend_right : null;
$attributes_right 	= isset($attributes_right) ? $attributes_right : null;
$legend_both 		= isset($legend_both) ? $legend_both : null;
$attributes_both 	= isset($attributes_both) ? $attributes_both : null;

$title 				= isset($title) ? $title : null;
$content			= isset($content) ? $content : null;
$artnr 				= isset($artnr) ? $artnr : null;
$description		= isset($description) ? $description : null;
$info_link			= isset($info_link) ? $info_link : null;
$tool_link			= isset($tool_link) ? $tool_link : null;
$product_details	= isset($excerpt) ? $excerpt : null;
$header_img			= isset($header_img['name']) ? $header_img['name'] : null;

!isset($attributes_left['class']) ? $attributes_left['class'] = ' fieldset left' : $attributes_left['class'] .= ' fieldset left';
!isset($attributes_right['class']) ? $attributes_right['class'] = ' cms-box fieldset right' : $attributes_right['class'] .= ' cms-box fieldset right';
!isset($attributes_both['class']) ? $attributes_both['class'] = ' fieldset both' : $attributes_both['class'] .= ' fieldset both';
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
		echo form_label('Produktbeschreibung', 'content');
		echo form_textarea('content', set_value('content', $content), 'class="wysiwyg"');
		echo '</div>';
		
		echo '<div class="form-box textarea-field textarea">';
		echo form_label('Technische Details', 'excerpt');
		echo form_textarea('excerpt', set_value('excerpt', $product_details), 'class="wysiwyg"');
		echo '</div>';

	echo form_fieldset_close("\n");
// ------------------------------
// Fieldset right
	echo form_fieldset($legend_right, $attributes_right)."\n\t";
		
		echo '<div class="button input float-left">'.form_submit('submit', 'speichern', 'class = "save tcd3 scd2"').'</div>'."\n\t\t";		
		echo '<div class="button input reset float-left"><a href="'.lang_url().'/entries">Abbrechen</a></div>'."\n\t\t";

		echo '<div class="form-box input-field input inlinelabel">';
		echo form_label('Artikelnummer', 'artnr');
		echo form_input('artnr', $artnr);
		echo '</div>';
		
		echo '<div class="form-box textarea-field textarea inlinelabel">';
		echo form_label('Kurzbeschreibung', 'description');
		echo form_textarea(array("name" => "description", "value" => $description, "class" => "max-150") );
		echo '</div>';
		
		if(isset($pos_array) && is_array($pos_array))
		{
			echo '<div class="form-box select-field select">';
			echo form_label('Position', 'pos');
			echo form_dropdown('pos', $pos_array, $pos);
			echo '</div>';
		}
		
		echo '<div class="form-box select-field select">';
		echo form_label('Produktkategorie', 'product_cat');
		echo form_dropdown('product_cat', $product_cat_array, $product_cat);
		echo '</div>';

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

		echo '<div class="form-box input-field input upload-file">';
		echo form_label('Titelbild', 'header_img');
		echo "<br class='clear' />";
		echo form_upload('header_img');
		echo "<span class='show-file'>".$header_img."</span>";
		echo '</div>';

		echo '<div class="form-box input-field input">';
		echo form_label('Link zur Infografik', 'info_link');
		echo form_input('info_link', $info_link);
		echo '</div>';		
		
		echo '<div class="form-box input-field input">';
		echo form_label('Kürzel', 'short_name');
		echo form_input('short_name', set_value('short_name', $short_name));
		echo '</div>';
		
		echo '<div class="form-box input">';
		echo form_label('Letzte Änderung', 'change');
		echo '<input type="text" value="'.$change.'" disabled=disabled />';
		echo '</div>';
		
		echo '<div class="form-box input-field input">';
		echo form_label('Link zum Auslegungstool', 'tool_link');
		echo form_input('tool_link', $tool_link);
		echo '</div>';

	echo form_fieldset_close("\n");
// ------------------------------
// Fieldset both
echo form_fieldset($legend_both, $attributes_both)."\n\t";

	echo '<div class="box-field link-box">';
		echo form_label("<h4>".form_checkbox('links_download[active]','Aktivieren', (isset($links_download['active']) && $links_download['active'] == TRUE ? TRUE : FALSE) )."Download-Links</h4>");
	
		$count_a = isset($links_download['title'] ) && is_array($links_download['title']) ? count($links_download['title']) : 1;
		$count_b = isset($links_download['url'] ) && is_array($links_download['url']) ? count($links_download['url']) : 1;
		$count = ($count_a > $count_b) ? $count_a : $count_b;

		$i = 0;
		while($i < $count)
		{
		echo '<div class="one-link-box">';
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Name des Links', 'links_download[title]['.$i.']');
			echo form_input('links_download[title]['.$i.']', set_value('links_download[title]['.$i.']', (isset($links_download['title'][$i]) ? $links_download['title'][$i] : '') ), "class = 'link-title'");
			echo '</div>';
	
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Link', 'links_download[url]['.$i.']');
			echo form_input('links_download[url]['.$i.']', set_value('links_download[url]['.$i.']', (isset($links_download['url'][$i]) ? $links_download['url'][$i] : '') ), "class = 'link-url'");
			echo '</div>';
		echo '</div>';
		++$i;
		}

	echo '</div>';

	echo '<div class="box-field link-box">';
		echo form_label("<h4>".form_checkbox('links_extra[active]','Aktivieren', (isset($links_extra['active']) && $links_extra['active'] == TRUE ? TRUE : FALSE) )."Zubehör-Links</h4>");
	
		$count_a = isset($links_extra['title'] ) && is_array($links_extra['title']) ? count($links_extra['title']) : 1;
		$count_b = isset($links_extra['url'] ) && is_array($links_extra['url']) ? count($links_extra['url']) : 1;
		$count = ($count_a > $count_b) ? $count_a : $count_b;
		$i = 0;
		while($i < $count)
		{
		echo '<div class="one-link-box">';
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Name des Links', 'links_extra[title]['.$i.']');
			echo form_input('links_extra[title]['.$i.']', set_value('links_extra[title]['.$i.']', (isset($links_extra['title'][$i]) ? $links_extra['title'][$i] : '') ), "class = 'link-title'");
			echo '</div>';
	
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Link', 'links_extra[url]['.$i.']');
			echo form_input('links_extra[url]['.$i.']', set_value('links_extra[url]['.$i.']', (isset($links_extra['url'][$i]) ? $links_extra['url'][$i] : '') ), "class = 'link-url'");
			echo '</div>';
		echo '</div>';
		++$i;
		}
		unset($count, $count_a, $count_b);
	echo '</div>';

	echo '<div class="box-field link-box">';
		echo form_label("<h4>".form_checkbox('links_support[active]','Aktivieren', (isset($links_support['active']) && $links_support['active'] == TRUE ? TRUE : FALSE) )."Support-Link</h4>");
	
		$count_a = isset($links_support['title'] ) && is_array($links_support['title']) ? count($links_support['title']) : 1;
		$count_b = isset($links_support['url'] ) && is_array($links_support['url']) ? count($links_support['url']) : 1;
		$count = ($count_a > $count_b) ? $count_a : $count_b;

		$i = 0;
		while($i < $count)
		{
		echo '<div class="one-link-box">';
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Name des Links', 'links_support[title]['.$i.']');
			echo form_input('links_support[title]['.$i.']', set_value('links_support[title]['.$i.']', (isset($links_support['title'][$i]) ? $links_support['title'][$i] : '') ), "class = 'link-title'");
			echo '</div>';
	
			echo '<div class="form-box input-field input inlinelabel">';
			echo form_label('Link', 'links_support[url]['.$i.']');
			echo form_input('links_support[url]['.$i.']', set_value('links_support[url]['.$i.']', (isset($links_support['url'][$i]) ? $links_support['url'][$i] : '') ), "class = 'link-url'");
			echo '</div>';
		echo '</div>';
		++$i;
		}

	echo '</div>';
echo form_fieldset_close("\n");
// ------------------------------
echo form_close();
