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
        <div class="col-sm-10 col-sm-push-1 osdays-intro">
            $OpenstackDaysContent
            <% if $OpenstackDaysVideoID1 && $OpenstackDaysVideoID2 %>
            <div class="row">
                <div class="col-xs-6">
                    <div class="video">
                        <iframe src="https://www.youtube.com/embed/{$OpenstackDaysVideoID1}?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen=""></iframe>
                        <p>
                            $OpenstackDaysVideoDesc1
                        </p>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="video">
                        <iframe src="https://www.youtube.com/embed/{$OpenstackDaysVideoID2}?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen=""></iframe>
                        <p>
                            $OpenstackDaysVideoDesc2
                        </p>
                    </div>
                </div>
            </div>
            <% end_if %>
        </div>
    </div>
</div>

<div id="osdays-tab-stop"></div>

<div class="software-tab-wrapper">
    <div class="container">
        <ul class="nav nav-tabs project-tabs" id="osdays-tabs" role="tablist">
            <li class="active"><a href="#events_tab" role="tab" data-toggle="tab">Upcoming Events</a></li>
            <li><a href="#host_tab" role="tab" data-toggle="tab">Host An OpenStack Day</a></li>
        </ul>
    </div>
</div>

<div class="software-main-wrapper">
    <div class="container inner-osdays">
        <!-- Begin Page Content -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="events_tab">
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Upcoming OpenStack Days</h3>
                    </div>
                </div>
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
            <div role="tabpanel" class="tab-pane fade" id="host_tab"> <!-- Host Tab -->
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Host An OpenStack Day</h3>
                        <p>
                            Are you interested in hosting an OpenStack Day in your area?
                        </p>
                        <p>
                            First, get in touch with your local user group or community ambassador to see if there are already events happening and how you can get plugged in. You can reference the <a href="//groups.openstack.org/groups" target="_blank">list of user groups</a> and <a href="//groups.openstack.org/ambassador-program" target="_blank">ambassadors</a> to find one near you. OpenStack Days can require quite a bit of work to organize, so it’s ideal for user group leaders to help organize them to collaborate across organizations and leverage existing local infrastructure, such as attendee lists and regular sponsors.
                        </p>
                        <p>
                            Next, <a href="//www.openstack.org/brand/event-policy/">review the event guidelines</a>.  OpenStack Days are non-commercial events and we expect them to run in accordance with the spirit of the community. This means making reasonable efforts to open the events to anyone in the community who wants to help organize, attend, or sponsor, regardless of affiliation. It also means avoiding sales pitches and focusing on visionary, educational and user-driven content.
                        </p>
                        <p>
                            The OpenStack Foundation helps support OpenStack Days with funding, content and promotion. Once you’ve reviewed the guidelines, please contact <a href="mailto:events@openstack.org">events@openstack.org</a> to get started.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <hr>
                        <h3>FAQs</h3>
                        <p class="question">
                            Can anyone host an OpenStack Day event?
                        </p>
                        <p class="answer">
                            OpenStack Day events are typically hosted by local user groups, but anyone can get involved. If you are interested in starting a new OpenStack Day in your area, it’s best to get in touch with your <a href="//groups.openstack.org">local user group or regional ambassador</a>. In order to use the “OpenStack Day” brand, you must get approval from the OpenStack Foundation and meet <a href="//www.openstack.org/brand/event-policy/">the event guidelines</a>.
                        </p>
                        <p class="question">
                            Who attends OpenStack Day events?
                        </p>
                        <p class="answer">
                            OpenStack Day events vary based on the organizers and local communities. Some events are more focused on business issues and cloud strategy, while some are more focused on technical operators and developers. Most events attract a few hundred attendees, but the largest event had more than 1,500 participants. Most annual events grow and attract new participants each year, so it’s OK to start on the smaller end and build over time.
                        </p>
                        <p class="question">
                            How can the OpenStack Foundation help?
                        </p>
                        <p class="answer">
                            Once an event is approved, the Foundation typically provides sponsorship funding (up to 5,000 USD depending on the size of the event), logos and digital branding assets, support attracting key speakers in the community and promotion. Please contact <a href="mailto:events@openstack.org">events@openstack.org</a> for more information and to get started.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<%--
<% with FeaturedEvent %>
    $Title
    $Picture
<% end_with %>
--%>