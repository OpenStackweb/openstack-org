var Sortable = require('sortablejs')
<sortable>
	<ul id="simpleList" class="list-group">
		<li each="{ item, i in opts.items }" class="list-group-item" data-id="{ item.id }" data-order="{ item.order }" >{ item.title }</li>
	</ul>

	<style>
		.sortable-ghost {
			background-color: #E6E6E6;
			color: #E6E6E6!important;
		}
	</style>	

	<script>

		var self = this
		self.api = self.opts.api
		self.items = self.opts.items

		sendUpdatedSort(new_sort) {
			self.api.trigger('save-sort-order', self.parent.listID, new_sort)
		}

		this.on('mount', function(){

			// Simple list		
			var simpleList = document.getElementById('simpleList')
			var sortable = Sortable.create(simpleList,{
				onUpdate: function(evt){
					
					self.sendUpdatedSort(sortable.toArray())
					self.update()

				}
			});
		})

	</script>

</sortable>