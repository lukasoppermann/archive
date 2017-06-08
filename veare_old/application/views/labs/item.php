<div id="lab_item">
	<div id="demo" class="slider">
		<div class="slider-content">
			<div class="slide demo active">
				Test
			</div>
			<div class="slide image">
				Test 2
			</div>
		</div>
		<ul class="tabs">
			<li class="active" data-slide="demo">show live demo</li>
			<li class="passive" data-slide="image">show image</li>
		</ul>
	</div>
	<h1 class="main-headline"><?=variable($title)?></h1>
	<div class="excerpt"><?=variable($excerpt)?></div>
	<div class="content"><?=variable($text)?></div>
	<div class="tags"><?=show_tags($tags)?></div>
</div>