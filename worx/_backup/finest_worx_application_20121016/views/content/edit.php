<!-- Open Form -->
<form method="post" accept-charset="utf-8" id="content_edit">
	<input type="hidden" name="id" value="<?=variable($id)?>" />
	<input type="hidden" name="position" value="<?=variable($position)?>" />
<!-- Open Sidebar -->
<div id="sidebar">
	<div id="save_changes" class="button save">Save changes</div>
	<div id="delete_entry" class="button-link delete">delete entry</div>
	<?
		if( variable($last_saved) == null )
		{
			$last_saved =  variable($date);
		}
	
	?>
	<span class='last-saved'><?=date("d/m/Y h:i a", strtotime(server_to_user(human_to_unix($last_saved), variable(user('gmt')))))?></span>
	<div class="publishing">
		<?
		// ---------------------------------------------------
		// Status
		echo "<div class='form-element status full button-bar'>
			<div class='merged-buttons'>
				<div class='publish button".( variable($status) != '2' ? ' active' : ' disabled')."' data-value='1'>Publish</div>
	        	<div class='draft button".( variable($status) == '2' ? ' active' : '')."' data-value='2'>Draft</div>
			</div>
			<div class='hidden-elements'>
				<input type='radio' name='status' value='1'".( variable($status) != '2' ? ' checked="checked"' : '')." class='publish' />
				<input type='radio' name='status' value='2'".( variable($status) == '2' ? ' checked="checked"' : '')." class='draft' />
			</div>
		</div>";
		// ---------------------------------------------------
		// Publish on date
		// echo "<div class='form-element publish-date full'>
		// 	<label for='publication_date'>Publish on date</label>
		// 	<input id='publication_date' name='publication_date' class='input date-input' type='text' value='".variable($publication_date)."'>
		// </div>";
		// ---------------------------------------------------
		// Menu item
		echo "<div class='form-element menu-item full'".( (variable($type) == 4 || variable($type) == 3 )  ? ' style="display: none;"' : '').">
			<label for='menu_item'>Create Menu Item</label>
			<input id='menu_item' name='menu_item' class='input' type='text' value='".variable($menu_item)."'>
		</div>";
		// ---------------------------------------------------
		// Social Media
		echo "<div class='social-media'>".
			"<div class='form-element".(variable($twitter) != '' ? ' active' : ' passive')."'>
				<div class='label-button twitter'><label for='twitter'>Post to Twitter</label><span class='close'>&times</span></div>
				<div class='textarea' contenteditable='true' name='twitter' placeholder='Twitter message (110 chars)'>".variable($twitter)."</div>
				<span class='chars-left' data-chars='110'>".(110 - strlen(variable($twitter)))."</span>
			</div>".
			"<div class='form-element".(variable($facebook) != '' ? ' active' : ' passive')."'>
				<div class='label-button facebook'><label for='facebook'>Post to Facebook</label><span class='close'>&times</span></div>
				<div class='textarea' contenteditable='true' name='facebook' placeholder='Facebook message (410 chars)'>".variable($facebook)."</div>
				<span class='chars-left' data-chars='410'>".(410 - strlen(variable($facebook)))."</span>
			</div>
			<div class='form-element".(isset($homepage) != '' ? ' active' : ' passive')."'>
				<div class='label-button homepage'><label for='homepage_title'>Post to Homepage</label><span class='close'>&times</span></div>
				<div class='content'>
					<input type='text' name='homepage_title' placeholder='headline' value=\"".variable($homepage['title'])."\" />
					<div class='textarea' contenteditable='true' name='homepage_text' placeholder='Homepage message'>".variable($homepage['text'])."</div>
				</div>
			</div>
		</div>";
		?>
	</div>
</div>	
<!-- Open Form -->
<div class="entry-container">
	<div id="images">
		<div id="entry_form">
			<?=fs_select($types, variable($type), array('name' => 'type', 'id' => 'type', 'class' => 'dropdown entry-top-info'));?>
			<!-- <div id="type" class="dropdown entry-top-info" data-type="dropdown">
				<span class="selected">Product</span>
				<ul class="selection">
					<li class="option" data-value="1">Product</li>
					<li class="option" data-value="2">Page</li>
					<li class="option" data-value="3">Tips & Tricks</li>
				</ul>
				<select class="hidden-elements" name="type">
					<option value="1">Product</option>
					<option value="2">Page</option>
					<option value="3">Tips & Tricks</option>
				</select>
			</div> -->
			<div class="form-element discreet">	
				<label for="title">Title</label>
				<input type="text" name="title" value="<?=variable($title)?>" placeholder="Title" />
			</div>
			<div class="form-element percent-80 discreet left">	
				<label for="meta_title">SEO-Title</label>
				<input type="text" name="meta_title" value="<?=variable($meta_title)?>" placeholder="Seo-Title" />
			</div>
			<div class="form-element percent-20 discreet right permalink">	
				<label for="permalink">Permalink</label>
				<input type="text" name="permalink" value="<?=variable($permalink)?>" placeholder="permalink" />
			</div>
			<div class="form-element discreet">	
				<label for="text">Text</label>
				<div class="wysiwyg textarea text" placeholder="Text"><?=variable($text)?></div>
			</div>
			<div class="form-element discreet tags percent-50">	
				<label for="tags">Tags</label>
				<textarea name="tags" placeholder="tags"><?=variable($tags)?></textarea>
			</div>
			<div class="form-element discreet description  percent-50">	
				<label for="meta_description">SEO Description</label>
				<textarea name="meta_description" placeholder="Seo description"><?=variable($meta_description);?></textarea>
			</div>
			<!-- blocks -->
			<div class="blocks" <?=( ( variable($type) != 4 && variable($type) != 3 )  ? 'style="display: none;"' : '')?>>
				<?=variable($blocks)?>
				<div class="add-block">add block</div>
			</div>
			<!-- close entry form -->
		</div>	
		<!-- image & media -->
		<div class="media-upload-button">
			<span class="label">Drag files to upload</span>
			<!-- <input id="media_upload_input" type="file" /> -->
		</div>
	<?=variable($display_images);?>
	</div>
<!-- Close entry container -->
</div>
<div id="media">
	<span class="message"><h2>Drop file to Upload</h2><br /><span>click ESC to cancel</span></span>
</div>
<!-- Close Form -->
</form>
<div contenteditable="true" id="clean_paste"></div>