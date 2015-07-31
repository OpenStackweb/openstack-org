require('./selection-list.tag')
<selection-manager>

	<div class="row">
		<div class="col-lg-12">
			<categorymenu categories="{ summit.track_chair.categories }" active="{ activeCategory }" />
			<hr/>
		</div>
	</div>

	<selection-list each="{ lists }" name="List" listname="{ list_name }" selections={ selections } listid="{ 'list' + list_id }" selectionlist="{ list_id }" />

	<script>
		var selections = null
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
		}

		opts.api.on('selections-loaded', function(result){
			console.log('5a. selectionmanager hears selections-loaded.')
			self.listsLoaded = true
			self.lists = []
			self.update()
			self.lists = result
			self.update()
			console.log('5b. selectionmanager fires selections-ready.')			
			opts.api.trigger('selections-ready')
		})

	</script>

</selection-manager>