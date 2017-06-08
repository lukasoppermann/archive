<div class="news-item">
	<? if(variable($single) == 'true'){ ?>
		<a class="back-link" href="<?=lang_url(TRUE).'news'?>"><span class="arrow-left"></span><?=lang('back_news')?></a>
		<h1 class="headline"><?=$title?></h1>	
	<? } else { ?>
		<a class="teaser" href="<?=lang_url(TRUE).'news/'.$id?>">
			<h4 class="headline"><?=$title?></h4>
		</a>
	<? } ?>
	<? if(variable($single) != 'true'){ ?>
		<?=trim_text($text, 400)?>
		<p class="referral">
			<a class="link" href="<?=lang_url(TRUE).'news/'.$id?>"><?=lang('read_more')?></a>
		</p>
	<? }else{ ?>
		<?=$text?>
	<? } ?>
</div>