<? 	if( !isset($type) ){ $type = 2; }
		if( !isset($product_type) || $product_type == null ){ $product_type = '1'; }
		foreach(config('product_type') as $key => $p_type)
		{
			$product_types[$key] = $p_type['label'];
		}
 ?>
<!-- Open Form -->
<form method="post" accept-charset="utf-8" id="content_edit">
	<div class="system-box">
		<div class="system">
			<div class="boats">
				<?if(isset($boats)){
					echo "<div class='form-element product-type'>
							<label for='boat'>Select Boat</label>
							".fs_select($boats, variable($boat), array('name' => 'boat', 'id' => 'boat', 'class' => 'dropdown select'))."
						</div>";
				}?>
				<div class="button float-right close-systems">
					close
				</div>
			</div>
			<div class="modules">
				<?=variable($active_modules)?>
			</div>
		</div>
		<div class="system-bg"></div>
	</div>
	<div class="choose-modules">
		<div class="header">
			<h4>Select a module</h4>
			<div class="button float-right close-modules">
				close
			</div>
		</div>
		<?=variable($choose_module)?>
	</div>
	<input type="hidden" name="id" value="<?=variable($id)?>" />
	<input type="hidden" name="position" value="<?=variable($position)?>" />
	<input type="hidden" name="link" value="<?=variable($link)?>" />
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
		echo "<div class='form-element menu-item full'".( (variable($type) == 2 || variable($type) == 4 || variable($type) == 7 || variable($type) == 3 )  ? ' style="display: none;"' : '').">
			<label for='menu_item'>Create Menu Item</label>
			<input id='menu_item' name='menu_item' class='input' type='text' value='".variable($menu_item)."'>
		</div>";
		// ---------------------------------------------------
		// Product code
		echo "<div class='form-element product-code full'".( (variable($type) == 2 )  ? '' : ' style="display: none;"').">
			<label for='product_code'>Product code</label>
			<input id='product_code' name='product_code' class='input' type='text' value='".variable($product_code)."'>
		</div>";
		// Product
		echo "<div class='form-element price full'".( (variable($type) == 2 )  ? '' : ' style="display: none;"').">
			<label for='price'>Price</label>
			<input id='price' name='price' class='input' type='text' value='".variable($price)."'>
		</div>";
		// type 
		echo "<div class='form-element product-type full'".( (variable($type) == 2 )  ? '' : ' style="display: none;"').">
			<label for='product_type'>Type</label>
			".fs_select($product_types, variable($product_type), array('name' => 'product_type', 'id' => 'product_type', 'class' => 'dropdown select'))."
		</div>";
		// Slots for boats
		echo "<div class='form-element slots full double'".( (variable($type) == 2 && ( variable($product_type) == '1' ) )  ? '' : ' style="display: none;"').">
			<div class='full-div'><label for='slots'>Slots per row</label><label for='rows'>  / Rows</label></div>
			".fs_select(array(0 => 'slots',1,2,3,4,5,6,7,8), variable($slots), array('name' => 'slots', 'id' => 'slots', 'class' => 'dropdown select'))."
			".fs_select(array(0 => 'rows',1,2,3,4,5,6,7,8), variable($rows), array('name' => 'rows', 'id' => 'rows', 'class' => 'dropdown select'))."
		</div>";
		// Slots for modules
		echo "<div class='form-element module-slots full'".( (variable($type) == 2 && ( variable($product_type) == 2) )  ? '' : ' style="display: none;"').">
			<div class='full-div'><label for='slots'>Slots</label></div>
			".fs_select(array(0 => 'slots',1,2,3,4,5,6,7,8), variable($slots), array('name' => 'module_slots', 'id' => 'module_slots', 'class' => 'dropdown select'))."
		</div>";
		// Modules image
		echo "<div class='form-element module-image full double'".( (variable($type) == 2 && ( variable($product_type) == 2) )  ? '' : ' style="display: none;"').">
			<a id='module_image' title='Images need to be uploaded in the right size depending on number of slots. See dashboard for specs.' class='button padded'>
				<input type='file' id='module_image_upload' />
				<span class='upload-text' data-txt='Replace module image'>".(isset($module_image) ? 'Replace' : 'Upload')." module image</span>
			</a>
			".( isset($module_image) ? "<a target='_blank' class='current-module-image' href='".config('display_image_dir/dir')."/".$module_image."'>current module image</a>" : '')."
			<a target='_blank' href='".base_url()."dashboard' title='Images need to be uploaded in the right size depending on number of slots. See dashboard for specs.' class='important'>Important (hover)!</a>
		</div>";
		// Modules
		echo "<div class='form-element modules".(variable($count_boats) < variable($count_modules) ? " warning'" : "'").( (variable($type) == 2 && variable($product_type) == 3 )  ? '' : ' style="display: none;"').">
				<div class='label-button'><label for='modules'>Add modules</label><span class='mod-count float-right'>(<span class='module-count'>".
		variable($count_modules).'</span>/<span class="boat-count">'.variable($count_boats)."</span>)</span></div>
		</div>";
		// devider
		echo "<div class='divider'></div>";
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
			<div class="form-element discreet" contentEditable="false">	
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
			<div class="form-column percent-50">
				<div class="form-element discreet tags">	
					<label for="tags">Tags</label>
					<textarea name="tags" placeholder="tags"><?=variable($tags)?></textarea>
				</div>
				<div class="form-element discreet description">	
					<label for="meta_description">Description</label>
					<textarea name="meta_description" placeholder="Seo description"><?=variable($meta_description);?></textarea>
				</div>
			</div>
			<div class="form-column percent-50">
				<div class="form-element discreet preview-text" <?=( ( variable($type) != 2 )  ? 'style="display: none;"' : '')?>>	
					<label for="preview_text">Preview text</label>
					<textarea name="preview_text" placeholder="Preview text"><?=variable($preview_text);?></textarea>
				</div>
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