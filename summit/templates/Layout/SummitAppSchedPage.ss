<div class="container">
    <h1 class="schedule_title">Schedule</h1>
    <hr>
    <% loop $Summit.getTypes() %>
        <button type="button" data-summit_type_id="$ID" class="btn btn-primary summit_type_filter active checked" data-toggle="button">
            <span class="glyphicon glyphicon-check"></span> $Title
        </button>
    <% end_loop %>
    <select class="select summit_event_type_filter">
        <option value="-1">All Events</option>
        <% loop $Summit.getEventTypes() %>
            <option value="$ID">$Type</option>
        <% end_loop %>
    </select>
    <% if $Summit.isAttendee() %>
        <button type="button" class="btn btn-info pull-right switch_schedule public">
            Switch to My Schedule
        </button>
    <% end_if %>
    <hr>
    <input id="summit_id" type="hidden" value="$Summit.ID" />
    <div id="schedule_container">

    </div>
    <div id="schedule_sidebar"></div>
</div>
<div id="fb-root"></div>
