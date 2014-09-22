<div class="postArticle">
    <a href="/news-add">Post A News Article</a>
</div>
<% if CurrentMember %>
    <% if CurrentMember.isNewsManager %>
        <div class="manageNews">
            <a href="/news-manage">Manage News</a>
        </div>
    <% end_if %>
<% end_if %>
<div class="clear"></div>
<div class="newsSlider">
    <ul class='slider'>
        <% control SlideNews %>
            <li data-label='$Pos' class="automotive">
                <a href="news/ViewArticle?articleID=$ID">
                    <div class="sliderImage">
                        <% if Image.Exists %>
                            $Image.CroppedImage(300,200) <p>&nbsp</p>
                        <% else %>
                            <img src="/themes/openstack/images/generic-profile-photo.png"><p>&nbsp;</p>
                        <% end_if %>
                    </div>
                    <div class="sliderText">
                        <p class='largeQuote'>&ldquo;$Headline&rdquo;</p>
                        <p class='attribution'>&mdash; $Summary</p>
                    </div>
                    <div class="clear"></div>
                </a>
            </li>
        <% end_control %>
    </ul>
</div>
<div class="newsBody">
    <div class="newsFeatured">
        <ul class='featured'>
            <li>
                <% control FeaturedNews %>
                    <div class="featuredBox">
                        <div class="featuredImage">
                            <a href="news/ViewArticle?articleID=$ID">
                                <% if Image.Exists %>
                                    $Image.CroppedImage(300,200) <p>&nbsp</p>
                                <% else %>
                                    <img src="/themes/openstack/images/generic-profile-photo.png"><p>&nbsp;</p>
                                <% end_if %>
                            </a>
                        </div>
                        <div class="featuredHeadline"><a href="news/ViewArticle?articleID=$ID">$Headline</a></div>
                        <div class="featuredDate">$formatDate</div>
                        <div class="featuredSummary">$Summary</div>
                    </div>
                    <% if Pos == 3 %>
                        <div class="clear"></div>
                        </li>
                        <li>
                    <% end_if %>
                <% end_control %>
            </li>
        </ul>
    </div>
    <div class="newsRecent">
        <h2>Recent News</h2>
        <% control  RecentNews %>
            <div class="recentBox">
                <div class="recentHeadline"><a href="news/ViewArticle?articleID=$ID">$Headline</a> | <span class="itemTimeStamp">$formatDate</span></div>
                <div class="recentSummary">$Summary</div>
            </div>
        <% end_control %>
    </div>
</div>
<div class="newsSidebar">
    <div class="upcomingEvents">
        <h2>Upcoming Events</h2>
        <div class="eventBlock upcoming">
                <% if FutureEvents(100) %>
                    <% control FutureEvents(100) %>
                            <div class="event<% if First %> top<% end_if %>">
                                <h3><a rel="nofollow" href="$EventLink" target="_blank">$Title</a></h3>
                                <div class="details">$formatDateRange - $EventLocation</div>
                                <span class="eventButton"><a rel="nofollow" href="$EventLink" target="_blank">$EventLinkLabel</a></span>
                            </div>
                    <% end_control %>
                <% else %>
                    <div class="event top">
                        <h3>Sorry, there are no upcoming events listed at the moment.</h3>
                        <p class="details">Wow! It really rare that we don't have any upcoming events on display. Somewhere in the world there's sure to be an OpenStack event in the near future&mdash;We probably just need to update this list. Please check back soon for more details.</p>
                    </div>
                <% end_if %>
            <div class="clear"></div>
        </div>
    </div>
    <div class="featuredEvents">
        <h2>Featured Events</h2>
        <div id="openStackFeed">
            <% control RssItems %>
                <div class="feedItem">
                    <a href="{$link}">$title <span class="itemTimeStamp">$pubDate</span></a>
                </div>
            <% end_control %>
        </div>
    </div>
</div>
<div class="clear"></div>