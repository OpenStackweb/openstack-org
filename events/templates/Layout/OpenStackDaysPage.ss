</div>
<div class="osdays-hero">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <img class="osdays-logo" src="./themes/openstack/images/osdays/days-logo.png" alt="">
                <h1>OpenStack Days</h1>
            </div>
        </div>
    </div>
</div>

<div class="days-pretext">$EventsYearlyCountText() - <a href="/community/events/openstackdays#tab=events_tab">find an upcoming OpenStack Day in your region!</a></div>

<div class="container slider-container">
    <section class="regular slider">
        <% loop $HeaderPics.Sort(SortOrder) %>
            <div> <img src="$getUrl()" width="260" /> </div>
        <% end_loop %>
    </section>
</div>

<div class="software-tab-wrapper">
    <div class="container">
        <ul class="nav nav-tabs project-tabs" id="osdays-tabs" role="tablist">
            <li class="active tab-about_tab nav-item">
                <a href="#about_tab" class="nav-link" role="tab" data-toggle="tab">About OpenStack Days</a>
             </li>
            <li class="tab-host_tab nav-item">
                <a href="#host_tab" class="nav-link" role="tab" data-toggle="tab">Host An OpenStack Day</a>
            </li>
            <li class="tab-events_tab nav-item">
                <a href="#events_tab" class="nav-link" role="tab" data-toggle="tab">Upcoming Events</a>
            </li>
        </ul>
    </div>
</div>

<div class="tab-page osdays-page">
    <div class="container">
        <!-- Begin Page Content -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="about_tab">
                <div class="row">
                    <div class="col-sm-12"> $AboutDescription </div>
                    <div class="col-sm-12">
                        <p class="mr5-30">$EventsYearlyCountText() - <a href="/community/events/openstackdays#tab=events_tab">find an upcoming OpenStack Day in your region!</a> </p>
                    </div>
                </div>
                <h2 class="tab-title">Highlights From Recent OpenStack Days</h2>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                        <% loop $AboutVideos().Sort(SortOrder) %>
                            <div class="col-md-4 text-center">
                                <a href="https://www.youtube.com/watch?v={$YoutubeID}" target="_blank">
                                    <img src="$getThumbnailUrl()" width="350" height="180" />
                                </a>
                                <p>$Caption</p>
                            </div>
                        <% end_loop %>
                        </div>
                    </div>
                </div>

                <span class="hr"></span>

                <h2 class="tab-title">Check Out More of the Past OpenStack Days!</h2>
                <div class="row featured_events">
                    <% loop $FeaturedEvents().Limit(4) %>
                    <div class="col-md-3 featured_event">
                        <img src="$Picture.getUrl()" width="200" />
                        <p>
                            $Event.Title
                            <span class="font-13">$Event.getLocation()</span>
                            <span class="font-12">$Event.formatDateRange()</span>
                        </p>
                    </div>
                    <% end_loop %>
                </div>
                <div class="text-center more-events">
                    <a href="" id="more_events">See More Past Events [+]</a>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade in hostan-page" id="host_tab">
                <div class="row">
                    <div class="col-sm-12">
                        $HostIntro
                        <a href="mailto:events@openstack.org" class="dark-blue">Contact Us For More Details</a>
                        <span class="hr"></span>
                        $HostFAQs
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        $ToolkitDesc
                    </div>
                </div>
                <h3 class="blue-title">Official Guidelines</h3>
                <div class="row host-an">
                    <% loop OfficialGuidelines() %>
                    <div class="col-md-4 <% if $Pos > 1 %>brl1<% end_if %>">
                        <div class="row">
                            <div class="col-md-7 col-xs-6">
                                $Title
                            </div>
                            <div class="col-md-5 col-xs-6"><a href="$Link" target="_blank" class="download">DOWNLOAD</a></div>
                        </div>
                    </div>
                    <% end_loop %>
                    <div class="col-md-4 brl1"></div>
                </div>

                <span class="hr margin24-0"></span>

                <h3 class="blue-title">Planning Tools</h3>
                <div class="row host-an">
                    <% loop getGroupedPlanningTools() %>
                        <div class="col-md-4 <% if $Pos > 1 %>brl1<% end_if %>">
                            <div class="row">
                                <% if $Group %>
                                    <div class="col-md-7 col-xs-6"> $Group </div>
                                    <div class="col-md-5 col-xs-6">
                                    <% loop $Tools %>
                                        <a href="$Link" target="_blank" class="download">$Title</a>
                                    <% end_loop %>
                                    </div>
                                <% else %>
                                    <div class="col-md-7 col-xs-6"> $Title </div>
                                    <div class="col-md-5 col-xs-6"><a href="$Link" target="_blank" class="download">DOWNLOAD</a></div>
                                <% end_if %>
                            </div>
                        </div>
                    <% end_loop %>
                </div>

                <span class="hr margin24-0"></span>

                <h3 class="blue-title">Artwork for Print</h3>
                <div class="mrtop20">
                    $ArtworkIntro
                </div>
                <ul class="content-list">
                    <% loop Artwork() %>
                    <li>
                        $SetRatioSize(200,150).getTag()
                        <p>$Title</p>
                        <a class="download" href="$getUrl()" target="_blank">Download</a>
                    </li>
                    <% end_loop %>
                </ul>

                <span class="hr margin0-0-24"></span>

                <h3 class="blue-title">Video / Presentations / Collateral</h3>
                <div class="mrtop20">
                    $CollateralIntro
                </div>
                <ul class="content-list">
                    <% if Collaterals().Exists() %>
                    <% loop Collaterals().Sort(SortOrder) %>
                        <li>
                            <a href="https://www.youtube.com/watch?v={$YoutubeID}" target="_blank">
                                <img src="$getThumbnailUrl" width="240" height="140" />
                            </a>
                            <p>$Caption</p>
                            <a class="download" target="_blank" href="https://www.youtube.com/watch?v={$YoutubeID}">WATCH</a>
                        </li>
                    <% end_loop %>
                    <% end_if %>
                </ul>

                <span class="hr margin0-0-24"></span>

                <h3 class="blue-title">PR / Media</h3>
                <div class="row host-an">
                    <% loop Media() %>
                    <div class="col-md-4 <% if $Pos > 1 %>brl1<% end_if %>">
                        $Title
                        <a href="$Link" target="_blank" class="download">DOWNLOAD</a>
                    </div>
                    <% end_loop %>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane fade in" id="events_tab">
                <div class="inner-osdays">
                    <h2 class="tab-title">Highlights From Recent OpenStack Days</h2>
                    <div class="osdays-events">
                        <% if $FutureOpenstackDaysEvents(22) %>
                            <% loop $FutureOpenstackDaysEvents(22) %>
                            <div class="row osdays-event">
                                <div class="col-sm-2 col-xs-3"><div class="osd-date">$formatDateRange</div></div>
                                <div class="col-sm-4 col-xs-4">$Title</div>
                                <div class="col-sm-3 col-xs-3">$Location</div>
                                <div class="col-sm-3 col-xs-2 text-right to-top">
                                    <a href="$EventLink">More Details</a>
                                    <a class="more-img" href="$EventLink"></a>
                                </div>
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
