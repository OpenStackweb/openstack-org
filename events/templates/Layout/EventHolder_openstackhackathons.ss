</div>
<div class="oshacks-hero">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>OpenStack App Hackathons</h1>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-push-1 oshacks-intro">
            $OpenstackHackathonsContent
            <% if $OpenstackHackathonsVideoID1 && $OpenstackHackathonsVideoID2 %>
            <div class="row">
                <div class="col-xs-6">
                    <div class="video">
                        <iframe src="https://www.youtube.com/embed/{$OpenstackHackathonsVideoID1}?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen=""></iframe>
                        <p>
                            $OpenstackHackathonsVideoDesc1
                        </p>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="video">
                        <iframe src="https://www.youtube.com/embed/{$OpenstackHackathonsVideoID2}?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen=""></iframe>
                        <p>
                            $OpenstackHackathonsVideoDesc2
                        </p>
                    </div>
                </div>
            </div>
            <% end_if %>
        </div>
    </div>
</div>

<div id="oshacks-tab-stop"></div>

<div class="software-tab-wrapper">
    <div class="container">
        <ul class="nav nav-tabs project-tabs" id="oshacks-tabs" role="tablist">
            <li class="active"><a href="#events_tab" role="tab" data-toggle="tab">Upcoming Events</a></li>
            <li><a href="#host_tab" role="tab" data-toggle="tab">Host An OpenStack App Hackathon</a></li>
        </ul>
    </div>
</div>

<div class="software-main-wrapper">
    <div class="container inner-oshacks">
        <!-- Begin Page Content -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="events_tab">
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Upcoming OpenStack App Hackathons</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 oshacks-events">
                        <% if $FutureOpenstackHackathonsEvents(22) %>
                            <% loop $FutureOpenstackHackathonsEvents(22) %>

                                    <div class="row oshacks-event">
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
                        <h3>Host An OpenStack App Hackathon</h3>
                        <p>
                            Are you interested in hosting an OpenStack App Hackathon in your area?
                        </p>
                        <p>
                            First, get in touch with your local user group or community ambassador to see if there are already events happening and how you can get plugged in. You can reference the <a href="//groups.openstack.org/groups" target="_blank">list of user groups</a> and <a href="//groups.openstack.org/ambassador-program" target="_blank">ambassadors</a> to find one near you. OpenStack App Hackathons can require quite a bit of work to organize, so it’s ideal for user group leaders to help organize them to collaborate across organizations and leverage existing local infrastructure, such as attendee lists and regular sponsors.
                        </p>
                        <p>
                            Next, <a href="//www.openstack.org/brand/event-policy/">review the event guidelines</a>.  OpenStack App Hackathons are non-commercial events and we expect them to run in accordance with the spirit of the community. This means making reasonable efforts to open the events to anyone in the community who wants to help organize, attend, or sponsor, regardless of affiliation.
                        </p>
                        <p>
                            Once you’ve reviewed the guidelines, please contact <a href="mailto:flanders@openstack.org">App Hack Working Group</a> to get started.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <hr>
                        <h3>FAQs</h3>
                        <p class="question">
                            Can anyone host an OpenStack App Hackathon?
                        </p>
                        <p class="answer">
                            OpenStack App Hackathon events are typically hosted by local user groups, but anyone can get involved. If you are interested in starting a new OpenStack App Hack in your area, it’s best to get in touch with your <a href="//groups.openstack.org">local user group or regional ambassador</a>. In order to use the OpenStack brand, you must get approval from the OpenStack Foundation and meet <a href="//www.openstack.org/brand/event-policy/">the event guidelines</a>.
                        </p>
                        <p class="question">
                            Who attends OpenStack App Hackathon events?
                        </p>
                        <p class="answer">
                            These events are aimed at experienced developers who want to learn more about building cloud applications, and specifically OpenStack. Current events attract a couple hundred attendees. Most annual events grow and attract new participants each year, so it’s OK to start on the smaller end and build over time.
                        </p>
                        <p class="question">
                            How can the App Hack Working Group help?
                        </p>
                        <p class="answer">
                            The working group is comproised of leaders who have run hackathons in the past. They will provide a kit you can follow to make a successful event and provide mentoring to assist along the way.
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
