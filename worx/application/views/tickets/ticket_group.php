<div class="group group-<?=$group_id?>" data-group="<?=$group_id?>">
	<h3 class="group-header"><?=$group?></h3><div class="group-options">
	<a class="new-entry" href="<?=base_url().'ticket/new/'.$group_id ?>">new ticket</a>
	<!-- <a class="trash" href="<?=base_url().'content/trash/' ?>" style="display:none;">trash deleted content</a>	| -->
	<a class="deleted-entries" href="#" data-text="show active content">show hidden</a>
	</div>
	<ul class="entry-list new-items">
		<?=variable($items['default'])?>
	</ul>
	<ul class="deleted-list entry-list hidden">
		<?=variable($items['deleted'])?>
	</ul>
</div>