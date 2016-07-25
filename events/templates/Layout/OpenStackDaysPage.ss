</div>
<div class="osdays-hero">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>OpenStack Days</h1>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12"> $EventsYearlyCountText() </div>
        <div class="col-md-12">
            <% loop $HeaderPics.Sort(SortOrder) %>
                $CroppedImage(200,100).getTag()
            <% end_loop %>
        </div>
    </div>
</div>

<div id="osdays-tab-stop"></div>

<div class="software-tab-wrapper">
    <div class="container">
        <ul class="nav nav-tabs project-tabs" id="osdays-tabs" role="tablist">
            <li class="active"><a href="#about_tab" role="tab" data-toggle="tab">About OpenStack Days</a></li>
            <li><a href="#host_tab" role="tab" data-toggle="tab">Host An OpenStack Day</a></li>
            <li><a href="#events_tab" role="tab" data-toggle="tab">Upcoming Events</a></li>
        </ul>
    </div>
</div>

<div class="software-main-wrapper">
    <div class="container inner-osdays">
        <!-- Begin Page Content -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="about_tab">
                <div class="row">
                    <div class="col-sm-12"> $AboutDescription </div>
                    <div class="col-sm-12"> $EventsYearlyCountText() </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Highlights</h3>
                        <div class="row">
                        <% loop $AboutVideos().Sort(SortOrder) %>
                            <div class="col-md-3">
                                <iframe src="//www.youtube.com/embed/{$YoutubeID}?rel=0&amp;showinfo=0&amp;modestbranding=1&amp;controls=2" frameborder="0" height="120" width="250">
                                </iframe><br>
                                $Caption
                            </div>
                        <% end_loop %>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <h3>Check out more of the Past...</h3>
                    <% loop $FeaturedEvents().Limit(8) %>
                    <div class="col-md-3">
                        $Picture.CroppedImage(200,100).getTag()
                        $Event.Title
                    </div>
                    <% end_loop %>
                </div>
                <div class="row">
                    <button class="btn btn-default">See More</button>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="host_tab">
                <div class="row">
                    <div class="col-sm-12">
                        $HostIntroAndFAQs
                    </div>
                </div>
                <hr>
                <div class="row">
                    <h3>Openstack Days Toolkit</h3>
                    <div class="col-sm-12">
                        $ToolkitDesc
                    </div>
                </div>
                <div class="row">
                    <h4> Official Guidelines </h4>
                    <% loop OfficialGuidelines() %>
                        $Title
                    <% end_loop %>
                </div>
                <div class="row">
                    <h4> Planning Tools </h4>
                    <% loop PlanningTools() %>
                        $Title
                    <% end_loop %>
                </div>
                <div class="row">
                    <h4> Artwork For Print </h4>
                    <% loop Artwork() %>
                        $Title
                    <% end_loop %>
                </div>
                <div class="row">
                    <h4> Video / Presentations / Collateral </h4>
                    <% if Collaterals().Exists() %>
                    <% loop Collaterals().Sort(SortOrder) %>
                        <div class="col-md-3">
                            <iframe src="//www.youtube.com/embed/{$YoutubeID}?rel=0&amp;showinfo=0&amp;modestbranding=1&amp;controls=2" frameborder="0" height="120" width="250">
                            </iframe><br>
                            $Caption
                        </div>
                    <% end_loop %>
                    <% end_if %>
                </div>
                <div class="row">
                    <h4> PR / Media </h4>
                    <% loop Media() %>
                        $Title
                    <% end_loop %>
                </div>

            </div>
            <div role="tabpanel" class="tab-pane fade" id="events_tab">
                <div class="row">
                    <div class="col-sm-12 osdays-events">
                        <% if $FutureOpenstackDaysEvents(22) %>
                            <% loop $FutureOpenstackDaysEvents(22) %>
                                <div class="row osdays-event">
                                    <div class="col-sm-2 col-xs-3">
                                        <div class="osd-date"> $formatDateRange </div>
                                    </div>
                                    <div class="col-sm-4 col-xs-4">
                                        <div class="osd-name"> $Title </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="osd-location"> $Location, $Continent </div>
                                    </div>
                                    <% if $EventLink %>
                                    <div class="col-sm-3 col-xs-2">
                                        <a rel="nofollow" href="$EventLink" target="_blank" data-type="$EventCategory">
                                            <div class="osd-link"></div>
                                        </a>
                                    </div>
                                    <% end_if %>
                                </div>
                            <% end_loop %>
                        <% else %>
                            <h3>Sorry, there are no upcoming events listed at the moment.</h3>
                            <p class="details">
                                Wow! It really rare that we don't have any upcoming events on display.
                                Somewhere in the world there's sure to be an OpenStack event in the near future&mdash;
                                We probably just need to update this list. Please check back soon for more details.
                            </p>
                        <% end_if %>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
