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
		<!-- by page type -->
		<div class="group">
			<ul data-filter="type" class="filters type">
				<lh>Types <div><span class="filter all">all</span>/<span class="filter none">none</span></div></lh>
				<li class="filter" data-value='1'>News</li>
				<li class="filter" data-value='2'>Product Page</li>
				<li class="filter" data-value='3'>Content Page</li>
			</ul>
		</div>
	</div>
	<?=$content?>
	<div id="remove_articles"><h3>Remove delete articles forever</h3>
		<p>This action cannot be reversed.</p>
		<div id="delete_button">
			<div class="button" id="delete">Remove articles</div>
		</div>
	</div>
</div>