<div class="media-item" data-id="<?=$id?>">
	<div class="delete">×</div>
	<div class="edit-box">
		<div class="input name">
			<label for="label">Name</label><input type="text" value="<?=variable($label)?>" placeholder="Name" name="label" class="label" />
		</div>
		<div class="input save-button">
			<div>Save</div>
		</div>
		<div class="hero-label">
			<div>Hero</div>
		</div>
		<div class="input link <?=variable($link_class)?>">
			<label for="link">Link</label><input type="text" value="<?=variable($link)?>" placeholder="http://" name="link" class="link" />
		</div>
		<div class="input instore-button<?=variable($instore_class)?>" <?=variable($btn_hide)?>>
			<div>instore</div>
		</div>
		<div class="input eboutique-button<?=variable($eboutique_class)?>" <?=variable($btn_hide)?>>
			<div>e-boutique</div>
		</div>
		<div class="columns">
			<div class="input media-button column <?=$column_one?>" data-column="column-one"><div>1 column</div></div>
			<div class="input media-button column<?=$column_two?>" data-column="column-two"><div>2 columns</div></div>
		</div>
	</div>
	<div class="image">
		<div class='options'>
			<a href="<?=$cms_full_path?>" class="lightbox-link" rel="lightbox"><span class="expand"></span></a>
			<span class="edit"></span>
		</div>
		<img src="<?=$cms_thumb_150?>" />
	</div>
	<div class="filename"><span class="icon visibility<?=$status?>"></span><span><?=$filename?></span></div>
</div>