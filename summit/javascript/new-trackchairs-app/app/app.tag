require('./sortable.tag')
require('./presnetationitem.tag')
require('./navbar.tag')
require('./raw.tag')
require('./categorymenu.tag')
require('mousetrap')
require('./forms/addcommentform.tag')
require('./comment.tag')
require('./selectionmanager.tag')
require('./rg/rg-toast.js')
require('./rg/rg-modal.js')
require('./modal.tag')
require('./chairdirectory.tag')
require('./tutorial.tag')
require('./change-requests.tag')
require('./comments-list.tag')



<app>

	<modal presentation="{ currentPresentation }" categories="{ summit.categories }" api="{ this.opts }" />

	<navbar admin="{ summit.track_chair.is_admin }" />
    <div class="container-fluid">

    	<rg-toast toasts="{ toasts }" position="bottomright"></rg-toast>

    	<!-- Tutorial -->
     	<div show={ DisplayMode === 'tutorial' }>
    		<tutorial/>
    	</div>

    	<!-- Chair Directory -->
     	<div show={ DisplayMode === 'directory' }>
    		<chairdirectory chairs="{ summit.chair_list }" />
    	</div>

    	<!-- Chair Selections -->
    	<div show={ DisplayMode === 'selections' }>
    		<selection-manager categories="{ summit.categories }" api="{ this.opts }" visible="{ DisplayMode === 'selections' }"/>
    	</div>
    	<!-- End Chair Selections -->

    	<!-- Change Requests Browser -->
    	<div show={ DisplayMode === 'requests' }>
    		<change-requests api="{ this.opts }"/>
    	</div>
    	<!-- Change Requests Browser -->

			<!-- Change Requests Browser -->
    	<div show={ DisplayMode === 'comments' }>
    		<comments-list api="{ this.opts }" visible="{ DisplayMode === 'comments' }"/>
    	</div>
    	<!-- Change Requests Browser -->

    	<!-- Presentation Browser -->
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
							<div class="col-lg-9 col-md-9 hidden-sm hidden-xs">
								&nbsp;
							</div>
							<div class="col-lg-1 col-md-1 hidden-sm hidden-xs" >
								Ave
							</div>
							<div class="col-lg-1 col-md-1 hidden-sm hidden-xs">
								Count
							</div>
							<div class="col-lg-1 col-md-1 hidden-sm hidden-xs">
								Total
							</div>
						</div>

					<!-- Presentation List -->
					<div class="presentation-list" id="presentation-list">
						<presentationitem each={ presentation, i in presentations } activekey="{ activekey }" key="{ i }" data="{ presentation }" details="{details}" />
					</div>
					<div>
						<ul id="presentation-list-pager"></ul>
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
								<br/><a if="{ !(summit.on_selection_period || summit.is_selection_period_over) }" data-toggle="modal" data-target="#myModal" href="#"><i class="fa fa-random"></i>&nbsp;Suggest Category Change</a>
							</div>

							<div class="col-lg-6">
								<div class="btn-group pull-right" role="group" >

									<!-- My list button -->
									<button show="{ currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ unselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> My List</button>
									<button show="{ !currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ selectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> My List</button>

									<!-- Group List button -->
									<button show="{ currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupUnselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> Team List</button>
									<button show="{ !currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupSelectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> Team List</button>

								</div>

							</div>

						</div>

						<hr/>
						<h2>{ currentPresentation.title }</h2>
						<h4>{ currentPresentation.level } | Submitted By: { currentPresentation.creator }</h4>
						<hr/>
						<span class="label label-primary">Vote Count <span class="badge">{ currentPresentation.vote_count }</span></span>
						<span class="label label-primary">Vote Ave <span class="badge">{ currentPresentation.vote_average }</span></span>
						<span class="label label-primary">Vote Total <span class="badge">{ currentPresentation.total_points }</span></span>
						<span class="label label-info" show="{currentPresentation.comments.length}">Chair Comments: { currentPresentation.comments.length }</span>
						<hr/>
						<h4>Description</h4>
						<raw content="{ currentPresentation.description }"/>

						<hr/>
						<h4>Problems Addressed</h4>
						<raw content="{ currentPresentation.problem_addressed }"/>

						<hr/>
						<h4>Why Should This Presentation Be Selected?</h4>
						<raw content="{ currentPresentation.selection_motive }"/>

						<hr/>
						<h4>What Should Attendees Expect To Learn?</h4>
						<raw content="{ currentPresentation.attendees_expected_learnt }"/>

						<div each="{ currentPresentation.speakers }">
							<hr/>
							<h5>Speaker: <strong>
							<img class="img-circle profile-pic"  title="{ first_name+' '+last_name }" src="{ photo_url }"/>
							<a if="{ available_for_bureau }" href="/community/speakers/profile/{ id }" title="{ first_name+' '+last_name }" target="_blank">{ first_name }&nbsp;{ last_name }</a>
							<span if="{ !available_for_bureau }">{ first_name }&nbsp;{ last_name }</span>		</strong></h5>
							<p>
									{ title }
									<br/>
									<a href="mailto:{ email }">{ email }</a>
							</p>
							<raw content="{ bio }"/>
							<div if="{ former_presentations.length > 0 }">
							<strong>Presentations from previous OpenStack Summits:</strong>
							<ul class="list-unstyled">
							<li each="{ former_presentations }"><a href="{ url }" title="{ title }" target="_blank">{ title }</a></li>
							</ul>
							</div>
							<div if="{ other_links.length > 0 }">
								<strong>Additional presentations:</strong>
								<ul class="list-unstyled">
									<li each="{ other_links }"><a href="{ url }" title="{ title }" target="_blank">{ title }</a></li>
								</ul>
							</div>
						</div>
						<div>

						</div>

						<hr/>
						<h4>Chair Comments</h4>
						<p>(These are only visible to Track Chairs)</p>

						<div if="{ currentPresentation.comments[0] }">
							<comment each="{ currentPresentation.comments }" />
							<hr/>
						</div>
						<addcommentform api="{ this.opts }" presentation="{ currentPresentation }" commenter="{ summit.track_chair.first_name + ' ' + summit.track_chair.last_name }" />
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
		this.presentation_query = null;
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
			riot.route('presentations/show/' + id)
		}

		setCategory(category) {
			self.activekey = null
			self.activeCategory = category
			var id
			if(category) id = category.id
			console.log('setCategory '+id)
			opts.trigger('load-presentations',null,id)
		}

		this.on('mount', function(){

			opts.trigger('load-summit-details')

		})

		opts.on('summit-details-loaded', function(result){
			self.summit = result
			if(self.summit.track_chair.categories) self.setCategory(self.summit.categories[0])
			self.update()
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

		opts.on('presentations-loaded', function(response){

			console.log('presentations loaded')

			self.presentations = response.results
			self.quantity = self.presentations.length

			if(self.currentPresentation) self.activekey = self.indexOf(self.currentPresentation.id)

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

				if (mode === 'tutorial') {
					self.DisplayMode = 'tutorial'
					self.update()
				}

				if (mode === 'requests') {
					self.DisplayMode = 'requests'
					self.update()
				}

				if (mode === 'comments') {
					self.DisplayMode = 'comments'
					self.update()
				}


			})

			// fire up the router defined above and route based on current URL
			riot.route.start(true)

			var options = {
				bootstrapMajorVersion:3,
				currentPage: response.page ,
				totalPages:  response.total_pages,
				numberOfPages: 10,
				onPageChanged: function(e,oldPage,newPage){
					console.log('page ' + newPage);
					$('body').ajax_loader();
					opts.trigger('load-presentations', self.presentation_query, self.activeCategory.id, newPage)
				}
			}


			self.update()

			if (response.results.length){
				$('#presentation-list-pager').bootstrapPaginator(options);
			}

			$('body').ajax_loader('stop');

		})


		opts.on('presentation-details-loaded', function(result){

			console.log('presentations details loaded')

			// Entirely clear out any previous display elements
			self.currentPresentation = []
			self.update()

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

			self.presentation_query = e.target[0].value;
			console.log('search '+self.presentation_query)
			var id = null;
			if(self.activeCategory) id = self.activeCategory.id;
			opts.trigger('load-presentations', self.presentation_query, id)
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
			$('#app-search').val('');
			self.quantity = 0
			self.searchmode = false
			self.presentation_query = null;
			console.log('clearSearch')
			var id = null;
			if(self.activeCategory) id = self.activeCategory.id;
			opts.trigger('load-presentations', self.presentation_query, id)
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
