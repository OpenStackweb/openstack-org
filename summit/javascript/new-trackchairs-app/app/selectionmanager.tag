require('./selection-list.tag')
require('./selectionmenu.tag')

<selection-manager>


	<h2>
		{ activeCategory.title } Track
		<selectionmenu categories="{ summit.track_chair.categories }" if="{ summit.track_chair.categories.length > 1 }" active="{ activeCategory }" />
	</h2>

	<hr/>

	<selection-list each="{ lists }" 
		name="List" listname="{ list_name }" 
		selections={ selections } 
		listid="{ 'list' + list_id }" 
		selectionlist="{ list_id }" 
		mine="{ mine }"
		listtype="{ list_type }"
		slots="{ slots }" 
		category="{ activeCategory }" />


	<script>
		var self = this

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
			self.parent.details = false
			self.parent.setCategory(category)
		}

		opts.api.on('selections-loaded', function(result){

			// see if we need to update the selections we are looking at
			if(result.category_id == self.activeCategory.id) {

				self.lists = []
				self.update()
				self.lists = result.lists
				self.update()
				opts.api.trigger('selections-ready')

			}

		})

		opts.api.on('sort-order-saved', function(){
			self.setCategory(self.activeCategory)
		})


	</script>

</selection-manager>