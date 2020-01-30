</div>
<div class="osdays-hero">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <img class="osdays-logo" src="{$Top.CloudUrl('assets/openstack-logo/openstackdays/days-hero-mark.svg')}" alt="OpenStack Days">
                <h1>$Title</h1>
            </div>
        </div>
    </div>
</div>

<div class="days-pretext"><a href="#tab=events_tab" class="deep-link" data-tab="events_tab">Find an upcoming OpenStack or OpenInfra Day in your region!</a></div>

<div class="software-tab-wrapper">
    <div class="container">
        <ul class="nav nav-tabs project-tabs" id="osdays-tabs" role="tablist">
            <li class="active tab-about_tab nav-item">
                <a href="#about_tab" class="nav-link" role="tab" data-toggle="tab">About OpenStack & OpenInfra Days</a>
             </li>
            <li class="tab-host_tab nav-item">
                <a href="#host_tab" class="nav-link" role="tab" data-toggle="tab">Host An OpenStack or OpenInfra Day</a>
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
                </div>
                <h2 class="tab-title">Highlights From Recent OpenStack Days</h2>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                        <% loop $AboutVideos().Sort(SortOrder) %>
                            <div class="col-md-4 text-center">
                                <iframe width="350" height="180" src="//www.youtube.com/embed/{$YoutubeID}" frameborder="0" allowfullscreen></iframe>
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
                        <a href="$Event.EventLink()" target="_blank">
                            <img src="$Picture.getUrl()" width="200" height="130"/>
                        </a>
                        <p>
                            <a href="$Event.EventLink()" target="_blank">$Event.Title</a>
                            <span class="font-13">$Event.getLocation()</span>
                            <span class="font-12">$Event.formatDateRange()</span>
                        </p>
                    </div>
                    <% end_loop %>
                </div>
                <div class="text-center more-events">
                    <a href="" id="more_events" data-category="OpenStack Days">See More Past Events [+]</a>
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
                                $Label
                            </div>
                            <div class="col-md-5 col-xs-6"><a href="$Doc.Link" target="_blank" class="download">DOWNLOAD</a></div>
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
                                        <a href="$Doc.Link" target="_blank" class="download">$Label</a>
                                    <% end_loop %>
                                    </div>
                                <% else %>
                                    <div class="col-md-7 col-xs-6"> $Label </div>
                                    <div class="col-md-5 col-xs-6"><a href="$Doc.Link" target="_blank" class="download">DOWNLOAD</a></div>
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
                        $Thumbnail.SetRatioSize(200,150).getTag()
                        <p>$Label</p>
                        <a class="download" href="$Doc.Link" target="_blank">Download</a>
                    </li>
                    <% end_loop %>
                </ul>

                <span class="hr margin0-0-24"></span>

                <h3 class="blue-title">Marketing / Branding / Video /Presentations / Collateral</h3>
                <div class="mrtop20">
                    $CollateralIntro
                </div>
                <ul class="content-list">
                    <% if Collaterals().Exists() %>
                    <% loop Collaterals().Sort(SortOrder) %>
                        <li>
                            <iframe width="240" height="140" src="//www.youtube.com/embed/{$YoutubeID}" frameborder="0" allowfullscreen></iframe>
                            <p>$Caption</p>
                        </li>
                    <% end_loop %>
                    <% end_if %>
                    <% if CollateralsDocs().Exists() %>
                    <% loop CollateralsDocs() %>
                        <li>
                            <div><img src="$Thumbnail.getUrl()" height="140" width="240" alt="$Label" /></div>
                            <div><a href="$Doc.Link" target="_blank">$Label</a></div>
                        </li>
                    <% end_loop %>
                    <% end_if %>
                </ul>

                <span class="hr margin0-0-24"></span>

                <p>
                    <strong>OpenStack Day Logo and Digital Assets</strong>
                </p>
                <p>
                    The OpenStack Foundation has created an official OpenStack Day logo and digital assets to be used for all OpenStack Days events.
                    For both trademark and legal reasons, all official OpenStack Day events are required to use these assets in all event
                    communications, printed materials, signage, and website presence.  To request your official OpenStack Day logo and branding
                    kit, please send an email to events@openstack.org
                </p>

                <p>
                    OpenStack Marketing Collateral<br/>
                    <a href="https://www.openstack.org/marketing#tab=collateral">https://www.openstack.org/marketing#tab=collateral</a>
                </p>

                <p>
                    Openstack Brand Policy<br/>
                    <a href="https://www.openstack.org/brand/">https://www.openstack.org/brand/</a>
                </p>

                <p>
                    OpenStack Privacy Policy<br/>
                    <a href="https://www.openstack.org/privacy/">https://www.openstack.org/privacy/</a>
                </p>

                <p>
                    OpenStack Trademark Policy<br/>
                    <a href="https://www.openstack.org/brand/openstack-trademark-policy/">
                        https://www.openstack.org/brand/openstack-trademark-policy/
                    </a>
                </p>

                <p>&nbsp;</p>
                <span class="hr margin0-0-24"></span>

                <h3 class="blue-title">PR / Media</h3>
                <div class="row host-an">
                    <% loop Media() %>
                    <div class="col-md-4 <% if $Pos > 1 %>brl1<% end_if %>">
                        $Label
                        <a href="$Doc.Link" target="_blank" class="download">DOWNLOAD</a>
                    </div>
                    <% end_loop %>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane fade in" id="events_tab">
                <div class="inner-osdays">
                    <h2 class="tab-title">OpenStack and OpenInfra Days by Region</h2>
                    <div class="osdays-events">
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><strong>Date</strong></div>
                        <div class="col-sm-4 col-xs-4"><strong>Event Name</strong></div>
                        <div class="col-sm-3 col-xs-3"><strong>Location</strong></div>
                        <div class="col-sm-3 col-xs-2"><strong>Region</strong></div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">TBA</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Taiwan</div>
                        <div class="col-sm-3 col-xs-3">Taipei, Taiwan</div>
                        <div class="col-sm-3 col-xs-2">APAC</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">TBA</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day China</div>
                        <div class="col-sm-3 col-xs-3">Beijing, China</div>
                        <div class="col-sm-3 col-xs-2">APAC</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">July 15, 2020</div></div>
                        <div class="col-sm-4 col-xs-4">Cloud Operator Day Japan</div>
                        <div class="col-sm-3 col-xs-3">Tokyo, Japan</div>
                        <div class="col-sm-3 col-xs-2">APAC</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">TBA</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Vietnam</div>
                        <div class="col-sm-3 col-xs-3">Vietnam</div>
                        <div class="col-sm-3 col-xs-2">APAC</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">TBA</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Korea</div>
                        <div class="col-sm-3 col-xs-3">Korea</div>
                        <div class="col-sm-3 col-xs-2">APAC</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">Q4</div></div>
                        <div class="col-sm-4 col-xs-4">Open Infrastructure Day Indonesia</div>
                        <div class="col-sm-3 col-xs-3">Indonesia</div>
                        <div class="col-sm-3 col-xs-2">APAC</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">March 17, 2020</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Turkey</div>
                        <div class="col-sm-3 col-xs-3">Istanbul, Turkey</div>
                        <div class="col-sm-3 col-xs-2">Europe</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">Q2</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day UK</div>
                        <div class="col-sm-3 col-xs-3">London, UK</div>
                        <div class="col-sm-3 col-xs-2">Europe</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">Q2</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Poland</div>
                        <div class="col-sm-3 col-xs-3">Poland</div>
                        <div class="col-sm-3 col-xs-2">Europe</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">Q3</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Hungary</div>
                        <div class="col-sm-3 col-xs-3">Hungary</div>
                        <div class="col-sm-3 col-xs-2">Europe</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">Q4</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Benelux</div>
                        <div class="col-sm-3 col-xs-3">The Netherlands</div>
                        <div class="col-sm-3 col-xs-2">Europe</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">Q4</div></div>
                        <div class="col-sm-4 col-xs-4">OpenStack/Infra Day Russia</div>
                        <div class="col-sm-3 col-xs-3">Russia</div>
                        <div class="col-sm-3 col-xs-2">Europe</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">Q4</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Italy</div>
                        <div class="col-sm-3 col-xs-3">Italy</div>
                        <div class="col-sm-3 col-xs-2">Europe</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">Q4</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Nordics</div>
                        <div class="col-sm-3 col-xs-3">Sweden - TBC</div>
                        <div class="col-sm-3 col-xs-2">Europe</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">TBA</div></div>
                        <div class="col-sm-4 col-xs-4">OpenInfra Day Canada</div>
                        <div class="col-sm-3 col-xs-3">Ottawa, Canada</div>
                        <div class="col-sm-3 col-xs-2">North America</div>
                      </div>
                      <div class="row osdays-event">
                        <div class="col-sm-2 col-xs-3"><div class="osd-date">May 4-5, 2020</div></div>
                        <div class="col-sm-4 col-xs-4">OpenStack Day DR Congo</div>
                        <div class="col-sm-3 col-xs-3">Congo</div>
                        <div class="col-sm-3 col-xs-2">Central Africa</div>
                      </div>
                    </div>
                    <h2 class="tab-title">Mark Your Calendars</h2>
                    <div class="osdays-events">
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

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
