<div class="container">
    <h1>Openstack Days</h1>
    <p>
    Free text area
    </p>
    <% with FeaturedEvent %>
        $Title
        $Picture
    <% end_with %>

    $getEvents(100,future_events,OpenStack Days)

</div>