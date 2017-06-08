<?
$options = array(
                  'Prospekte'  					=> 'Prospekte',
                  'Ausschreibungstexte' 		=> 'Ausschreibungstexte',
                  'Betriebsanleitung'   		=> 'Betriebsanleitung',
                  'Brochures'  					=> 'Prospekte (EN)',
                  'Specifications' 				=> 'Ausschreibungstexte (EN)',
                  'Manuals'   					=> 'Betriebsanleitung (EN)',
                 'Hidden'   					=> 'Nicht angezeigt'
                );

echo form_open(current_url(), array('class' => 'bar edit-form'), array('id' => $id, 'full_path' => $path.$file));

echo '<div class="inlinelabel form-box input-field">';
echo form_label('Angezeigter Name', 'name');
echo form_input(array('name' => 'name', 'id' => 'name', 'class' => 'name', 'value' => $name));
echo '</div>';

echo '<div class="form-box select-field select">';
echo form_dropdown('category', $options, $category);
echo '</div>';

echo '<div class="form-box input-field path">';	
echo '<span class="path" title="'.$file.'">...'.substr($file,0,30).'</span>';
echo '<div class="button copy-icon float-right">'.clipit($path.$file).'</div>'."\n\t\t";
echo '</div>';

echo '<div class="button delete input float-right">'.form_submit('submit', 'löschen', 'class = "delete tcd3 scd2"').'</div>'."\n\t\t";

echo '<div class="button input float-right">'.form_submit('submit', 'speichern', 'class = "save tcd3 scd2"').'</div>'."\n\t\t";

echo form_close();

?>