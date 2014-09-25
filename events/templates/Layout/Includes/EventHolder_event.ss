<% if IsEmpty %>
    <div class="event top">
        <h3>Sorry, there are no upcoming events listed at the moment.</h3>
        <p class="details">Wow! It really rare that we don't have any upcoming events on display. Somewhere in the world there's sure to be an OpenStack event in the near future&mdash;We probably just need to update this list. Please check back soon for more details.</p>
    </div>
<% else %>
    <div class="event<% if IsFirst %> top<% end_if %>">
        <h3><a rel="nofollow" href="$EventLink" target="_blank">$Title</a></h3>
        <p class="details">$formatDateRange - $EventLocation</p>
        <p class="eventButton"><a rel="nofollow" href="$EventLink" target="_blank">$EventLinkLabel</a></p>
    </div>
<% end_if %>
