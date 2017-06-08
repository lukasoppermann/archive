<?
echo form_open('/content', array('id' => 'entry_form', 'class'=>'form'));
// ---------------------------------------------------
// prepare data
$last_saved = variable($entry['data']['last_saved']);
if(substr($last_saved,0,10) == date('d/m/Y'))
{
	$last_saved = substr($last_saved,12);
}
//
if(!isset($entry['status']) || $entry['status'] == 3)
{
	$entry['status'] = 2;
}
// news or product true ?
$creatable = false;
if($entry['type'] == "1" || $entry['type'] == "2")
{
	$creatable = true;
}
// ---------------------------------------------------
// sidebar
echo "<div id='sidebar'>".
	"<input type='hidden' value='".$entry['id']."' name='id' id='id' />".
	// ---------------------------------------------------
	// Save Button
	"<div class='save full'><div class='button save' id='save'>save changes</div>";
	// only display if news or product
	if($creatable == true)
	{
		echo "<span class='delete-entry'>delete entry</span>".
			 "<span class='last-saved'>".$last_saved."</span>";
	}
	echo "</div>";

	// only display if news or product
	if($creatable == true)
	{
		// ---------------------------------------------------
		// Status
		echo "<div class='status full'>
			<label for='status'>Status</label>
			<div class='btn btn-left".($entry['status'] == '1' ? ' active' : '')."' data-value='publish'>Publish</div>
	        <div class='btn btn-right".($entry['status'] == '2' ? ' active' : '')."' data-value='draft'>Draft</div>
			<div class='hidden-elements'>
				<input type='radio' name='status' value='1'".($entry['status'] == '1' ? ' checked="checked"' : '')." class='publish' />
				<input type='radio' name='status' value='2'".($entry['status'] == '2' ? ' checked="checked"' : '')." class='draft' />
			</div>
		</div>";
		// ---------------------------------------------------
		// Status
		echo "<div class='publish-date full'>
			<label for='status'>Publish on date</label>
			<input id='datepicker' name='datepicker' class='input' type='text' value='".variable($entry['data']['publication_date'])."' ".($entry['status'] == '2' ? ' disabled="disabled"' : '').">
		</div>";
		// ---------------------------------------------------
		// Type
		echo "<div class='type full'>
			<label for='type'>Article type</label>
			<select id='type' name='type'>
				<option value='1' ".($entry['type'] == '1' ? ' selected="selected"' : '').">News item</option>
				<option value='2' ".($entry['type'] == '2' ? ' selected="selected"' : '').">Product Page</option>
			</select>
		</div>";
		// ---------------------------------------------------
		// Product Category
			echo "<div class='designer full product'".($entry['type'] != '2' ? ' style="display:none;"' : '').">
				<label for='designer'>Product Designer</label>
				".$designer."
				<div id='add_designer'><div class='add-button'>+</div>
				<div class='edit-window' data-type='designer'>
					".$designer_edit."
					<input name='type_label' class='label' type='text' value='Label' />
					".$designer_position."
					<button class='delete'>delete</button><button class='save'>save</button>
				</div>
				</div>
			</div>";
		// ---------------------------------------------------
		// Product Type
			echo "<div class='product-type full product'".($entry['type'] != '2' ? ' style="display:none;"' : '').">
				<label for='product_type'>Product Type</label>
				".$product_types."<div id='add_product_type'><div class='add-button'>+</div>
				<div class='edit-window' data-type='product-type'>
					".$product_types_edit."
					<input name='type_label' class='label' type='text' value='Label' />
					<input name='product_default_sizes' class='sizes' type='text' value='Default sizes separated by \",\"' />
					".$product_type_position."
					<button class='delete'>delete</button><button class='save'>save</button>
				</div>
				</div>
			</div>";
		// ---------------------------------------------------
		// Sales
			echo "<div class='sales full product'".($entry['type'] != '2' ? ' style="display:none;"' : '').">
				<label for='sales_start'>Start Sale</label>
					<input id='sales_start' name='sales_start' class='input' type='text' value='".variable($entry['data']['sales_start'])."' ".($entry['status'] == '2' ? ' disabled="disabled"' : '').">
				<label for='sales_end'>End Sale</label>
				<input id='sales_end' name='sales_end' class='input' type='text' value='".variable($entry['data']['sales_end'])."' ".($entry['status'] == '2' ? ' disabled="disabled"' : '').">
				<div id='sales_price_box'>
				<label for='sales_prices'>Sale Price</label>
				<span class='dollar'>$</span>
				<input type='text' name='sales_price' id='sales_price' class='input' value='".variable($entry['data']['sales_price'])."' />
				</div>
			</div>";
		// ---------------------------------------------------
		// Social Media
		echo "<div class='social-media full'>";
		// ---------------------------------------------
		// Post as News
		echo "<div id='post_news' class='social-post product' ".($entry['type'] != '2' ? ' style="display:none;"' : '').">
				<label for='area'>Post as News Item</label>	
				<div class='check-button news'>
					<span class='check".(variable($entry['data']['news']) == 'news' ? ' checked' : ' crossed')."'>
						<span class='icon checkmark'></span>
						<span class='icon cross'></span>
					</span>
					<span class='label'>News</span>
					<div class='hidden-elements'>
					<input type='checkbox' name='news' value='news' checked='".(variable($entry['data']['news']) == 'news' ? 'checked' : ' ')."' />									
					</div>
				</div>
			</div>";
		// ---------------------------------------------
		// Facebook		
		echo "<div id='fb_post' class='social-post'>
		<label for='area'>Post on Facebook</label>";	
		if ( !isset($this->cauth['fb_token']) || !$this->cauth['fb_token'] )
		{
			echo "<span class='social-error'>Not connected to Facebook</span>";
		}
		else
		{
			echo "<div class='check-button facebook'>
				<span class='check".(variable($entry['data']['facebook']) == 'facebook' ? ' checked' : ' crossed')."'>
					<span class='icon checkmark'></span>
					<span class='icon cross'></span>
				</span>
				<span class='label'>Facebook</span>
				<div class='hidden-elements'><input type='checkbox' name='facebook' value='facebook' ".(variable($entry['data']['facebook']) == 'facebook' ? 'checked=\'checked\'' : '')." /></div>
			</div>";	
		}
		echo "</div>";

		// ---------------------------------------------
		// twitter
		// Post twitter box
		echo "<div id='tw_post' class='social-post'>
			<label for='area'>Post on Twitter</label>";
		// try to login
		if ( !$this->tweet->logged_in() )
		{
			echo "<span class='social-error'>Not connected to Twitter</span>";
		}
		else
		{
		echo "<div class='check-button twitter'>
			<span class='check".(variable($entry['data']['twitter']) == 'twitter' ? ' checked' : ' crossed')."'>
				<span class='icon checkmark'></span>
				<span class='icon cross'></span>
			</span>
			<span class='label'>Twitter</span>
			<div class='hidden-elements'><input type='checkbox' name='twitter' value='twitter' ".(variable($entry['data']['twitter']) == 'twitter' ? 'checked=\'checked\'' : '')." /></div>
		</div>";
		}
	echo "</div></div>";
		// -------
		// END of Social Media
	}
echo "</div>";
// ---------------------------------------------------
// form
echo "<div class='form-container'>";
// Title
echo "<div id='title_container'>";
echo form_input(array(	'name'  		=> 'title',
  						'id'    		=> 'title',
  						'value' 		=> set_value('title',variable($entry['title'])),
						'placeholder' 	=> 'Headline',
						'class' 		=> 'input-hidden title'
						));
echo "</div>";
// Meta Title
echo "<div id='meta_title_container' ".($entry['type'] == '1' ? ' style="display:none;"' : '').">";
echo form_input(array(	'name'  		=> 'meta_title',
  						'id'    		=> 'meta_title',
  						'value' 		=> set_value('meta_title',variable($entry['meta_title'])),
						'placeholder' 	=> 'SEO page title',
						'class' 		=> 'input-hidden meta-title'
						));
echo "</div>";
// Text		
echo "<div id='text_container'>";			
echo form_textarea(array('name'  		=> 'text',
  						'id'    		=> 'text',
  						'value' 		=> set_value('text',variable($entry['text'])),
						'placeholder' 	=> 'Text',
						'class' 		=> 'input-hidden text wysiwyg'));	
	
echo "</div>";

// ---------------------------------------------------
// Product Fields
if($creatable == true)
{
	// price
	echo "<div class='one-column left product'".($entry['type'] != '2' ? ' style="display:none;"' : '').">
		<div class='price'><label for='price'>Price</label>";
	echo "$ ".form_input(array(	'name'  	=> 'price',
		  					'id'    		=> 'price',
		  					'value' 		=> set_value('price',variable($entry['data']['price'])),
							'placeholder' 	=> '0000',
							'class' 		=> 'input-underline price'
							));		
	// ----------------------------------------------
	// product code
	echo "</div><div class='product_code'><label for='product_code'>Product Code</label>";
	echo form_input(array(	'name'  		=> 'product_code',
		  					'id'    		=> 'product_code',
		  					'value' 		=> set_value('product_code',variable($entry['data']['product_code'])),
							'placeholder' 	=> 'product code',
							'class' 		=> 'input product_code'
							));		
	// ----------------------------------------------
	// product sizes
	$sizes = variable($entry['data']['sizes']);
	($sizes != null)? $sizes = implode(', ',$sizes) : '';
	echo "</div><div class='product_sizes'><label for='product_sizes'>Product Sizes (separated by comma)</label>";
	echo form_input(array(	'name'  		=> 'product_sizes',
		  					'id'    		=> 'product_sizes',
		  					'value' 		=> set_value('product_sizes',$sizes),
							'placeholder' 	=> 'product sizes',
							'class' 		=> 'input product_sizes'
							));		
	echo "</div></div>";
	// ----------------------------------------------
	// stores
	echo "<div class='one-column product'".($entry['type'] != '2' ? ' style="display:none;"' : '').">
		<label for='stores'>Stores</label>";
	if( isset($entry['data']['store']) && is_array($entry['data']['store']) )
	{
		$instore 	= 	in_array('instore', $entry['data']['store']) ? 'instore' : '';
		$eboutique 	= 	in_array('eboutique', $entry['data']['store']) ? 'eboutique' : '';	
	}
	echo "<div class='checkbox-group'><label class='label-left' for='store_instore'>instore</label>".
	form_checkbox(array('name'  		=> 'store[]',
						'id' 			=> 'store_instore',
	  					'value' 		=> 'instore',
						'checked' 		=> boolean($instore),
						'class' 		=> 'checkbox'
					)).'</div>';
	echo "<div class='checkbox-group'><label class='label-left' for='store_eboutique'>e-boutique</label>".
	form_checkbox(array('name'  		=> 'store[]',
						'id' 			=> 'store_eboutique',
						'value' 		=> 'eboutique',
						'checked' 		=> boolean($eboutique),
						'class' 		=> 'checkbox'
					)).'</div>';
	// ----------------------------------------------
	// product stock
	$stock = variable($entry['data']['product_stock'], '0');
	echo "<div class='product_stock'><label for='product_stock'>Product Stock</label>";
	echo form_input(array(	'name'  		=> 'product_stock',
		  					'id'    		=> 'product_stock',
		  					'value' 		=> set_value('product_stock',$stock),
							'class' 		=> 'input product_stock'
							));		
	echo "</div></div>";
}
// ---------------------------------------------------	
// Media start
if(!isset($entry['data']['no_media']) || $entry['data']['no_media'] != true)
{
	echo '<div id="edit_media_switch" class="media-switch tab-container">
	    <div class="nav">
	        <div class="btn btn-left btn-right active" data-tab="slideshow">Slideshow</div>';
	        // <div class="btn btn-right" data-tab="video">Video</div>
	echo '</div>
	    <div class="tabs">                    
			<div class="slideshow active">
			    <ul class="upload-list">
				    <div class="upload-container">'.
					form_input(array('name'  		=> 'filename',
			  						'id'    		=> 'filename',
			  						'value' 		=> set_value('filename',variable($filename)),
									'placeholder' 	=> 'insert a filename',
									'class' 		=> 'input filename'
									)).
	                    '<div class="file-uploader" id="file_uploader" data-dir="'.config('client_root', true).config('client_media', true).config('client_images', true).'">
	                        <noscript>          
	                            <p>Please enable JavaScript to use file uploader.</p>
	                        </noscript>
	                    </div>
	                </div>';
				if(isset($images) && count($images) > 0)
				{
					foreach($images as $key => $img)
					{
						echo '<li class="upload_thumb'.($img['key'] == 'hero' ? ' hero' : '').'">
								<span class="delete" data-img_id="'.$key.'">X</span>
								<div class="thumb">
									<img src="'.config('client_base', true).config('client_media', true).config('client_images', true).$img['data']['thumb_150'].'" alt="'.$img['data']['label'].'">
								</div>
								<span class="filename">'.$img['data']['filename'].'</span>
							</li>';
					}
				}
	        echo '</ul>
			</div>';
			// <div class="video">
			// 	<input type="file" name="image_upload" id="image_upload" />
			// </div>
	echo '</div>
	</div>';
}
// only display if news or product
if($creatable == true)
{
	// Social Media Start
	echo '<div id="social_media" class="tab-container">
			<div class="nav">
		    	<div class="btn btn-left product active" data-tab="news"'.($entry['type'] != '2' ? ' style="display:none;"' : '').'>News</div>
		    	<div id="fb_text_button" class="btn'.($entry['type'] != '2' ? ' active btn-left' : '').'"" data-tab="facebook">Facebook</div>
		    	<div class="btn btn-right" data-tab="twitter">Twitter</div>
			</div>
			<div class="tabs">';
			// --------------------------------
			// 	News text tab
			echo '<div class="tab news product active"'.($entry['type'] != '2' ? ' style="display:none;"' : '').'><p class="chars-left">You have <span class="fb-chars"></span> characters left.</p>
				<textarea placeholder="News text" name="news_update" id="news_update" length="600" class="limit-chars" data-chars_left="fb-chars">'.set_value('news_update',variable($entry['data']['news_update'])).'</textarea>
			</div>';
			// --------------------------------
			// 	Facebook text tab
			echo '<div class="tab facebook'.($entry['type'] != '2' ? ' active"' : '').'"><p class="chars-left">You have <span class="fb-chars"></span> characters left.</p>
					<textarea placeholder="Status update" name="fb_update" id="fb_update" length="389" class="limit-chars" data-chars_left="fb-chars">'.set_value('fb_update',substr(variable($entry['data']['fb_update']),0,strrpos(variable($entry['data']['fb_update']), ' http'))).'</textarea>
				</div>';
			// --------------------------------
			// 	Twitter text tab
			echo '<div class="tab twitter"><p class="chars-left">You have <span class="tw-chars"></span> characters left.</p>
					<textarea placeholder="Twitter update" name="tweet" id="tweet" length="109" class="limit-chars" data-chars_left="tw-chars">'.set_value('tweet',substr(variable($entry['data']['tweet']),0,strrpos(variable($entry['data']['tweet']), ' http'))).'</textarea>
				</div>';
		// END TABS
		echo '</div>
		</div>';
}
echo form_close();
?>