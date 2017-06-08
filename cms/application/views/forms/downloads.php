<?
echo '<div id="main_content">
<h1 class="tcd1 icl3">'.$title.'</h1>';

$options = array(
                  'Prospekte'  					=> 'Prospekte',
                  'Ausschreibungstexte' 		=> 'Ausschreibungstexte',
                  'Betriebsanleitung'   		=> 'Betriebsanleitung',
                  'Brochures'  					=> 'Prospekte (EN)',
                  'Specifications' 				=> 'Ausschreibungstexte (EN)',
                  'Manuals'   					=> 'Betriebsanleitung (EN)',
                  'Hidden'   					=> 'Nicht angezeigt'
                );

	echo form_open_multipart(current_url(), array('id' => 'downloads', 'class' => 'bar'));
	
	echo '<div class="inlinelabel form-box input-field">';
	echo form_label('Angezeigter Name', 'upload_name');
	echo form_input(array('name' => 'upload_name', 'id' => 'upload_name', 'class' => 'name', 'value' => $upload_name));
	echo '</div>';

	echo '<div class="form-box select-field select">';
	echo form_dropdown('category', $options);
	echo '</div>';

	echo '<div class="form-box upload-field upload">';	
	echo form_upload('upload_file');
	echo '</div>';
	
	echo '<div class="button input float-right">'.form_submit('submit', 'hochladen', 'class = "tcd3 scd2"').'</span></div>'."\n\t\t";

	echo form_close();
	
	echo $list;
	
echo '</div>';

?>