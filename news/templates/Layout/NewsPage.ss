</div>
<div class="grey-bar news">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <% if CurrentMember %>
                    <% if CurrentMember.isNewsManager %>
                        <a class="manage-news-link" href="/news-manage"><i class="fa fa-cog"></i> Manage News</a>
                    <% end_if %>
                <% end_if %>
                <a href="/news-add"><i class="fa fa-plus-circle"></i> Post A News Article</a>
                <a class="review-news-link" href="/marketing/make-some-news-with-openstack/news-page-editorial-guidelines/"><i class="fa fa-book"></i> Review the Editorial Guidelines</a>
                <a class="marketing-link" href="/marketing/"><i class="fa fa-cubes"></i> Marketing Portal</a>
            </div>
        </div>
    </div>
</div>

<div id="news-slider" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->

    <% if $SlideNewsCount > 1 %>
    <ol class="carousel-indicators">
        <% loop SlideNews %>
            <li data-target="#news-slider"  <% if First %>class="active" <% end_if %> data-slide-to="$Pos(0)" ></li>
        <% end_loop %>

    </ol>
    <% end_if %>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <% loop SlideNews %>
            <div class="item <% if First %>active<% end_if %>">
                <% if Image.Exists %>
                    $Image.SetWidth(1100)
                <% end_if %>
                <div class="carousel-caption">
                    <h3 class='largeHeadline'>$Headline</h3>
                    <div class='sliderSummary'>$HTMLSummary</div>
                    <a class="more-btn" href="news/view/$ID/$HeadlineForUrl">Read More <i class="fa fa-chevron-circle-right"></i></a>
                </div>
            </div>
        <% end_loop %>
    </div>

    <% if $SlideNewsCount > 1 %>
    <!-- Controls -->
    <a class="left carousel-control" href="#news-slider" role="button" data-slide="prev">
        <i class="fa fa-chevron-left"></i>
    </a>
    <a class="right carousel-control" href="#news-slider" role="button" data-slide="next">
        <i class="fa fa-chevron-right"></i>
    </a>
    <% end_if %>
</div>


<div class="container news-container">
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h2>OpenStack Community News</h2>
            <% loop RecentNews %>
                <div class="recentBox">
                    $ImageThumb
                    <div class="recent_text">
                        <div class="recentHeadline">
                            <a href="news/view/$ID/$HeadlineForUrl">$RAW_val(Headline)</a> <span class="itemTimeStamp">$formatDate</span>
                        </div>
                        <div class="recentSummary">$HTMLSummary</div>
                    </div>
                    <div style="clear:both"></div>
                </div>
            <% end_loop %>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <div class="news-sidebar">
                <h3>Subscribe to our News Feed</h3>
                <div class="sendgrid-subscription-widget" data-token="tKWLGalJp7SuomZ06awtOEUDVCRyJIzHw6y5mNMWa0Qp7GmM2v7jRcBRkxGCR0oI1XxQh1qSfv1CFfzbMoIFHg%3D%3D"></div>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://s3.amazonaws.com/subscription-cdn/0.2/widget.min.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "sendgrid-subscription-widget-js");</script>
                <hr>
                <h3>OpenStack in the News</h3>
                <div class="featured-links">
                    <a href="http://superuser.openstack.org">SuperUser</a> | <a href="news/archived">Archived News</a>
                </div>
                <ul class="featured">
                    <% loop FeaturedNews %>
                            <li>
                                <div class="featuredImage">
                                    <a href="news/view/$ID/$HeadlineForUrl">
                                        <div class="featuredDate">$formatDate</div>
                                        <div class="featuredHeadline">
                                            $Headline
                                            <div class="more">Read More <i class="fa fa-chevron-circle-right"></i></div>
                                        </div>
                                        <% if Image.Exists %>
                                            $Image.SetWidth(360)
                                        <% end_if %>
                                    </a>
                                </div>
                                <div class="featuredSummary">$HTMLSummary</div>
                            </li>
                    <% end_loop %>
                </ul>
                <hr>
                <div class="upcomingEvents">
                    <h3>
                        Upcoming Events
                        <div class="see-all-events">
                            <a href="/events">All Events <i class="fa fa-angle-right"></i></a>
                        </div>
                    </h3>
                    <div class="eventBlock upcoming">
                        <% if FutureEvents(22) %>
                            <% loop FutureEvents(22) %>
                                <div class="event <% if First %> top<% end_if %>">
                                    <a rel="nofollow" href="$EventLink" target="_blank">$Title</a>
                                    <div class="details">$formatDateRange - $EventLocation</div>
                                    <span class="eventButton"><a rel="nofollow" href="$EventLink" target="_blank">Details</a></span>
                                </div>
                            <% end_loop %>
                        <% else %>
                            <div class="event top">
                                <h3>Sorry, there are no upcoming events listed at the moment.</h3>
                                <p class="details">Wow! It really rare that we don't have any upcoming events on display. Somewhere in the world there's sure to be an OpenStack event in the near future&mdash;We probably just need to update this list. Please check back soon for more details.</p>
                            </div>
                        <% end_if %>
                    </div>
                </div>
                <hr>
                <h3>Media Contacts</h3>
                <p><strong>Robert Cathey</strong><br/>
                <a href="http://www.cathey.co/">Cathey.co</a> for the OpenStack Foundation<br/>
                +1 865-386-6118<br/>
                <a href="mailto:robert@cathey.co">robert@cathey.co</a>
                <a href="http://twitter.com/robertcathey">@robertcathey</a>
                </p>
        </div>
    </div>
</div>
<!-- End Page Content -->
