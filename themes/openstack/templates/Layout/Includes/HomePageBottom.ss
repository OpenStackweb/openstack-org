    <!-- Page Content -->

    <div class="content-section-a">

        <div class="container">
            <div class="row overview-section">
                <div class="col-lg-6 col-sm-12 overview-left">
                    <h2>
                        Open source software for creating private and public clouds.
                    </h2>
                    <p>OpenStack software controls large pools of compute, storage, and networking resources throughout a datacenter, managed through a <a href="/software/openstack-dashboard/">dashboard</a> or via the <a href="http://developer.openstack.org">OpenStack API</a>. OpenStack works with <a href="/marketplace/drivers/">popular enterprise and open source technologies</a> making it ideal for heterogeneous infrastructure.</p>

                    <p><a href="/user-stories/">Hundreds of the world’s largest brands</a> rely on OpenStack to run their businesses every day, reducing costs and helping them move faster. OpenStack has a strong <a href="/foundation/companies/">ecosystem</a>, and users seeking commercial support can choose from different OpenStack-powered products and services in the <a href="/marketplace/">Marketplace.</a></p>

                    <p>The software is built by a <a href="/community/">thriving community</a> of developers, in collaboration with users, and is designed in the open at our <a href="/summit/">Summits</a>.</p>
                    
                    <div class="btn-wrapper">
                        <a href="/software/" class="overview-btn left-btn">How It Works <i class="fa fa-arrow-circle-right"></i></a>
                        <a href="/software/start/" class="overview-btn right-btn">Get The Software <i class="fa fa-arrow-circle-down"></i></a>
                    </div>
                    <div class="release-text">Latest Release: <a href="/software/train/">Train</a> (October 2019)</div>
                </div>
                <div class="col-lg-6 col-sm-12 overview-right">
                    <img class="img-responsive control-cloud-graphic" src="{$Top.CloudUrl("assets/homepage/dashboard-illustration-2020.svg")}" onerror="this.onerror=null; this.src=assets/homepage/dashboard-illustration-2020.svg" alt="OpenStack Cloud Software">
                    <a href="http://www.youtube.com/watch?v=z6ftW7fUdp4?autoplay=1" target="_blank" class="demo-link">Watch a Demo of the Dashboard <i class="fa fa-play-circle"></i></a>
                </div>
            </div>

        </div>
        <!-- /.container -->

    </div>
    <!-- /.content-section-a -->

    <div class="customers-row">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <h2>
                        The Most Innovative Companies In The World Move Faster With OpenStack
                    </h2>
                </div>
                <div class="col-lg-12 col-sm-12 customer-logos-wrapper">
                    <div class="customer-logos" id="chinamobile-logo"><img src="{$Top.CloudUrl('assets/homepage/company-logos/chinamobile2.png')}" alt="China Mobile Uses OpenStack"></div>
                    <div class="customer-logos" id="target-logo"><img src="{$Top.CloudUrl('assets/homepage/company-logos/target.png')}" alt="Target Uses OpenStack"></div>
                    <div class="customer-logos" id="progressive-logo"><img src="{$Top.CloudUrl('assets/homepage/company-logos/progressive.png')}" alt="Progressive Uses OpenStack"></div>
                    <div class="customer-logos logo-hover" id="cathay-logo"><img src="{$Top.CloudUrl('assets/homepage/company-logos/cathaypacific.png')}" alt="Comcast Uses OpenStack">
                    </div>
                    <div class="customer-logos"id="paypal-logo"><img src="{$Top.CloudUrl('images/customer-paypal.png')}" alt="PayPal Uses OpenStack"></div>
                    </div>
                </div>
                <div class="col-lg-12 col-sm-12">
                    <div class="customers-description">
                        <p class="change-description">
                            <strong>Comcast</strong> delivers interactive entertainment to millions of living rooms.
                        </p>
                    </div>
                </div>
                <div class="col-lg-12 col-sm-12 customers-action">
                    <a href="/user-stories/" class="customer-btn">Read More User Stories</a>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-section-b -->

    <!-- Community Section -->
    <div class="banner community-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <object type="image/svg+xml" class="img-responsive community-graphic"
                            data="{$Top.CloudUrl("images/community-graphic.svg")}?people=$MembersCount&countries=$CountryCount&organizations=$OrganizationCount&codelines=20M+" onerror="this.onerror=null; this.src='{$Top.CloudUrl("/images/community-graphic.png")}'" alt="OpenStack Community">
                    </object>
                </div>
                <div class="col-md-6">
                    <h2>One of the fastest growing open source communities in the world.</h2>
                    <p>
                        The OpenStack project is a global collaboration of developers and cloud computing technologists producing the open standard cloud computing platform for both public and private clouds. Backed by a vibrant community of developers and some of the biggest names in the industry.
                    </p>
                    <p>
                        <a href="/community/" class="community-btn">Get Involved <i class="fa fa-play-circle"></i></a>
                    </p>
                </div>
            </div>
        </div>
        <!-- /.container -->
    </div>
    <!-- End Community Section -->
    <!-- News Section -->
    <div class="news-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-6 news-wrapper">
                    <h2>Upcoming Events <a href="/community/events/">See All <i class="fa fa-caret-right"></i></a></h2>
                    <% if $getEventsBanner() %>
                    <a href="{$getEventsBannerLink()}">
                        <img class="event-ad" src="{$getEventsBanner().URL}">
                    </a>
                    <% end_if %>
                    $UpcomingEvents(20)
                </div>
                <div class="col-lg-6 col-sm-6 news-wrapper">
                    <h2>News <a href="/news">See More News <i class="fa fa-caret-right"></i></h2>
                    <a href="/passport"><img class="news-ad" src="{$Top.CloudUrl('assets/passport/passport-banner-homepage.png')}"></a>
                    
                       <% loop NewsItems %>
                            <!-- News Item -->
                            <a href="{$link}" class="single-event">
                                <div class="left-event">
                                    <div class="planet-type">$type</div>
                                </div>
                                <div class="event-details">
                                    <div class="news-title">$title</div>
                                    <div class="news-date">$pubdate</div>
                                </div>
                                <div class="right-event">
                                    <div class="right-arrow"><i class="fa fa-chevron-right"></i></div>
                                </div>
                            </a>
                        <% end_loop %>
                </div>
            </div>
        </div>
    </div>
   
    <!-- End News Section -->
