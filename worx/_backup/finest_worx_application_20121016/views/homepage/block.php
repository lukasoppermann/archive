<div class="block parent" data-id="<?=$id?>" data-column="<?=variable($column)?>">
	<span class="close delete" data-id="<?=$id?>">&times</span>
	<div class="content">
		<div class="handle"></div>
		<div class="image" <?=(isset($image) && is_array($image)) ? '' : 'style="display:none;"' ?>>
			<img src="<?=(isset($image) && is_array($image) ? config('display_image_dir').'/'.$image['filename'].'_thumb_280.'.$image['ext'] : '')?>" alt="<?=variable($image['filename'])?>">
		</div>
		<div><input class="headline autosave" type="text" value="<?=variable($title)?>" name="title" placeholder="headline" /></div>
		<form class='upload-form' enctype='multipart/form-data'>
			<a title="<?=(!isset($image['filename']) ? 'add image' : 'delete image')?>" class="edit-image<?=(!isset($image['filename']) ? ' add-image' : ' delete-image')?>">
				<input name='new_file' class='upload-input' type='file' />
			</a>
		</form>
		<div class="text autosave-edit" contenteditable="true" name="text"><?=variable($text)?></div>
		<input class="link autosave" type="text" value="<?=variable($permalink)?>" name="permalink" placeholder="link"/>
	</div>
	<div class="add-block" data-id="<?=$id?>"><span class="add">add block</span></div>
</div>