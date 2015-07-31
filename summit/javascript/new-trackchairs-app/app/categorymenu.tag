<categorymenu>
	<div class="btn-group">
		<button if="{ opts.active }" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			{ opts.active.title } <span class="caret"></span>
		</button>
		<button if="{ !opts.active }" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" if="{ opts.active }">
			All Categories <span class="caret"></span>
		</button>		
		<ul class="dropdown-menu">
			<li><a href="#" onclick="{ allCategories }">All Categories</a></li>
			<li role="separator" class="divider"></li>
			<li each={category in opts.categories}><a href="#" onclick="{ parent.setCategory }">{ category.title }</a></li>
		</ul>
	</div>

	setCategory(e){
		this.parent.setCategory(e.item.category)
	}

	allCategories(){
		this.parent.setCategory()
	}	

</categorymenu>