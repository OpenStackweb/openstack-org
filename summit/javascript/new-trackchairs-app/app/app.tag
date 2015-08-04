require('./sortable.tag')
require('./presnetationitem.tag')
require('./navbar.tag')
require('./raw.tag')
require('./categorymenu.tag')
require('mousetrap')
require('./forms/addcommentform.tag')
require('./comment.tag')
require('./selectionmanager.tag')
require('riotgear-toast')
require('riotgear-modal')
require('./modal.tag')
require('./chairdirectory.tag')

<app>

	<modal presentation="{ currentPresentation }" categories="{ summit.categories }" api="{ this.opts }" />

	<navbar/>
    <div class="container-fluid">

    	<rg-toast toasts="{ toasts }" position="bottomright"></rg-toast>

    	<!-- Chair Directory -->
     	<div show={ DisplayMode === 'directory' }>
    		<chairdirectory chairs="{ summit.chair_list }" />
    	</div>

    	<!-- Chair Selections -->
    	<div show={ DisplayMode === 'selections' }>
    		<selection-manager categories="{ summit.categories }" api="{ this.opts }"/>
    	</div>
    	<!-- End Chair Selections -->


    	<!-- Presntation Browser -->
    	<div show={ DisplayMode === 'browse' } class="row">
		    <div class="{ col-lg-4: details } { col-lg-12: !details }">
	        	<div class="well well-sm">
	        		<h4>{ summit.title } Presentation Submissions</h4>
	        		<hr/>
					<div class="input-group">
					  <span class="input-group-addon" id="sizing-addon2">
					  	<i if="{ !searchmode }" class="fa fa-search"></i>
					  	<i if="{ searchmode }" onclick="{ clearSearch }" class="fa fa-times"></i>
					  </span>
					  <form onsubmit="{search}">
						  <input type="text" id="app-search" class="form-control" placeholder="search..." aria-describedby="sizing-addon2">
					  </form>
					</div>
				</div>
				<categorymenu categories="{ summit.categories }" active="{ activeCategory }" if="{ !searchmode }"/>
				<div if="{ searchmode && quantity }">Showing { quantity } results</div>
				<div if="{ quantity }" class="list-group" id="presentation-list">
						<div class="row" show={ !details }>
							<div class="col-lg-9">
								&nbsp;
							</div>
							<div class="col-lg-1" >
								Ave
							</div>
							<div class="col-lg-1">
								Count
							</div>
							<div class="col-lg-1">
								Total
							</div>
						</div>

					<!-- Presentation List -->
					<div class="presentation-list" id="presentation-list">
						<presentationitem each={ presentation, i in presentations } activekey="{ activekey }" key="{ i }" data="{ presentation }" details="{details}" />
					</div>

				</div>
				<div if="{ !quantity && searchmode }">No results were found</div>
	        </div>
	        <div class="col-lg-8" show={ details }>
				<div class="panel panel-default" name="presentation-details">
					<div class="panel-heading">
						<h3 class="panel-title">Presentation Details <a href="#" onclick={ closeDetails }><i class="fa fa-times pull-right"></i></a></h3>
					</div>
					<div class="panel-body">

						<!-- Button Row -->
						<div class="row">
							<div class="col-lg-6">
								<strong>Category:</strong> { currentPresentation.category_name }
								<br/><a data-toggle="modal" data-target="#myModal" href="#"><i class="fa fa-random"></i>&nbsp;Suggest Category Change</a>
							</div>

							<div class="col-lg-6">
								<div class="btn-group pull-right" role="group" >

									<!-- My list button -->
									<button if="{ currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ unselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> My List</button>
									<button if="{ !currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ selectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> My List</button>

									<!-- Group List button -->
									<button if="{ currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupUnselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> Team List</button>
									<button if="{ !currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupSelectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> Team List</button>

								</div>

							</div>

						</div>

						<hr/>
						<h2>{ currentPresentation.title }</h2>
						<h4>{ currentPresentation.level }</h4>
						<hr/>
						<span class="label label-primary">Vote Count <span class="badge">{ currentPresentation.vote_count }</span></span>
						<span class="label label-primary">Vote Ave <span class="badge">{ currentPresentation.vote_average }</span></span>
						<span class="label label-primary">Vote Total <span class="badge">{ currentPresentation.total_points }</span></span>
						<span class="label label-info" show="{currentPresentation.comments.length}">Chair Comments: { currentPresentation.comments.length }</span>
						<hr/>
						<raw content="{ currentPresentation.description }"/>

						<div each="{ currentPresentation.speakers }">
							<hr/>
							<h4>{ first_name }&nbsp;{ last_name }</h5>
							<p>{ title }</p>
							<raw content="{ bio }"/>
						</div>
						<div>
			
						</div>
						<div if="{ currentPresentation.comments[0] }">
							<hr/>
							<h4>Comments</h4>
							<comment each="{ currentPresentation.comments }" />
							<hr/>
						</div>
						<addcommentform api="{ this.opts }" presentation="{ currentPresentation }" />
					</div>
				</div>
	        </div>   

    	</div>
    	<!-- End Presntation Browser -->
         
    </div>


    <script>

		var self = this
		this.sortitems = []
		this.DisplayMode = 'browse'

		this.toasts = [];


		// helper function to find the index of a given presentation id
		indexOf(id) {
            var i = -1, index = -1

            for(i = 0; i < self.presentations.length; i++) {
                if(self.presentations[i].id == id) {
                    index = i
                    break
                }
            }

            return index
        }

		// helper function to find a category
		categoryIndex(id) {
            var i = -1, index = -1

            for(i = 0; i < self.summit.categories.length; i++) {
                if(self.summit.categories[i].id == id) {
                    index = i
                    break
                }
            }

            return index
        }


		setActiveKey(key) {
			self.activekey = key
			id = self.presentations[key].id
			opts.trigger('load-presentation-details', id)			
			self.update()
		}

		setCategory(category) {
			self.activekey = null
			self.activeCategory = category
			var id
			if(category) id = category.id
			opts.trigger('load-presentations',null,id)
		}


		riot.route(function(mode, action, id) {
			if (mode === 'presentations') {

				self.DisplayMode = 'browse'

				if(action === 'show' && id) {
					opts.trigger('load-presentation-details', id)
					self.showDetails()
				}

				self.update()
			}

			if (mode === 'selections') {
				self.DisplayMode = 'selections'
				self.update()				
			}

			if (mode === 'directory') {
				self.DisplayMode = 'directory'
				self.update()				
			}

		})				

		this.on('mount', function(){

			console.log('window height', window.innerHeight)

			opts.trigger('load-summit-details')

			riot.route.exec(function(mode, action, id) {
				if (mode === 'presentations') {

					self.DisplayMode = 'browse'

					if(action === 'show' && id) {
						opts.trigger('load-presentation-details', id)
						self.showDetails()
					}

					self.update()
				}

				if (mode === 'selections') {
					self.DisplayMode = 'selections'
					self.update()
				}
			})			
		})

		opts.on('summit-details-loaded', function(result){
			self.summit = result
			self.setCategory(self.summit.categories[0])
		})

		self.sortPresentations = function(set, sortBy, order) {
			
			if(order === 'desc') {
				set.sort(function(a,b) {
					return b[sortBy] - a[sortBy]
				})
			} else {
				set.sort(function(a,b) {
					return a[sortBy] - b[sortBy]
				})				
			}

			return set
		}		

		opts.on('presentations-loaded', function(result){

			// default sort order
			self.presentations = self.sortPresentations(result, 'vote_average', 'asc')
			self.quantity = self.presentations.length

			if(self.currentPresentation) self.activekey = self.indexOf(self.currentPresentation.id)

			self.update()

		})


		opts.on('presentation-details-loaded', function(result){

			for(var key in result) {
			    if(result[key] === ""){
			      result[key] = " "
			    }
			 }

			console.log('currentPresentation', result)
			self.currentPresentation = result

			if(!self.searchmode) {

				cat_index = self.categoryIndex(result.category_id)
				
				if(self.activeCategory != self.summit.categories[cat_index]) {
					self.activeCategory = self.summit.categories[cat_index]
					opts.trigger('load-presentations',null,result.category_id)
				} else {
					if(self.currentPresentation) self.activekey = self.indexOf(self.currentPresentation.id)
				}

			}

			self.update()
		})

		showDetails(){
			self.details = true
			self.update()
		}

		closeDetails(){
			self.details = false
		}

		search(e){
			self.activekey = null
			self.details = false
			// prevents flash of wrong value
			self.quantity = 0
			self.searchmode = true
			opts.trigger('load-presentations', e.target[0].value)
		}

		selectPresentation(e){
			opts.trigger('select-presentation', self.currentPresentation.id)
		}

		unselectPresentation(e){
			opts.trigger('unselect-presentation', self.currentPresentation.id)
		}

		groupSelectPresentation(e){
			opts.trigger('group-select-presentation', self.currentPresentation.id)
		}

		groupUnselectPresentation(e){
			opts.trigger('group-unselect-presentation', self.currentPresentation.id)
		}

		opts.on('presentation-selected', function(){
			self.currentPresentation.selected = true;
			self.opts.trigger('load-selections',self.currentPresentation.category_id)				
			self.toasts.push(
				{
				  text: 'The presentation was added to your selection list.',
				  timeout: 6000
				}
			)

			presIndex = self.indexOf(self.currentPresentation.id)
			self.presentations[presIndex].selected = true

			self.update();

		})

		opts.on('presentation-unselected', function(){
			self.currentPresentation.selected = false

			presIndex = self.indexOf(self.currentPresentation.id)

			self.presentations[presIndex].selected = false

			self.opts.trigger('load-selections',self.currentPresentation.category_id)
			self.toasts.push(
				{
				  text: 'The presentation was removed from your selection list.',
				  timeout: 6000
				}
			)
			self.update();							
		})		

		opts.on('presentation-group-selected', function(){
			self.currentPresentation.group_selected = true;
			self.opts.trigger('load-selections',self.currentPresentation.category_id)				
			self.toasts.push(
				{
				  text: 'The presentation was added to the team selection list.',
				  timeout: 6000
				}
			)

			self.update();

		})

		opts.on('presentation-group-unselected', function(){
			self.currentPresentation.group_selected = false

			self.opts.trigger('load-selections',self.currentPresentation.category_id)
			self.toasts.push(
				{
				  text: 'The presentation was removed from the team selection list.',
				  timeout: 6000
				}
			)
			self.update();							
		})	

		clearSearch() {
			document.getElementById('app-search').value='';
			self.quantity = 0
			self.searchmode = false
			opts.trigger('load-presentations', null, this.activeCategory.id)
			self.activekey = null			
			self.update()
		}

		Mousetrap.bind('n', function() {
			nextPresKey = self.activekey + 1
			if(nextPresKey + 1 > self.presentations.length) nextPresKey = 0
			self.setActiveKey(nextPresKey)
		})

		Mousetrap.bind('p', function() {
			nextPresKey = self.activekey - 1
			if(nextPresKey === -1) nextPresKey = self.presentations.length - 1
			self.setActiveKey(nextPresKey)
		})


		Mousetrap.bind('s', function() {
			if(
				self.currentPresentation.can_assign &&
				!self.currentPresentation.selected
			) 
			{
				self.selectPresentation()
			 }
		})		

	</script>


</app>