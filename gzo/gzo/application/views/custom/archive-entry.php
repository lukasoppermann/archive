<div class="entry-list">
	<?=($long_text != "" ? "<a class='invisible-link' href='".lang_url().'news/'.$id."'>" : "")?>
	<h3><?=$title?></h3>
	<?=$text?>
	<?=($long_text != "" ? "<a class='link' href='".lang_url().'news/'.$id."'>mehr lesen</a></a>" : "")?>
</div>