var Sortable = require('sortablejs')

<selection-list>


	<div class="col-lg-3">
		<h3>{ opts.listname }</h3>
		<div if={!opts.selections}><i>This person has yet to make any selections.</i></div>
		<ul id="{ opts.listid }" class="list-group {empty: !opts.selections}">
			<li each="{ item, i in opts.selections }" 
				class="list-group-item { alternate: i >= soltsAvailble } animated" 
				data-id="{ item.id }" 
				data-order="{ item.order }" 
				>
					<div class="selection-front" onclick="{ flip }">{ item.title }</div>
					<div class="selection-back" onclick="{ flip }">Other Side</div>
			</li>
		</ul>
	</div>

	<style>

		.list-group-item {
			overflow: hidden;
			cursor: arrow;
		}

		.sortable-ghost, .sortable-ghost .selection-front, .sortable-ghost .selection-back {
			background-color: #E6E6E6;
			color: #E6E6E6!important;
		}

		.alternate {
			background-color: rgba(184, 220, 253, 0.25);
		}

		.list-group.empty {
			min-height: 30px;
			border: 1px dotted grey;
		}

		.selection-front, .selection-back {
			display: block;
			padding: 5px;
			min-height: 3em;
		}

		.selection-front {
			width: 100%;
		}

		.selection-back {
			background-color: #D5D5D5;
			width: 6em;
			position: absolute;
			top: 0px;
			right: -6em;
			bottom: 0px;
		}

		.selection-back.slide {
		    right: -6em;
		    -webkit-animation: slide 0.2s forwards;
		    animation: slide 0.2s forwards;
		}

		@-webkit-keyframes slide {
		    100% { right: 0; }
		}

		@keyframes slide {
		    100% { right: 0; }
		}


	</style>

	<script>

		var self = this
		var api = self.parent.parent.opts.api

		self.soltsAvailble = 10


		sendUpdatedSort(new_sort) {
			api.trigger('save-sort-order', self.opts.selectionlist, new_sort)
		}

		flip(e) {
			console.log(e)
			e.target.nextElementSibling.classList.toggle('slide')
			self.update()
		}

		api.on('selections-ready', function(result){			

			console.log('6a. selection list hears selections-ready.')

			var simpleList = document.getElementById(self.opts.listid)

			if(simpleList) {
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
			}

			console.log('6b. selection list updates.')
			self.update()

		})


	</script>


</selection-list>
