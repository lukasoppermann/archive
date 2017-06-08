<div class="preview done" data-id="<?=$id?>">
	<span class="close">×</span>
	<div class="imageHolder">
		<img src="<?=config('display_image_dir').'/'.$file?>" />
		<span class="info">i</span>
		<span class="uploaded"></span>
	</div>
	<div class="social-channels">
		<span class="channel all">all</span>
		<span class="channel news<?=(variable($social_images['news']) == $id ? ' active' : '')?>" data-type="news" title="news"></span>
		<span class="channel twitter<?=(variable($social_images['twitter']) == $id ? ' active' : '')?>" data-type="twitter" title="twitter"></span>
		<span class="channel facebook<?=(variable($social_images['facebook']) == $id ? ' active' : '')?>" data-type="facebook" title="facebook"></span>
	</div>
</div>