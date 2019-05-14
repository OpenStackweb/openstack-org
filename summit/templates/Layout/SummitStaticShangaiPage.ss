<div class="white about-summit-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                <h4 class="summit-title script">The Open Infrastructure Summit</h4>
                <h2>Home of Open Development</h2>
                <div class="bird">
                    <h5>
                        <strong>
                            <a href="http://twitter.com/openstack" target="_blank">
                                <i class="fab fa-twitter" style="font-size: 24px;color:#ffd602;"></i>
                            </a>
                            <a href="https://www.facebook.com/openstack/" target="_blank">
                                <i class="fab fa-facebook-f" style="margin-left:15px;font-size: 24px;color:#ffd602;"></i>
                            </a>
                            <a href="" data-toggle="modal" data-target="#wechat-modal">
                                <i class="fab fa-weixin" style="margin-left:15px;font-size: 24px;color:#ffd602;"></i>
                            </a>
                        </strong>
                    </h5>
                </div>
            </div>

            <div id="wechat-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <img width="150" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/summits/shanghai/qrcode-for-gh-5cc38c749efd-1280.jpg" />
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-6 col-sm-12 about-buttons">

                <p>Join the people building and operating open infrastructure, with hundreds of sessions and workshops on Container Infrastructure, CI/CD, Telecom + NFV, Public Cloud, Private & Hybrid Cloud, Security and members of open source communities like Airship, Ansible, Ceph, Docker, Kata Containers, Kubernetes, ONAP, OpenStack, Open vSwitch, OPNFV, StarlingX, Zuul and more.</p>

                <a class="btn btn-default" href="/summit/shanghai-2019/sponsors/">Sponsor the Summit <i class="fal fa-arrow-right"></i></a>

                <% include EventbriteRegisterLink Position='m',ExtraClass='btn-primary',Dropdown=1 %>

            </div>
            <div class="col-md-12 session-box">
                <h3>Presentations, Workshops, and Collaborative Sesssions</h3>
                <ul class="sessions">
                    <li class="session"> <span class="ai"></span> AI</li>
                    <li class="session"> <span class="cicd"></span> CI/CD</li>
                    <li class="session"> <span class="container"></span> Containers</li>
                    <li class="session"> <span class="edge"></span> Edge Computing</li>
                    <li class="session"> <span class="hybrid"></span> Hybrid Cloud</li>
                    <li class="session"> <span class="hpc"></span> HPC</li>
                    <li class="session"> <span class="nfv"></span> NFV</li>
                    <li class="session"> <span class="cloud"></span> Public & Private Cloud</li>
                    <li class="session"> <span class="security"></span> Security</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="what-to-expect" id="expect">
    <div class="container">
        <div class="row">
            <div class="video-container col-md-7 col-sm-12">
                <h3 class="title-section">What to Expect at the Summit</h3>
                <a href="https://youtu.be/8I2P1QKxUZA" class="video">


                    <span>
                        <i class="fal fa-play"></i>
                    </span>

                </a>
            </div>
            <div class="text-container col-md-5 col-sm-12">
                <h3 class="title-section">Who Attends the Summit</h3>
                <p>Thousands of IT decision makers, operators and developers will gather at the Summit to collaborate
                    across common use cases and solve real problems. In Shangai, you can meet people from over 50 countries
                    and 600 companies using and contributing to over 35 open source projects.
                </p>

                <ul class="attends">
                    <li><span class="architecture"></span>Architecture & Ops</li>
                    <li><span class="platform"></span>Platform Developers</li>
                    <li><span class="bussiness"></span>Business & Strategy</li>
                    <li><span class="upstream"></span>Upstream Developers</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- <div class="light about-summit-users-wrapper" id="{$Name}">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-lg-8 col-md-6">
                <h3 class="title-section">Shangai 2019 Open Infrastructure Summit Featured Speakers</h3>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/baidu.jpg')}" alt="yu bai" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Yu Bai</div>
                            <div class="title">Baidu</div>
                            <div class="topic">Deploying Kata Containers on the Edge</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/profile-images/duc-new-me.jpg')}" alt="Duc Truong"
                                 class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Duc Truong</div>
                            <div class="title">Blizzard Entertainment</div>
                            <div class="topic">Autoscaling with OpenStack for Overwatch</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/chsmall.jpg')}" alt="Chris Small" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Chris Small</div>
                            <div class="title">Boeing</div>
                            <div class="topic">OpenStack Upgrades</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/JenniferCloer.jpg')}" alt="Jennifer Cloer" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Jennifer Cloer</div>
                            <div class="title">Chasing Grace Project </div>
                            <div class="topic">Diversity in Tech</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/+.jpg')}" alt="Xiaoguang Zhang"
                                 class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Xiaoguang Zhang</div>
                            <div class="title">China Mobile</div>
                            <div class="topic">5G based on OpenStack</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/Maestropandy.jpg')}" alt="Pandiyan Muthuraman" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Pandiyan Muthuraman</div>
                            <div class="title">Reliance Jio</div>
                            <div class="topic">Cognitive Computing at the Edge with AI </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/neelima.jpg')}" alt="Neelima Mukiri"
                                 class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Neelima Mukiri</div>
                            <div class="title">Cisco Systems Inc</div>
                            <div class="topic">Machine Learning with a Hybrid Cloud</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/profile-images/5ae358d7c4723.jpg')}" height="174"
                                 alt="Joseph Sandoval" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Joseph Sandoval</div>
                            <div class="title">Adobe Systems</div>
                            <div class="topic">Kubernetes Lessons Learned</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/profile-images/20170707172318.jpg')}" alt="Jinghua Gao" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Jinghua Gao</div>
                            <div class="title">Lenovo</div>
                            <div class="topic">Deep Learning for Heart Disease Diagnosis </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/headshot2.png')}" alt="Paul Belanger" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Paul Belanger</div>
                            <div class="title">Red Hat Inc.</div>
                            <div class="topic">Moving from Jenkins to Zuul </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/profile-pic2.png')}" alt="Michael Beaver" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">Michael Beaver</div>
                            <div class="title">AT&amp;T Integrated Cloud</div>
                            <div class="topic">Bare metal provisioning with Airship</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-4 col-xs-4 featured">
                    <div class="summit-user-section">
                        <div class="summit-user-image-box">
                            <img src="{$Top.CloudUrl('assets/Uploads/penick-profile-pic2.png')}" alt="James Penick" class="summit-user-image">
                        </div>
                        <div class="info">
                            <div class="name">James Penick</div>
                            <div class="title">Verizon Media</div>
                            <div class="topic">Edge Computing</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <a class="btn" href="/summit/shanghai-2019/speakers">See all summit speakers <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4 col-md-6 col-xs-12">
                <h3 class="title-section">Summit Overview</h3>
                <div class="col-12 col-md-12 col-sm-6 col-xs-12 featured">
                    <ul class="overview">
                        <li><a href="https://www.openstack.org/summit/shanghai-2019/summit-schedule/events/22721/openstack-upstream-institute-sponsored-by-lenovo-day-1-rsvp-required"><h5>OpenStack Upstream Institute</h5>
                            <p><small>Sunday - Monday</small></p> <i class="fal fa-info-circle"></i></a></li>
                        <li><a href="https://www.openstack.org/summit/shanghai-2019/summit-schedule/global-search?t=Keynotes"><h5>Keynotes</h5>
                            <p><small>Monday-Wednesday</small></p> <i class="fal fa-info-circle"></i></a></li>
                        <li><a href="https://www.openstack.org/summit/shanghai-2019/summit-schedule"><h5>Breakout Sessions</h5>
                            <p><small>Monday-Wednesday</small></p> <i class="fal fa-info-circle"></i></a></li>
                        <li><a href="https://wiki.openstack.org/wiki/Forum"><h5>The Forum</h5>
                            <p><small>Monday-Wednesday</small></p> <i class="fal fa-info-circle"></i></a></li>
                        <li><a href="https://www.openstack.org/summit/shanghai-2019/summit-schedule/"><h5>Workshops and Trainings</h5>
                            <p><small>Monday-Wednesday</small></p> <i class="fal fa-info-circle"></i></a></li>
                        <li><a href="https://www.openstack.org/summit/shanghai-2019/summit-schedule/global-search?t=Marketplace"><h5>Open Infrastructure Marketplace</h5>
                            <p><small>Monday-Wednesday</small></p> <i class="fal fa-info-circle"></i></a></li>
                        <li><a href="https://www.openstack.org/ptg"><h5>Project Teams Gathering (PTG)</h5>
                            <p><small>Thursday-Saturday</small></p> <i class="fal fa-info-circle"></i></a></li>
                    </ul>

                </div>

                <div class="col-xs-12">
                    <a class="btn" href="/summit/shanghai-2019/summit-schedule/">Full Summit Schedule<i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                {$Text}
            </div>
        </div>
    </div>
</div>
-->
<div class="academy-wrapper" id="Sponsors">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="recap-title script">Thank you to our Shanghai 2019 Sponsors!
                </h3>
            </div>


            <div id="sponsorsCarousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#sponsorsCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#sponsorsCarousel" data-slide-to="1"></li>
                    <li data-target="#sponsorsCarousel" data-slide-to="2"></li>
                    <li data-target="#sponsorsCarousel" data-slide-to="3"></li>
                    <li data-target="#sponsorsCarousel" data-slide-to="4"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="item active">

                        <h3>Diamond</h3>
                            
                        <div class="col-lg-4 col-md-4 col-sm-4 centered-column">
                            <a rel="nofollow" href="http://www.intel.com/"><img alt="Intel_big_logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/intel-xlg.jpg" class="big-logo-company company-logo"></a>
                        </div>
                        
                    
                        <div class="col-lg-4 col-md-4 col-sm-4 centered-column">
                            <a rel="nofollow" href="https://intl.cloud.tencent.com/"><img alt="Tencent Cloud_big_logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/Tencent-vert-lg.jpg" class="big-logo-company company-logo"></a>
                        </div>

                    </div>
                    <div class="item">
                        <h3>Premier Sponsors</h3>

                        <div class="col-lg-2 col-md-2 col-sm-2 centered-column">
                            <a rel="nofollow" href="http://www.99cloud.net/"><img alt="99Cloud Inc._big_logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/99cloud-lg.jpg" class="big-logo-company company-logo"></a>
                        </div>
                        
                    
                        <div class="col-lg-2 col-md-2 col-sm-2 centered-column">
                            <a rel="nofollow" href="http://www.cisco.com/"><img alt="Cisco_big_logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/cisco-new-lg.png" class="big-logo-company company-logo"></a>
                        </div>

                    </div>
                    <div class="item">
                        <h3>Exhibitor Sponsors</h3>

                        <div class="col-lg-2 col-md-2 col-sm-2 centered-column">
                            <a rel="nofollow" href="http://www.cloudbase.it"><img alt="Cloudbase Solutions_big_logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/cloudbase-lg.jpg" class="big-logo-company company-logo"></a>
                        </div>
                        
                    
                        <div class="col-lg-2 col-md-2 col-sm-2 centered-column">
                            <a rel="nofollow" href="https://www.huayun.com/about"><img alt="Huayun_big_logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/huayun-lg.jpg" class="big-logo-company company-logo"></a>
                        </div>
                    </div>
                </div>
                <a class="carousel-control left" href="#sponsorsCarousel" data-slide="prev">
                    <span class="fa fa-chevron-left"></span>
                </a>
                <a class="carousel-control right" href="#sponsorsCarousel" data-slide="next">
                    <span class="fa fa-chevron-right"></span>
                </a>
            </div>
            <div class="col-sm-12 sponsor">
                <h4 class="title">Become a Summit Sponsor</h4>
                <p class="text">
                    Meet the open source users, IT decision makers and passionate developers, administrators and operators building the modern
                    infrastructure stack. Content and attendees span more than 30 open source communities including Ceph,
                    Docker, Kata Containers, Kubernetes, OpenStack, OPNFV, Zuul and more.
                </p>
                <p style="text-align: center;">
                    <a class="btn register-btn-lrg" href="/summit/shanghai-2019/sponsors/">Sponsor the Summit <i class="fa fa-arrow-right"></i></a>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="diverse">
    <div class="blue-div"></div>
    <div class="image"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 text">
                <span></span>
                <h2>Inclusive.<br>Diverse.<br>Open.</h2>
                <p>We are a diverse community of professionals, and the OpenStack Foundation is dedicated to providing an
                    inclusive and safe Summit experience for everyone. View the <a href="https://www.openstack.org/summit/shanghai-2019/code-of-conduct/" target="_blank">Summit Code of Conduct</a> for more information.</p>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="summit-insights">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">

                <div class="pink box item big">
                    <h4>Get buy in from your boss</h4>
                    <p>Justifying your trip to Shangai this November is easier when armed with the right information.</p>
                    <p>Get budget info and event details to help convince your boss that you need to attend the Summit.</p>
                    <p>To make it even easier, here’s a sample letter filled with info on who you can expect to meet, information on hands-on training opportunities, collaborative sessions, networking events and more that is bound to send you and your coworkers to the home of open infrastructure.
                    </p>

                    <a class="btn" href="/summit/shanghai-2019/sample-letter/">Sample Letter <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="video item">
                    <div class="embed-video"><iframe src="//www.youtube.com/embed/xZwqtldjJRQ?rel=0&showinfo=0" frameborder="0"
                                                     allowfullscreen></iframe>
                        <p class='video-catpion'>{$Caption}</p>
                    </div>
                    <div class="video-cover">

                        <h5>OpenStack at Progressive Insurance: Data Science and Machine Learning
                        </h5>
                        <h6>Vancouver 2018</h6>
                    </div>
                </div>

            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">

                <div class="video item">
                    <div class="embed-video">
                        <iframe src="//www.youtube.com/embed/gN5veI1nvKc?rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
                        <p class='video-catpion'>{$Caption}</p>
                    </div>
                    <div class="video-cover">
                        <h6>Vancouver 2018</h6>
                    </div>
                </div>
                <div class="box light item  spec">
                    <h4>Get the Summit Mobile App</h4>
                    <p>Download the official Summit app (iOS or Android) and build your personal Summit schedule.</p>

                    <a class="btn" href="https://www.openstack.org/mobile">Get the App<i class="fa fa-arrow-right"></i></a>
                </div>

                <div class="box light item  spec">
                    <h4>Get involved with the Forum</h4>
                    <p>Operators and developers attend the Forum to share feedback and collaboratively brainstorm requirements for the next release.</p>

                    <a class="btn" href="https://wiki.openstack.org/wiki/Forum">Get involved <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="box light item">
                    <h4>Shangai Travel</h4>
                    <p>Stay tuned for more info!
                    </p>
                </div>


                <div class="pink box item">
                    <h4>What to expect from your first Open Infrastructure Summit</h4>
                    <p>Community leaders share tips and need-to-knows for the Summit-curious.
                    </p>

                    <a class="btn" href="http://superuser.openstack.org/articles/first-open-infrastructure-summit-schedule-launch/" target="_blank">Read the article<i class="fa fa-arrow-right"></i></a>
                </div>

                <div class="box light item">
                    <h5 class="insight">“The overall quality of leads is spot on. The conversations were extremely valuable,
                        and attendees connected with our solution. The whole team is super happy with quality”</h5>

                    <div class="signature">
                        <span class="photo photo-bis"></span>
                        <div class="info">
                            <h5 class="name">Johan Christenson</h5>
                            <div class="company">City Networks</div>
                        </div>

                        <span class="brand  second"></span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>