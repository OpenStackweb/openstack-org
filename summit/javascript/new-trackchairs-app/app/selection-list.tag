var Sortable = require('sortablejs')

<selection-list>


	<div class="col-lg-3">
		<h3>{ opts.listname }</h3>
		<div if={!opts.selections && opts.listtype != 'Group'}><i>This person has not made any selections yet.</i></div>
		<div if={!opts.selections && opts.listtype == 'Group'}><i>There are no team selections yet. Drag one into here to create one.</i></div>

		<ul id="{ opts.listid }" class="list-group {empty: !opts.selections}">
			<li each="{ item, i in opts.selections }"
				class="list-group-item { alternate: i >= soltsAvailble }"
				data-id="{ item.id }"
				data-order="{ item.order }"
				onclick="{ loadPresentation }"
				>
					<span class="pull-left slot-number" if="{ i < soltsAvailble }">{i+1}</span>
					<span class="pull-left slot-number" if="{ i >= soltsAvailble }">A</span>
					<div class="item-title">{ item.title }
					<span if="{ i >= soltsAvailble }">(Alternate)</span>
					</div>
			</li>
		</ul>
		<p if="{ selections.length > 0 }">{selections.length} of { slots } selected.
			<span if="{ (selections.length - soltsAvailble) > 0 }" >{selections.length - soltsAvailble } Alternates.</span>
			<span if="{ !((selections.length - soltsAvailble) > 0) }" >No alternates yet.</span>
		</p>
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
			min-height: 100px;
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

		.slot-number {
			display: block;
		}

		.item-title {
			margin-left: 20px;
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

		self.soltsAvailble = opts.slots

		sendUpdatedSort(new_sort) {
			api.trigger('save-sort-order', self.opts.selectionlist, new_sort)
		}


		// helper function to see if the list already contains an item
		self.indexOf = function(needle) {
            var i = -1, index = -1

            if (!self.opts.selections) return index

            for(i = 0; i < self.opts.selections.length; i++) {
                if(self.opts.selections[i].id == needle) {
                    index = i
                    break
                }
            }

            return index
        }


		api.on('selections-ready', function(result){

			var simpleList = document.getElementById(self.opts.listid)

			if(simpleList) {
				var sortable = Sortable.create(simpleList,{
					group: { name: "selection-list-group", pull: "clone", put: true },
					onUpdate: function(evt){

						if(!self.opts.mine && !(self.opts.listtype == 'Group')) {

							// This is horribly ugly and will be replaced with an API call instead
							self.parent.parent.parent.toasts.push(
								{
								  text: 'Oops! You can\'t sort another chair\'s list .',
								  timeout: 4000
								}
							)
							api.trigger('sort-order-saved')
							self.parent.parent.parent.update()
							return
						}

						if (evt.newIndex >= self.soltsAvailble) {
							evt.item.className = "list-group-item alternate"
						} else {
							evt.item.className = "list-group-item"
						}
						self.sendUpdatedSort(sortable.toArray())
						self.update()
					},
					onAdd: function(evt){

						if(!self.opts.mine && !(self.opts.listtype == 'Group')) {
							evt.item.parentNode.removeChild(evt.item)

							// This is horribly ugly and will be replaced with an API call instead
							self.parent.parent.parent.toasts.push(
								{
								  text: 'Oops! You can\'t add a presentation to another chair\'s list .',
								  timeout: 4000
								}
							)
							self.parent.parent.parent.update()
						}

						else if (self.indexOf(evt.item.dataset.id) > -1) {
							evt.item.parentNode.removeChild(evt.item)

							// This is horribly ugly and will be replaced with an API call instead
							self.parent.parent.parent.toasts.push(
								{
								  text: 'This presentation is already in this list.',
								  timeout: 4000
								}
							)
							self.parent.parent.parent.update()

						}

						self.sendUpdatedSort(sortable.toArray())
						self.update()
					}
				});
			}

			self.update()

		})


		loadPresentation(e) {
			self.parent.parent.parent.clearSearch()
			riot.route('presentations/show/' + e.item.item.id)
		}


	</script>


</selection-list>
