<a class="column card-flip ajax-link <?=variable($card_class)?>" data-tag="<?=implode(' ',$tags)?>" href="<?=base_url().'en/portfolio/'.$permalink?>">
	<div class="card-wrap">
		<div class="card front">
				<div class="card-content">
					<div class="card-image">
						<img src="<?=base_url()?>images/<?=variable($images['filename']).'.'.variable($images['ext'])?>" alt="<?=$title?>" />
					</div>
					<h3 class="card-headline open_sans_bold"><?=$title?></h3>
					<p class="card-text open_sans"><?=text_limiter(variable($excerpt), 100, true, '')?></p>
				</div>
		</div>
		<div class="card back">
				<div class="card-back-title open_sans_bold">Open<br /> Case</div>
				<div class="card-back-content">
					<h3 class="card-headline open_sans_bold"><?=$title?></h3>
				</div>
				<div class="card-arrow"></div>
		</div>
	</div>
</a>