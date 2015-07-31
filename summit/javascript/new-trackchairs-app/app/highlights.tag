<highlights collection="summit-highlights">
	<div class="keynote-highlights">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1>Highlights from the Keynotes</h1>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="clicked-keynote-highlight" style="background-image:url('{ url(featureditem.fields.backgroundImage) }')">
						<div class="clicked-keynote-description">
							<h4>{ featureditem.fields.title }</h4>
							<p>
								{ featureditem.fields.description }
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="keynote-highlight-row">
					<div class="keynote-highlight-day">
						{ collection.name }
					</div>
					<div class="col-sm-3" each={ collection, i }>
						<a href="#" class="keynote-highlight-single" onclick={ parent.setFeatured }>
							<div class="keynote-highlight-thumb { active: featured }">
								<img src={ parent.url(fields.previewImage) } alt="">
							</div>
							<div class="keynote-highlight-title">
								{ fields.title }
							</div>
						</a>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-8 col-sm-push-2 keynote-highlights-action">
					<p>
						Now you can watch videos of these keynotes and almost every other session!
					</p>
					<p>
						<a href="http://www.openstack.org/summit/vancouver-2015/summit-videos/" class="red-btn">Watch Summit Videos Now</a>
					</p>
				</div>
			</div>

		</div>
	</div>

	<script>

		// intialize the collection
		var json = require("json!../_data/" + this.opts.collection + ".json")
		this.collection = json.items
		this.collection.images = json.includes.Asset
		this.collection.name = 'Day 1'

		// By default, set the first item to be featured
		this.featureditem = this.collection[0]
		this.featureditem.featured = true

		// set the variable self so we can reference it in the function below
		var self = this

		setFeatured(e) {
			// turn off current featured item
			self.featureditem.featured = false
			// set this item as featured
			e.item.featured = true
			self.featureditem = e.item
		}

		// This function takes a contenful resource ID, finds the associated asset, and returns the asset's URL
		url(asset) {
			// Grab the image identifier from the item object
			var imageId = asset.sys.id
			// Find that identifier in the arry of included images
			var result = this.collection.images.filter(function( obj ) {
			  return obj.sys.id == imageId
			});
			return result[0].fields.file.url
		}		

	</script>

</highlights>