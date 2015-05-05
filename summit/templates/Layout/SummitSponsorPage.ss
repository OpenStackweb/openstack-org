            <div class="white sponsor-page-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
			    <% if $SponsorAlert %>
				<div class="alert alert-info sponsor-alert">
					$SponsorAlert
				</div>
				<% end_if %>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-8 col-lg-push-2 sponsor-intro">
				<h1>Thank You To The OpenStack Summit Sponsors</h1>
				<p>
					The generous support of our sponsors makes it possible for our community to gather, learn and build the future of cloud computing. A warm thank you to all of our sponsors for the May 2015 OpenStack Summit.
				</p>
			</div>
		</div>
	</div>
</div>
<div class="light city-nav sponsor">
	<div class="container">
		<ul class="city-nav-list">
			<li>
				<a href="#sponsors">
					<i class="fa fa-heart"></i>
					Sponsors
				</a>
			</li>
			<li>
				<a href="#venue-map">
					<i class="fa fa-map-marker"></i>
					Venue Maps
				</a>
			</li>
			<li>
				<a href="#audience">
					<i class="fa fa-group"></i>
					Audience
				</a>
			</li>
		</ul>
	</div>
</div>
<div class="white sponsor-list" id="sponsors">
	<div class="container">
		<% if HeadlineSponsors %>
		<div class="row">
			<div class="col-lg-8 col-lg-push-2">
				<h5 class="section-title">
					Headline Sponsors
				</h5>
			</div>
			<div class="col-lg-12">
				<div class="row">
					<% loop HeadlineSponsors %>
					<div class="col-lg-3 col-md-3 col-sm-3">
						<a rel="nofollow" href="{$SubmitLandPageUrl}">$SubmitLogo</a>
					</div>
					<% end_loop %>
				</div>
			</div>
		</div>
		<% end_if %>
		<% if PremierSponsors %>
		<div class="row">
			<div class="col-lg-8 col-lg-push-2">
				<h5 class="section-title">
					Premier Sponsors 
				</h5>
			</div>
			<div class="col-lg-8 col-lg-push-2">
				<div class="row">
					<% loop PremierSponsors %>
					<div class="col-lg-3 col-md-3 col-sm-3">
			                        <a rel="nofollow" href="{$SubmitLandPageUrl}">
										$SubmitLogo
			                        </a>
					</div>
					<% end_loop %>
				</div>
			</div>
		</div>
		<% end_if %>
		<% if SpotlightSponsors %>
		<div class="row">
			<div class="col-lg-8 col-lg-push-2">
				<h5 class="section-title">
					Spotlight Sponsors 
				</h5>
			</div>
			<div class="col-lg-8 col-lg-push-2">
				<div class="row">
					<% loop SpotlightSponsors %>
					<div class="col-lg-2 col-md-2 col-sm-2">
			                        <a rel="nofollow" href="{$SubmitLandPageUrl}">
										$SubmitLogo
			                        </a>
			                </div>
					<% end_loop %>
				</div>
			</div>
		</div>
		<% end_if %>
		<% if EventSponsors %>
            <div class="row">
                <div class="col-lg-8 col-lg-push-2">
                    <h5 class="section-title">
                        Event Sponsors
                    </h5>
                </div>
                <div class="col-lg-8 col-lg-push-2">
                    <div class="row">
					<% loop EventSponsors %>
					<div class="col-lg-2 col-md-2 col-sm-2">
			                        <a rel="nofollow" href="{$SubmitLandPageUrl}">
										$SubmitLogo
			                        </a>
			                </div>
					<% end_loop %>
                    </div>
                </div>
            </div>
		<% end_if %>
		<% if StartupSponsors %>
            <div class="row">
                <div class="col-lg-8 col-lg-push-2">
                    <h5 class="section-title">
                        Startup Sponsors
                    </h5>
                </div>
                <div class="col-lg-8 col-lg-push-2">
                    <div class="row">
					<% loop StartupSponsors %>
					<div class="col-lg-2 col-md-2 col-sm-2">
			                        <a rel="nofollow" href="{$SubmitLandPageUrl}">
										$SubmitLogo
			                        </a>
			                </div>
					<% end_loop %>
                    </div>
                </div>
            </div>
		<% end_if %>
		<% if InKindSponsors %>
            <div class="row">
                <div class="col-lg-8 col-lg-push-2">
                    <h5 class="section-title">
                        Community Partners
                    </h5>
                </div>
                <div class="col-lg-8 col-lg-push-2">
                    <div class="row">
					<% loop InKindSponsors %>
					<div class="col-lg-2 col-md-2 col-sm-2">
			                        <a rel="nofollow" href="{$SubmitLandPageUrl}">
										$SubmitLogo
			                        </a>
			                </div>
					<% end_loop %>
                    </div>
                </div>
            </div>
		<% end_if %>
        <% if MediaSponsors %>
            <div class="row">
                <div class="col-lg-8 col-lg-push-2">
                    <h5 class="section-title">
                        Media Sponsors
                    </h5>
                </div>
                <div class="col-lg-8 col-lg-push-2">
                    <div class="row">
                        <% loop MediaSponsors %>
                            <div class="col-lg-3 col-md-. col-sm-3">
                                <a rel="nofollow" href="{$SubmitLandPageUrl}">
                                    $SubmitLogo
                                </a>
                            </div>
                        <% end_loop %>
                    </div>
                </div>
            </div>
        <% end_if %>
	</div>
</div>
<div class="sponsor-bkgd">
	<div class="fixed-image exhibit"></div>
</div>
<div class="white" id="venue-map"> 
	<div class="container">
		<div class="col-lg-12">
			<h1>Venue Maps</h1>
			<div class="row">
				<div class="col-sm-4 venue-map-link">
					<a href="https://www.openstack.org/assets/vancouver-summit/openstack-vancouver-venue-maps-draft.pdf" target="_blank">
						<img src="/summit/images/map-exhibition-level.png" class="sponsor-logo">
						<h4>Exhibition Level</h4>
					</a>
				</div>
				<div class="col-sm-4 venue-map-link">
					<a href="https://www.openstack.org/assets/vancouver-summit/openstack-vancouver-venue-maps-draft.pdf" target="_blank">
						<img src="/summit/images/map-sponsored.png" class="sponsor-logo">
						<h4>Sponsored Lounge</h4>
					</a>
				</div>
				<div class="col-sm-4 venue-map-link">
					<a href="https://www.openstack.org/assets/vancouver-summit/openstack-vancouver-venue-maps-draft.pdf" target="_blank">
						<img src="/summit/images/map-sponsored-design.png" class="sponsor-logo">
						<h4>Sponsored Lounge in Design Summit</h4>
					</a>
				</div>
			</div> 
			<div class="row">
				<div class="col-lg-12 venue-map-download">
					<a href="https://www.openstack.org/assets/vancouver-summit/openstack-vancouver-venue-maps-draft.pdf" target="_blank">
						<p><i class="fa fa-cloud-download"></i>Download All Maps</p>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="sponsor-bkgd">
	<div class="fixed-image crowd"></div>
</div>
<div class="white" id="audience">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-push-2">
				<h1>Audience</h1>
				<p class="audience-intro">
					The Summit has experienced tremendous growth since its inception, and we're proud of the diverse audience reached at each one. Here's a quick look at the audience who attended Paris Summit in November 2014.
				</p>
			</div>
		</div>
			<div class="row">
			<div class="col-lg-12">
                <h5 class="section-title">November 2014 Paris OpenStack Summit Metrics:</h5>
				<div class="row">
					<div class="col-lg-8 col-lg-push-2 stats-highlight">
						<h3>4,600+<span>Total Summit Attendees</span></h3>
						<h3>876<span>Companies Represented</span></h3>
						<h3>62<span>Countries Represented</span></h3>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-5 col-lg-push-1 col-md-6 col-sm-6">
						<div class="attendees-region">
							<h4>Attendees By Region</h4>
							<!-- Region Chart -->
							<canvas id="attendeesRegion" width="250" height="250" style="width: 250px; height: 250px;"></canvas>
						</div>
					</div>
					<div class="col-lg-5 col-lg-push-1 col-md-6 col-sm-6">
						<div class="attendees-region">
							<h4>Attendees By Role</h4>
							<!-- Role Chart -->
							<canvas id="attendeesRole" width="250" height="250" style="width: 250px; height: 250px;"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

