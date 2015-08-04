<selectionmenu>
	<div class="btn-group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" if="{ opts.active }">
			Switch Category <span class="caret"></span>
		</button>		
		<ul class="dropdown-menu">
			<li each={category in opts.categories}><a href="#" onclick="{ parent.setCategory }">{ category.title }</a></li>
		</ul>
	</div>

	setCategory(e){
		this.parent.setCategory(e.item.category)
	}

	allCategories(){
		this.parent.setCategory()
	}	

</selectionmenu>