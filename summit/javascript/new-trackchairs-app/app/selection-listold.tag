var Sortable = require('sortablejs')

<selection-list>

	<div class="col-lg-3">
		<categorymenu categories="{ summit.track_chair.categories }" active="{ activeCategory }" />
		<h3>My Selection List</h3>
		<div show="{ !(selections) }">Loading...</div>
		<ul id="simpleList" class="list-group" show="{ selections }">
			<li each="{ item, i in selections }" class="list-group-item" data-id="{ item.id }" data-order="{ item.order }" >{ item.title }</li>
		</ul>
	</div>

	<style>
		.sortable-ghost {
			background-color: #E6E6E6;
			color: #E6E6E6!important;
		}
	</style>

	<script>

		var self = this
		self.listID = false

		sendUpdatedSort(new_sort) {
			self.opts.api.trigger('save-sort-order', self.listID, new_sort)
		}

		opts.api.once('selections-loaded', function(result){

			var simpleList = document.getElementById('simpleList')
			var sortable = Sortable.create(simpleList,{
				onUpdate: function(evt){
					self.sendUpdatedSort(sortable.toArray())
					self.update()

				}
			});
		})	

		opts.api.on('summit-details-loaded', function(result){
			self.summit = result
			self.activeCategory = self.summit.track_chair.categories[0]
			opts.api.trigger('load-selections',self.activeCategory.id)
			self.update()
		})

		setCategory(category) {
			self.activeCategory = category
			var id
			if(category) id = category.id
			opts.api.trigger('load-selections',category.id)
		}

		opts.api.on('selections-loaded', function(result){
			var selections = result.results
			self.selections = selections
			self.listID = result.list_id
			self.update()
		})

	</script>


</selection-list>
