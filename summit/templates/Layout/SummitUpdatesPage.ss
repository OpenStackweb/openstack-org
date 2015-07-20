            <div class="white about-summit">
	<div class="container">
		<div class="row">
        <div id="kiosk"></div>

        <h1>Details & Updates</h1>
			
			<div class="col-lg-6 col-md-6 col-sm-6 timeline-wrapper">
            <div class="section dates-section">
					<h5 class="section-title">Dates &amp; Events</h5>
					<p>Over the week of October 27 to October 30, two events happened together: </p>
					<div>
					    <div class="left">The OpenStack Conference <span>(Tuesday - Thursday)</span></div>
					    <div class="right">This is a great place for attendees of all levels to learn about OpenStack through keynotes, speaking sessions, hands-on labs, and our ecosystem marketplace.</div>
					    <div class="left">The OpenStack Design Summit <span>(Tuesday - Friday)</span></div>
					    <div class="right">These are dedicated working sessions where OpenStack developers and operators plan the roadmap for the next software release.</div>
					</div>
				</div>			
				
				<% if ImportantDates %>
				<div class="section">
					<h5 class="section-title">Important Upcoming Dates</h5>
					<ul class="summit-timeline">
					    <% loop ImportantDates %>
                            <li>
                                <strong>$Date.Format('jS F Y')</strong>
                                <span>$Description</span>
                            </li>
						<% end_loop %>
					</ul>
				</div>
				<% end_if %>
				<div class="section">
					<h5 class="section-title">Code of Conduct</h5>
					Please review the <a href="{$Parent.Link}code-of-conduct">OpenStack Code of Conduct</a>, which applies to all events and community forums, including the OpenStack Summit.
				</div>
			</div>
			

			<div class="col-lg-6 col-md-6 col-sm-6">
				<h5 class="section-title">Summit News & Updates</h5>
				
				<% loop sortedSummitUpdates %>
                        <!-- Start News Story - with image -->
                        <div href="#" class="news-story <% if not $Image %>no-pic<% end_if %>">
                            <% if $Image %>
                                <div class="news-left" style="background-image: url($Image.URL)">
                                </div>
                            <% end_if %>
                            <div class="news-right">
                                <p class="news-title">
                                    $Title
                                </p>
                                <% if $Description %><p>$Description</p><% end_if %>
                                <div class="news-byline">
                                    <div class="posted">
                                        Posted: $Created.Format('jS F Y')
                                    </div>
                                    <div class="type">
                                        <% if $Category=='News' %>
                                            <i class="fa fa-newspaper-o"></i>News
                                        <% else %>
                                            <i class="fa fa-user"></i>$Category
                                        <% end_if %>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End News Story - with image -->
				<% end_loop %>

			</div>			
			
		</div>
	</div>
</div>
<div class="about-city-row">
	<p>
		High-tech visions of the future fused with traditional Eastern culture...
	</p>
	<h1>Come Join Us In Tokyo</h1>
	<p>
		<a href="/summit/tokyo-2015/tokyo-and-travel/" class="btn orange-btn">More About The Host City</a>
	</p>
	<a href="https://flic.kr/p/adaKoH" class="photo-credit" data-toggle="tooltip" data-placement="left" title="Photo by Magnus Larsson" target="_blank"><i class="fa fa-info-circle"></i></a>
</div>

    <!-- Pass Modal -->
    <div class="modal fade" id="passModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Summit Passes</h4>
          </div>
          <div class="modal-body">
            <p>
                You can purchase two types of passes to attend the Summit: Keynote + Marketplace Pass and Full Access Pass. 
            </p>
            <hr>
            <p>
            	<img class="pass-timeline-large" src="/summit/images/pass-timeline2.svg" onerror="this.onerror=null; this.src=/summit/images/pass-timeline2.png" alt="Vancouver Summit Pass Timeline"
            </p>
            <hr>
            <p>
                <% if $CurrentSummit.RegistrationLink %>
                    <a href="$RegistrationLink" class="modal-contact-btn"><i class="fa fa-tag"></i> Purchase Your Summit Pass</a>
                <% end_if %>
            </p>
          </div>
          <div class="modal-footer">
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
            <!-- End Page Content -->

