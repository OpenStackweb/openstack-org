    <!-- Marketplace and Events -->
    <div class="banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-6">
                    <div class="marketplace-column">
                        <h2>Need an Enterprise Solution?</h2>
                        <p>
                            The OpenStack Marketplace will help you make an informed decision, whether you're building a cloud, looking to use one by the hour, or pursuing a hybrid model.
                        </p>
                        <ul>
                            <li class="training">Training</li>
                            <li class="distros">Distros</li>
                            <li class="consulting">Consulting</li>
                            <li class="public-cloud">Public Cloud</li>
                            <li class="drivers">Drivers</li>
                            <li class="hosted-cloud">Hosted Private Cloud</li>
                        </ul>
                        <a href="http://www.openstack.org/marketplace/" class="marketplace-btn blue">OpenStack Marketplace</a>
                    </div>
                </div>
                <div class="col-lg-8 col-sm-6">
                    <div class="events-wrapper">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <h2>You Can Also Find Us At Our Upcoming Enterprise Events</h2>
                            </div>
                        </div>
                        <!--<div class="row events">
                            <div class="col-lg-6 events-col">
                                <% with EnterpriseEvents(1).First %>
                                <a href="$EventLink" class="featured-event-link lrg">
                                    <img class="event-logo paris" src="$EventSponsorLogoUrl" alt="$Title">
                                    <span>$Title</span>
                                    <span>$formatDateRange() in $EventLocation</span>
                                </a>
                                <% end_with %>
                            </div>
                            <div class="vert-line"></div>
                            <div class="col-lg-6 events-col">
                                <% with SummitEvent %>
                                <a href="$EventLink" class="featured-event-link">
                                    <img class="event-logo paris" src="$EventSponsorLogoUrl" alt="OpenStack Summit in $EventLocation">
                                    <span>OpenStack Summit</span>
                                    <span>$formatDateRange() in $EventLocation</span>
                                </a>
                                <% end_with %>
                            </div>
                        </div>-->
                        <div class="row events">
                            <div class="col-lg-6 events-col">
                                <a href="/summit/barcelona-2016/" class="featured-event-link lrg">
                                    <img class="event-logo paris" src="//openstack.org/themes/openstack/images/enterprise/legacy/summit-event-logo.png" alt="$Title">
                                    <span>OpenStack Summit</span>
                                    <span>October 25-28, 2016 in Barcelona, Spain</span>
                                </a>
                            </div>
                            <div class="vert-line"></div>
                            <div class="col-lg-6 events-col">
                                <% with SummitEvent %>
                                <a href="$EventLink" class="featured-event-link">
                                    <img class="event-logo paris" src="//openstack.org/themes/openstack/images/enterprise/legacy/summit-event-logo.png" alt="OpenStack Summit in $EventLocation">
                                    <span>OpenStack Summit</span>
                                    <span>May 8-12, 2017 in Boston, Massachusetts</span>
                                </a>
                                <% end_with %>
                            </div>
                        </div>
                        <div class="row more-events">
                                <hr>
                                <div class="col-lg-3">
                                    <h4>More Events:</h4>
                                </div>
                                <div class="col-lg-9">
                                    <ul>
                                        <% loop EnterpriseEvents(3) %>
                                        <li><a href="$EventLink" target="_blank">$Title</a>, $formatDateRange() in $EventLocation</li>
                                        <% end_loop %>
                                    </ul>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container -->
    </div>
    <!-- /.banner -->
