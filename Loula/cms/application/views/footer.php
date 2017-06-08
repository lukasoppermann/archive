	<!-- close content div -->
	</div>
<script type="text/javascript" charset="utf-8">
	var CI_ROOT = '<?=base_url()?>';
	var CLIENT_BASE = '<?=config("client_base", true)?>';
	var CLIENT_IMAGES = '<?=config("client_base", true).config("client_media", true).config("client_images", true)?>';	
</script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.min-1.7.1.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.ui.core.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.ui.position.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.ui.datepicker.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/wysiwyg-0.0.1.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.fileuploader.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.serializer.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.limit-1.2.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.fs_filter-0.0.1.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/media-0.0.1.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.sortable.js"></script>
<?
// entries
if($current == 'content')
{
	echo '<script type="text/javascript" src="'.base_url().'libs/js/entry-0.0.1.js"></script>'."\n";
}
// settings
elseif($current == 'settings')
{
	echo '<script type="text/javascript" src="'.base_url().'libs/js/settings-0.0.1.js"></script>'."\n";	
}
// multimedia
elseif($current == 'multimedia')
{
	echo '<script type="text/javascript" src="'.base_url().'libs/js/jquery.slimbox.min.js"></script>'."\n";	
	echo '<script type="text/javascript" src="'.base_url().'libs/js/multimedia-0.0.1.js"></script>'."\n";	
}
?>
<script type="text/javascript" src="<?=base_url()?>libs/js/javascript.js"></script>
<div id="fb-root"></div>
<script>               
  window.fbAsyncInit = function() {
    FB.init({
      appId: '<?=$this->config->item('facebook_api_key')?>', 
      cookie: true, 
      xfbml: true,
      oauth: true
    });
    FB.Event.subscribe('auth.login', function(response) {
      window.location.reload();
    });
    FB.Event.subscribe('auth.logout', function(response) {
      window.location.reload();
    });
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
</script>
<?=fs_debug_print_js(); ?>
</body>
</html>