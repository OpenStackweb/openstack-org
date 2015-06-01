<div class="white about-summit">
    <div class="container">
        <h1>The Must-Attend OpenStack Event</h1>
        <hr/>

        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-8">
                <p><strong>The OpenStack Summit</strong> is a five-day conference for developers, users, and
                    administrators of OpenStack Cloud Software. It’s a great place to get started with OpenStack.
                </p>

                <div class="about-video-wrapper">
                    <iframe width="560" height="315" src="//www.youtube.com/embed/{$VideoRecapYouTubeID}?showinfo=0" frameborder="0" allowfullscreen></iframe>
                    <p class='video-caption'>$VideoRecapCaption</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-4">
                <ul class="help-me-menu">
                    <% if Summit.RegistrationLink %>
                        <li>
                            <a href="{$Summit.RegistrationLink}"><i class="fa fa-shopping-cart"></i>Register for the Summit</a>
                        </li>
                    <% end_if %>
                    <li><a href="{$Link}download-the-summit-app/"><i class="fa fa-mobile" style="font-size:2em;"></i>Download
                        The App</a>
                    </li>
                    <hr>
                    <li><a href="{$Link}open-cloud-ecosystem/"><i class="fa fa-users"></i>Open Cloud EcoSystem</a>
                    </li>
                    <li><a href="{$Link}faq/"><i class="fa fa-question"></i>Frequently Asked Questions</a>
                    </li>
                </ul>
                <div class="schedule-square">
                    <h3><i class="fa fa-calendar"></i>$ScheduleTitle</h3>
                    $ScheduleText
                    <a href="$ScheduleUrl" class="btn outline-btn">View The Schedule</a>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="light two-summits">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>One Week, Two Main Events</h1>
            </div>
        </div>
        <div class="row summit-types">
            <div class="col-lg-5 col-md-5 col-sm-6">
                <img class="summit-type-icon" src="/summit/images//grey-conference-logo.svg"
                     onerror="this.onerror=null; this.src=/images/grey-conference-logo.png"
                     alt="The OpenStack Conference" style="max-width: 150px;">

                <h3>The OpenStack Conference</h3>

                <div class="sub-h3">
                    For Everyone
                </div>
                <p><strong>Held Monday - Thursday</strong><br/>
                    Classic track with speakers and sessions. The perfect place for developers, users, and
                    administrators of OpenStack Cloud Software. This is great for those looking for the best way to get
                    started.
                </p>

            </div>
            <div class="col-lg-5 col-lg-push-2 col-md-5 col-md-push-2 col-sm-6">
                <img class="summit-type-icon" src="/summit/images//grey-summit-logo.svg"
                     onerror="this.onerror=null; this.src=/images/grey-summit-logo.png" alt="The OpenStack Conference"
                     style="max-width: 150px;">

                <h3>The OpenStack Design Summit</h3>

                <div class="sub-h3">
                    For Contributors
                </div>
                <p><strong>Held Tuesday - Friday</strong><br/>
                    Collaborative working sessions where OpenStack developers come together twice annually to discuss
                    the requirements for the next software release and connect with other community members.
                </p>

            </div>
        </div>
    </div>
</div>
<div class="white testimonial-row">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <img class="testimonial-author-img" src="/summit/images/wjossey.jpg">
                <blockquote>
                    OpenStack is clearly the winner, and the reason for that is what you see in this room today. It’s
                    all of you, it’s all of the community that have come together around this platform; it's people like
                    CERN and Expedia pushing upstream to this platform.
                </blockquote>
                <div class="testimonial-attribute">
                    <img src="/summit/images/tapjoy-logo.jpg">

                    <div class="testimonial-name">Wes Jossey</div>
                    <div class="testimonial-title">Head of Operations for Tapjoy</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="growth"
     style="background: rgba(0, 0, 0, 0) url('{$BoxChartBackgroundImageUrl}') no-repeat scroll center center / cover;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="growth-text-top">
                    $GrowthBoxTextTop
                    <img class="growth-chart-legend" src="{$BoxChartLegendImageUrl}"
                         onerror="this.onerror=null; this.src={$BoxChartLegendImagePngUrl}"
                         alt="OpenStack Summit Growth Legend">
                </div>
                <div class="growth-chart">
                    <img class="growth-chart" src="{$BoxChartImageUrl}"
                         onerror="this.onerror=null; this.src={$BoxChartImagePngUrl}"
                         alt="OpenStack Summit Growth Chart">
                </div>
                <div class="growth-text-bottom">
                    $GrowthBoxTextBottom
                </div>
            </div>
        </div>
    </div>
</div>
<div class="blue schedule-row">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>Schedule</h1>

                <p>
                    Get a glimpse into the wealth of speakers, topics and sessions happening at OpenStack Summit
                    Vancouver.
                </p>

                <p>
                    <a href="{$ScheduleUrl}" class="btn outline-btn">View The Schedule</a>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="white networking-row">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
               $NetworkingContent
                <% if Summit.RegistrationLink %>
                    <p>
                        <a href="{$Summit.RegistrationLink}">Register Now</a>
                    </p>
                <% end_if %>
            </div>
        </div>
    </div>
</div>
<!-- Photo Row -->

<div class="photo-row-wrapper">
    <div class="photo-container">
        <% if NetworkingPhotos %>
            <% loop NetworkingPhotos %>
                <img src="{$Url}"/>
            <% end_loop %>
        <% else %>
        <img src="/summit/images/networking-photos/photo1.jpg"/>
        <img src="/summit/images/networking-photos/photo2.jpg"/>
        <img src="/summit/images/networking-photos/photo3.jpg"/>
        <img src="/summit/images/networking-photos/photo4.jpg"/>
        <img src="/summit/images/networking-photos/photo5.jpg"/>
        <img src="/summit/images/networking-photos/photo6.jpg"/>
        <img src="/summit/images/networking-photos/photo7.jpg"/>
        <img src="/summit/images/networking-photos/photo8.jpg"/>
        <img src="/summit/images/networking-photos/photo9.jpg"/>
        <img src="/summit/images/networking-photos/photo10.jpg"/>
        <img src="/summit/images/networking-photos/photo11.jpg"/>
        <img src="/summit/images/networking-photos/photo12.jpg"/>
        <% end_if %>
    </div>
</div>
<!-- End Photo Row -->
$GATrackingCode