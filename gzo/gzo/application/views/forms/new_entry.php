<div id="entry_papers">
	<div id="entry_container">
		<?
		// open new form
		echo form_open(current_url(), array('id' => 'entry', 'class'=>'entry-form'));
		// top options
		echo "<div id='entry_form_top'>
				<div id='log' class='icon edit-small'>
					<span id='modified'>24.08.2011</span>
					by <span id='editor'>Lukas Oppermann</span>
				</div>
				<div id='top_options'><span id='publish' class='icon ".$state."'></span></div>
			</div>";
		// hidden fields
		echo form_hidden(array('entry_id' => variable($id), 'language' => variable($data['language']), 'status' => $data['status'], 'menu_id' => variable($menu_id) ));
		// title input
		echo form_input(array(	'name'  		=> 'title',
          						'id'    		=> 'title',
          						'value' 		=> set_value('title',variable($title)),
								'placeholder' 	=> 'Titel des Eintrages',
								'class' 		=> 'input-hidden title'));
		// content textarea
		echo '<div class="form-element">';
			echo form_textarea(array(	'name'  		=> 'text',
			          					'id'    		=> 'text',
			          					'value' 		=> set_value('text',variable($text)),
										'placeholder' 	=> 'Hier klicken um den Text hinzuzufügen',
										'class' 		=> 'input-hidden text wysiwyg'));
		echo '</div>';
		// tags textarea
		echo '<div class="form-element">';
			echo form_label('Tags (mit Komma getrennt eingeben)', 'tags', array('id' => 'tags_label', 'class' => 'icon tags'));
			echo form_textarea(array(	'name'  		=> 'tags',
		          						'id'    		=> 'tags',
		          						'value' 		=> set_value('tags',variable($data['tags'])),
										'placeholder' 	=> 'Tags mit Komma getrennt eingeben',
										'class' 		=> 'input-hidden tags'));
		echo '</div>';
		// excerpt textarea
		echo '<div class="form-element-half">';
			echo form_label('Auszug', 'excerpt', array('id' => 'excerpt'));
			echo form_textarea(array(	'name'  		=> 'excerpt',
		          						'id'    		=> 'excerpt',
		          						'value' 		=> set_value('excerpt',variable($data['excerpt'])),
										'placeholder' 	=> 'Auszug aus dem Artikel',
										'class' 		=> 'input-hidden half excerpt'));
		echo '</div>';
		// description textarea
		echo '<div class="form-element-half">';
			echo form_label('Kurzbeschreibung', 'description', array('id' => 'description'));
			echo form_textarea(array(	'name'  		=> 'description',
		          						'id'    		=> 'description',
		          						'value' 		=> set_value('description',variable($data['description'])),
										'placeholder' 	=> 'Kurzbeschreibung für Suchmaschinen',
										'class' 		=> 'input-hidden half description'));
		// robots options
		echo '<span id="robots" class="icon robots">finden & weiter suchen</span>';
		echo '</div>';
		?>	
	</div>
</div>