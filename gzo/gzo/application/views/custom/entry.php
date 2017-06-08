<div class="entry-list">
	<?=($long_text != "" ? "<a class='invisible-link' href='".lang_url().'news/'.$id."'>" : "")?>
	<h3><?=$title?></h3>
	<?=$text?>
	<?=($long_text != "" ? "<a href='".lang_url().'news/'.$id."' class='readmore'>mehr lesen</a></a>" : "")?>
</div>