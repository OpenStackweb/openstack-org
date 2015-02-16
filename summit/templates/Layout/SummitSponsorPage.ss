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
				<h1>Interested being a Vancouver Summit Sponsor?</h1>
				<p>
					Having a presence at the OpenStack Summit is a great way to get your company in front of the OpenStack community. There are five available levels of sponsorship: Headline, Premier, Spotlight, Event and Startup and a number of add-on opportunities.  You can read about the various options in the Sponsorship Prospectus.
				</p>
				<div class="row">
					<div class="col-lg-12 prospectus-wrapper">
        				<% if $SponsorProspectus %>               
                            <a class="btn register-btn-lrg sponsor" href="{$Link}prospectus">Download Sponsor Prospectus</a>
                        <% end_if %>
                        <% if $SponsorContract %>
                            <a class="btn register-btn-lrg contract" href="{$Link}contract">Sponsor Contract</a>
                        <% end_if %>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="light city-nav sponsor">
	<div class="container">
		<ul class="city-nav-list">
			<li>
				<a href="#packages">
					<i class="fa fa-tags"></i>
					Packages
				</a>
			</li>
			<li>
				<a href="#how-to-sponsor">
					<i class="fa fa-question-circle"></i>
					How To Sponsor
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
<div class="light" id="packages">
	<div class="container sponsor-wrapper">
	<div class="row">
		<div class="col-lg-8 col-lg-push-2">
			<h1>Packages</h1>
			<h5 class="section-title">
				Sponsorships Packages Available <span>(prices in USD)</span>
			</h5>
				<div class="row">
				
				    <% loop $SortedPackages %>
					<div class="col-lg-4 col-md-4 col-sm-4">
						<div class="sponsor-spots <% if $SoldOut %>sold-out<% end_if %>">
							<h3>$Title <span>$SubTitle</span></h3>
							<div class="sponsor-cost">
								$Cost.Nice
							</div>
							<div class="sponsor-count">
							    <% if $SoldOut %>
							        Sold Out
							    <% else %>
							        <% if $ShowQuantity %>
                                        <td>Available: $CurrentlyAvailable of $MaxAvailable</td>
                                    <% else %>
                                        <td>Still Available</td>
                                    <% end_if %>
								<% end_if %>
							</div>
						</div>
					</div>
					<% end_loop %>

				</div>
			<div class="sponsor-note">
				* In order to qualify for a Startup sponsorship a company must be in business for less than 3 years and have less than $5 million USD in revenue.
			</div>
		</div>
	</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-8 col-lg-push-2">
						<h5 class="section-title">Sponsorship Add-Ons Available <span>(prices in USD)</span></h5>
					</div>
				</div>
				<div class="table-responsive sponsor-table">
				  <table class="table">
					<thead>
						<tr>
							<th>Add-On Package</th>
							<th>Cost</th>
							<th>Available</th>
						</tr>
					</thead>
					<tbody>
					
                        <% loop $SortedAddOns %>
                            <tr <% if $SoldOut %>class="sold-out"<% end_if %>>
                                <td>$Title</td>
                                <td>$Cost</td>
                                <% if $SoldOut %>
                                    <td>Sold Out</td>
                                <% else %>
                                   <% if $ShowQuantity %>
                                    <td>$CurrentlyAvailable of $MaxAvailable</td>
                                    <% else %>
                                    <td>Available</td>
                                    <% end_if %>
                                <% end_if %>
                            </tr>
                        <% end_loop %>					
					</tbody>
				  </table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="sponsor-bkgd">
	<div class="fixed-image exhibit"></div>
</div>
<div class="light sponsor-instructions" id="how-to-sponsor">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<h1>
					Steps to Becoming a Sponsor
					<span>Please Read Completely</span>
				</h1>
				
				$SponsorSteps
				
			</div>
				<% if $AttachedFile %>
                    <div class="col-lg-12 prospectus-wrapper">
                        <a class="btn register-btn-lrg sponsor" href="{$Link}download">Download Sponsor Prospectus</a>
                        <a class="btn register-btn-lrg contract" href="{$Link}downloadContract">Download Sponsor Contract</a>
                        <a href="mailto:events@openstack.org" class="contact-link">Contact us with any questions</a>
                    </div>
				<% end_if %>				
			
		</div>
	</div>
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

