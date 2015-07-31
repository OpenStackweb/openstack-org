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

<app>

	<navbar/>
    <div class="container-fluid">

    	<rg-toast toasts="{ toasts }" position="bottomright"></rg-toast>

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
					<div class="col-lg-12" show={ !details }>
						<div class="row">
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
					</div>				
					<presentationitem each={ presentation, i in presentations } key="{ i }" data="{ presentation }" details="{details}" />
				</div>
				<div if="{ !quantity && searchmode }">No results were found</div>
	        </div>
	        <div class="col-lg-8" show={ details }>
				<div class="panel panel-default" name="presentation-details">
					<div class="panel-heading">
						<h3 class="panel-title">Presentation Details <a href="#" onclick={ closeDetails }><i class="fa fa-times"></i></a></h3>
					</div>
					<div class="panel-body">
						<button if="{ currentPresentation.selected }" type="button" onclick="{ selectPresentation }" class="btn btn-default">Unselect</button>
						<button if="{ !currentPresentation.selected }" type="button" onclick="{ selectPresentation }" class="btn btn-default">Select</button>
						<hr/>
						<h2>{ currentPresentation.title } ({ currentPresentation.id })</h2>
						<h4>{ currentPresentation.level }</h4>
						<raw content="{ currentPresentation.description }"/>
						<div>
							<span class="label label-info">Comments: { currentPresentation.comments.length }</span>
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
		this.DisplayMode = 'selections'

		this.toasts = [];		

		this.on('mount', function(){
			opts.trigger('load-summit-details')
		})

		setActiveKey(key) {
			self.activekey = key
			id = self.presentations[key].id
			opts.trigger('load-presentation-details', id)			
			self.update()
		}

		setCategory(category) {
			self.activeCategory = category
			var id
			if(category) id = category.id
			opts.trigger('load-presentations',null,id)
			opts.trigger('load-selections',category.id)
		}

		opts.on('summit-details-loaded', function(result){
			self.summit = result
			opts.trigger('load-presentations')
		})		

		opts.on('presentations-loaded', function(result){
			self.presentations = result
			self.quantity = self.presentations.length
			self.details = true

			// if there are results 

			if( self.presentations[0] ) {
				self.results = true
				id = self.presentations[0].id
				opts.trigger('load-presentation-details', id)
				self.activekey = 0
				self.update()
			} else {
				self.results = false
			}
			self.update()
		})

		opts.on('presentation-details-loaded', function(result){

			for(var key in result) {
			    if(result[key] === ""){
			      result[key] = " "
			    }
			 }

			self.currentPresentation = result
			console.log('currentPresentation ', self.currentPresentation)
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
			// prevents flash of wrong value
			self.quantity = 0
			self.searchmode = true
			opts.trigger('load-presentations', e.target[0].value)
		}

		selectPresentation(e){
			console.log('1. app is firing select-presentaiton')
			opts.trigger('select-presentation', self.currentPresentation.id)
		}

		opts.on('presentation-selected', function(){
			console.log('3a. app heard presentation-selected.');
			self.currentPresentation.selected = true;
			console.log('3b. app is firing load presentations for cat ' + self.currentPresentation.category_id);	
			self.opts.trigger('load-selections',self.currentPresentation.category_id)				
			self.toasts.push(
				{
				  text: 'The presentation was added to your selection list.',
				  timeout: 6000
				}
			)
			self.update();							
		})

		clearSearch() {
			document.getElementById('app-search').value='';
			self.quantity = 0
			self.searchmode = false
			opts.trigger('load-presentations')			
			self.update()
		}

		Mousetrap.bind('down', function() { 
			self.setActiveKey(self.activekey + 1)
		})


	</script>


</app>