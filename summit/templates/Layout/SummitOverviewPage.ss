<div class="white about-summit">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-8">
                <h2 style="text-align:center;">The Must-Attend OpenStack Event</h2>
                <hr/>
                $OverviewIntro
                <div class="atendees-charts-wrapper">
                    <h3 class="recap-title">Attendees From Around The World</h3>
                    <div class="atendees-charts">
                        <% if Atendees1ChartImageUrl %>
                            <div class="single-summit-stats right">
                                <h4>$Atendees1Label</h4>
                                <img class="atendees-chart" src="{$Atendees1ChartImageUrl}"
                                onerror="this.onerror=null; this.src={$Atendees1ChartImagePngUrl}" alt="OpenStack Summit Atendees">
                            </div>
                        <% end_if %>
                        <% if Atendees2ChartImageUrl %>
                            <div class="single-summit-stats right">
                                <h4>$Atendees2Label</h4>
                                <img class="atendees-chart" src="{$Atendees2ChartImageUrl}"
                                onerror="this.onerror=null; this.src={$Atendees2ChartImagePngUrl}" alt="OpenStack Summit Atendees">
                            </div>
                        <% end_if %>
                        <% if Atendees3ChartImageUrl %>
                            <div class="single-summit-stats right">
                                <h4>$Atendees3Label</h4>
                                <img class="atendees-chart" src="{$Atendees3ChartImageUrl}"
                                 onerror="this.onerror=null; this.src={$Atendees3ChartImagePngUrl}" alt="OpenStack Summit Atendees">
                            </div>
                        <% end_if %>
                        <% if Atendees4ChartImageUrl %>
                            <div class="single-summit-stats right">
                                <h4>$Atendees4Label</h4>
                                <img class="atendees-chart" src="{$Atendees4ChartImageUrl}"
                                onerror="this.onerror=null; this.src={$Atendees4ChartImagePngUrl}" alt="OpenStack Summit Atendees">
                            </div>
                        <% end_if %>
                    </div>
                    <div class="atendees-chart-ref-wrapper">
                        <img class="atendees-chart-ref" src="{$AtendeesChartRefImageUrl}"
                        onerror="this.onerror=null; this.src={$AtendeesChartRefImagePngUrl}" alt="OpenStack Summit Atendees Ref">
                        <a href="/sponsors/#audience">See Full Stats from Vancouver &amp; Paris</a>
                    </div>
                </div>
                <hr>
                <h3 class="recap-title">$RecapTitle</h3>
                <div class="about-video-wrapper">
                    <iframe width="360" height="225" src="//www.youtube.com/embed/{$VideoRecapYouTubeID1}?showinfo=0" frameborder="0" allowfullscreen></iframe>
                    <p class='video-caption'>$VideoRecapCaption1</p>
                </div>
                <div class="about-video-wrapper">
                    <iframe width="360" height="225" src="//www.youtube.com/embed/{$VideoRecapYouTubeID2}?showinfo=0" frameborder="0" allowfullscreen></iframe>
                    <p class='video-caption'>$VideoRecapCaption2</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-4">
                <ul class="help-me-menu">
                    <% if Summit.RegistrationLink %>
                        <li><a href="{$Summit.RegistrationLink}" target="_blank"><i class="fa fa-shopping-cart"></i>Register for the Summit</a></li>
                    <% end_if %>

                    <% if OrderedHelpMenuItems %>
                        <% loop OrderedHelpMenuItems %>
                            <li><a href="{$Url}"><i class="fa {$FAIcon}"></i>$Label</a></li>
                        <% end_loop %>
                    <% else %>
                        <li><a href="{$Link}download-the-summit-app/"><i class="fa fa-mobile" style="font-size:2em;"></i>Download The App</a></li>
                        <hr>
                        <li><a href="{$Link}open-cloud-ecosystem/"><i class="fa fa-users"></i>Open Cloud EcoSystem</a></li>
                        <li><a href="{$Link}faq/"><i class="fa fa-question"></i>Frequently Asked Questions</a></li>
                    <% end_if %>

                </ul>
                <div class="schedule-square">
                    <h3><i class="fa fa-calendar"></i>$ScheduleTitle</h3>
                    $ScheduleText
                    <a href="$ScheduleUrl" class="btn outline-btn">$ScheduleBtnText</a>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="light two-summits">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>$TwoMainEventsTitle</h1>
            </div>
        </div>
        <div class="row summit-types">
            <div class="col-lg-5 col-md-5 col-sm-6">
                <img class="summit-type-icon" src="{$EventOneLogoUrl}"
                     onerror="this.onerror=null; this.src={$EventOneLogoPngUrl}"
                     alt="{$EventOneTitle}" style="max-width: 150px;">
                <h3>$EventOneTitle</h3>
                <div class="sub-h3">
                    $EventOneSubTitle
                </div>
                $EventOneContent
            </div>
            <div class="col-lg-5 col-lg-push-2 col-md-5 col-md-push-2 col-sm-6">
                <img class="summit-type-icon" src="{$EventTwoLogoUrl}"
                     onerror="this.onerror=null; this.src={$EventTwoLogoPngUrl}" alt="{$EventTwoTitle}"
                     style="max-width: 150px;">
                <h3>$EventTwoTitle</h3>
                <div class="sub-h3">
                    $EventTwoSubTitle
                </div>
                $EventTwoContent
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8 col-sm-push-2">
                <div class="about-timeline-wrapper">
                    <h3>Summit Timeline</h3>
                    <p>$TimelineCaption</p>
                    <img class="summit-type-icon" src="{$TimelineImageUrl}" onerror="this.onerror=null; this.src={$TimelineImagePngUrl}" alt="Summit Timeline">
                </div>
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

<% if $GrowthBoxTextTop || $GrowthBoxTextBottom || $BoxChartLegendImageUrl || $BoxChartLegendImagePngUrl %>

<div class="growth"
     style="background: rgba(0, 0, 0, 0) url('{$BoxChartBackgroundImageUrl}') no-repeat scroll center center / cover;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="growth-text-top map">
                    $GrowthBoxTextTop
                </div>
                <div class="growth-map-wrapper">
                    <img class="growth-map tokyo" src="{$BoxChartImageUrl}"
                         onerror="this.onerror=null; this.src={$BoxChartImagePngUrl}"
                         alt="OpenStack Summit Growth Chart">

                    <img class="growth-chart-legend map" src="{$BoxChartLegendImageUrl}"
                         onerror="this.onerror=null; this.src={$BoxChartLegendImagePngUrl}"
                         alt="OpenStack Summit Growth Legend">
                </div>
                <div class="growth-text-bottom map">
                    $GrowthBoxTextBottom
                    <% if Summit.RegistrationLink %>
                        <p>
                            <a href="{$Summit.RegistrationLink}" target="_blank">Register Now</a>
                        </p>
                    <% end_if %>
                </div>
            </div>
        </div>
    </div>
</div>

<% end_if %>

<div class="blue schedule-row">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>$ScheduleTitle</h1>
                $ScheduleText
                <p>
                    <a href="{$ScheduleUrl}" class="btn outline-btn">$ScheduleBtnText</a>
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
                        <a href="{$Summit.RegistrationLink}" target="_blank">Register Now</a>
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