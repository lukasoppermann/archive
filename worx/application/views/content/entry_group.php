<div class="group group-<?=$group_id?>" data-group="<?=$group_id?>">
	<h3 class="group-header"><?=$group?></h3><div class="group-options">
	<a class="new-entry" href="<?=base_url().'content/edit/'.$group_type?>">create content</a>
	<a class="trash" href="<?=base_url().'content/trash/'.$group_type?>" style="display:none;">trash deleted content</a>	|
	<a class="deleted-entries" href="#" data-text="show active content">show deleted content</a></div>
	<ul class="entry-list">
		<?=variable($list)?>
	</ul>
	<ul class="deleted entry-list">
		<?=variable($deleted)?>
	</ul>
</div>