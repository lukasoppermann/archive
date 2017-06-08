<div class="page-content">
	<div id="sidebar" class="filter-list">
		<div class="group">
			<ul data-filter="status" class="filters status">
				<lh>Statuses <div><span class="filter all">all</span>/<span class="filter none">none</span></div></lh>
				<li class="filter" data-value='1'>Published</li>
				<li class="filter" data-value='2'>Draft</li>
				<li class="filter" id="deleted" data-value='3'>Deleted</li>
			</ul>
		</div>
		<!-- by designer -->
		<div class="group">
			<ul data-filter="designer" class="filters type">
				<lh>Designer <div><span class="filter all">all</span>/<span class="filter none">none</span></div></lh>
				<?=$designers?>
			</ul>
		</div>
		<!-- by product type -->
		<div class="group">
			<ul data-filter="product" class="filters type">
				<lh>Product Type <div><span class="filter all">all</span>/<span class="filter none">none</span></div></lh>
				<?=$product_type?>
			</ul>
		</div>
	</div>
	<?=$content?>
</div>