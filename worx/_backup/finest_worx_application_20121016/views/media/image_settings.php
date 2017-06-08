<?
echo "<h2>Edit image: <div contenteditable='true' class='filename autosave' data-id='".$id."' name='filename'>".$filename."</div></h2>";
foreach( config('thumbs') as $name => $thumb)
{
	echo "<div class='image-thumb' data-id='".$id."' data-thumb='".$name."'>
	<div class='image-holder'>
		<div class='loading-holder'>
			<div class='loader'></div>
		</div>
		<form class='replace replace-form' enctype='multipart/form-data'><div class='button'>replace<input name='replace_file' class='upload-input' type='file' /></div></form>
		<img class='thumb' src='".config('display_image_dir').'/'.$filename.'_'.$name.'.'.$ext."?".time()."' alt='Height: ".$thumb['height'].", Width: ".$thumb['width']."'>
	</div>
	<div class='text'>H ".$thumb['height']." &times W ".$thumb['width']."</div><a class='open' target='_blank' href='".config('display_image_dir').'/'.$filename.'_'.$name.'.'.$ext."'></a>
	</div>";
}
//
$hp_images = db_select(config('system/current/db_prefix').config('db_data'), array('type' => 'banner'), array('select' => array('data'), 'unstack' => false, 'index' => 'id', 'single' => true));
$hp_images = $hp_images['data'];
// ----------------
$types = config('homepage_links');
$entry = db_select(config('system/current/db_prefix').config('db_entries'), array('id' => $this->input->post('entry')), array('single' => true));
//
echo '<div class="autosave-home bottom form-element'.(array_key_exists($id, $hp_images) ? ' active':'').'" data-id="'.$id.'" data-link="'.$types[$entry['type']]['name'].$entry['permalink'].'">
	<div class="button add-to-homepage">add to homepage</div>
	<textarea name="caption" placeholder="caption" class="textarea">'.variable($hp_images[$id]['caption']).'</textarea>
</div>';
?>