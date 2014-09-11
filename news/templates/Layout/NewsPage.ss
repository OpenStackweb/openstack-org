<div class="newsSlider">
    <div class='case-studies-hero'>
        <div class='container'>
            <ul class='slider'>
                <% control SlideNews %>
                    <li data-label='$Pos' class="automotive">
                        <div class="sliderImage">
                            <a href="$Link"><img src="$Image" /></a>
                        </div>
                        <div class="sliderText">
                            <p class='largeQuote'>&ldquo;$Headline&rdquo;</p>
                            <p class='attribution'>&mdash; $Summary</p>
                        </div>
                        <div class="clear"></div>
                    </li>
                <% end_control %>
            </ul>
        </div>
    </div>
</div>
<div class="newsBody">
    <div class="newsFeatured">
        <h3>Featured</h3>
        <div class='case-studies-hero'>
            <div >
                <ul class='featured'>
                    <li>
                        <% control FeaturedNews %>
                            <div class="featuredBox">
                                <div class="featuredImage"><a href="$Link"><img src="$Image" /></a></div>
                                <div class="featuredHeadline"><a href="$Link">$Headline</a></div>
                                <div class="featuredDate">$formatDate</div>
                                <div class="featuredSummary">$Summary</div>
                            </div>
                            <% if Pos = 3 %>
                                <div class="clear"></div>
                                </li>
                                <li>
                            <% end_if %>
                        <% end_control %>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="newsRecent">
        <h3>Recent News</h3>
        <% control  RecentNews %>
            <div class="recentBox">
                <div class="recentHeadline"><a href="$Link">$Headline</a></div>
                <div class="recentDate">$formatDate</div>
                <div class="recentSummary">$Summary</div>
            </div>
        <% end_control %>
    </div>
</div>
<div class="newsSidebar">
    <div class="upcomingEvents">
        <div class="span-11">
        	<h2>Upcoming Events</h2>
        	<div class="eventBlock upcoming">
        			<% if FutureEvents(100) %>
        				<% control FutureEvents(100) %>
        						<div class="event<% if First %> top<% end_if %>">
        							<h3><a rel="nofollow" href="$EventLink" target="_blank">$Title</a></h3>
        							<p class="details">$formatDateRange - $EventLocation</p>
        							<p class="eventButton"><a rel="nofollow" href="$EventLink" target="_blank">$EventLinkLabel</a></p>
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
    </div>
    <div class="featuredEvents">
        <div class="feeds span-15">
            <h2>Featured Events</h2>
            <div id="openStackFeed">
                <% control RssItems %>
                        <div class="feedItem Web">
                        <div class="span-14 prepend-1 last">
                            <div class="itemContent">
                                <a href="{$link}">$title <span class="itemTimeStamp">$pubDate</span></a>
                            </div>
                        </div>
                    </div>
                <% end_control %>
            </div>
        </div>
    </div>
</div>
