var Sortable = require('sortablejs')

<selection-list>


	<div class="col-lg-3">
		<h3>{ opts.listname }</h3>
		<div if={!opts.selections}><i>This person has yet to make any selections.</i></div>
		<ul id="{ opts.listid }" class="list-group">
			<li each="{ item, i in opts.selections }" 
				class="list-group-item { alternate: i >= soltsAvailble }" 
				data-id="{ item.id }" 
				data-order="{ item.order }" >
					{ item.title }
			</li>
		</ul>
	</div>

	<style>
		.sortable-ghost {
			background-color: #E6E6E6;
			color: #E6E6E6!important;
		}

		.alternate {
			background-color: rgba(184, 220, 253, 0.25);
		}

	</style>

	<script>

		var self = this
		var api = self.parent.parent.opts.api

		self.soltsAvailble = 10


		sendUpdatedSort(new_sort) {
			api.trigger('save-sort-order', self.opts.selectionlist, new_sort)
		}

		api.on('selections-ready', function(result){			

			console.log('6a. selection list hears selections-ready.')

			var simpleList = document.getElementById(self.opts.listid)

			var sortable = Sortable.create(simpleList,{
				group: { name: "selection-list-group", pull: "clone", put: true },
				onUpdate: function(evt){
					console.log(evt)
					if (evt.newIndex >= self.soltsAvailble) {
						evt.item.className = "list-group-item alternate"
					} else {
						evt.item.className = "list-group-item"
					}
					self.sendUpdatedSort(sortable.toArray())
					self.update()
				},
				onAdd: function(evt){
					self.sendUpdatedSort(sortable.toArray())
					self.update()
				}
			});

			console.log('6b. selection list updates.')
			self.update()

		})


	</script>


</selection-list>
