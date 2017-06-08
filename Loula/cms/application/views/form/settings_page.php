<?
echo form_open('/settings/page', array('id' => 'settings_page', 'class'=>'form'));
echo form_hidden(array('page' => 'page'));
// ---------------------------------------------------
// sidebar
echo "<div id='sidebar'>".
	// ---------------------------------------------------
	// Save Button
	"<div class='save full'><div class='button save' id='save'>save changes</div></div>".
	// -------
	// END
	"</div>";
// ---------------------------------------------------
// form
echo "<div class='form-container'>";
echo "<h1>".variable($title)."</h1>";
// Title
echo "<div id='title_container'>";
echo "<label for='page_name' class='content-label'>Name of your website</label>";
echo form_input(array(	'name'  		=> 'page_name',
  						'id'    		=> 'page_name',
  						'value' 		=> set_value('page_name',variable($page_name)),
						'placeholder' 	=> 'Name of your website',
						'class' 		=> 'input-hidden title'
						));
echo "</div>";
// Analytics
// echo "<div id='analytics_container'>";
// echo "<label for='page_name' class='content-label'>Your Google Analytics Key</label>";
// echo form_input(array(	'name'  		=> 'analytics',
//   						'id'    		=> 'analytics',
//   						'value' 		=> set_value('analytics',variable($analytics)),
// 						'placeholder' 	=> 'Your Google Analytics Key',
// 						'class' 		=> 'input-hidden title'
// 						));
// echo "</div>";						
// Social Media
echo "<div id='settings_social_media'>";
	// Twitter
	echo "<div id='twitter_block'><a href='".base_url(false).$twitter_url."'>
			<div id='twitter_connect' data-connect='Connect to Twitter' data-disconnect='Disconnect from Twitter' class='button".$twitter."'>
			Connect to Twitter</div>
		</a></div>";
	// Facebook
	echo "<div id='facebook_connect'>".$fb_button."</div>";
echo "</div>";
//
echo "</div>";
echo form_close();
?>