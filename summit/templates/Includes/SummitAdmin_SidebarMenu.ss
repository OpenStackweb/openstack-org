<ul class="sidebar-nav nav-pills nav-stacked" id="menu">
    <li <% if $Active == 'dashboard' %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/dashboard"><span class="fa-stack fa-lg pull-left"><i class="fa fa-dashboard fa-stack-1x "></i></span>Dashboard</a>
    </li>
    <li <% if $Active == 'attendees' %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/attendees"><span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>Attendees</a>
    </li>
    <li <% if $Active == 'schedule' %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/events/schedule"><span class="fa-stack fa-lg pull-left"><i class="fa fa-calendar fa-stack-1x "></i></span>Schedule</a>
        <ul class="nav-pills nav-stacked" style="list-style-type:none;">
            <li <% if $Active == 'edit_event' %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/events">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>New Event
                </a>
            </li>
            <li <% if $Active == 'events_bulk' %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/events/bulk">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Bulk Actions
                </a>
            </li>
        </ul>
    </li>
    <li <% if $Active == 'speakers' %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/speakers"><span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>Speakers</a>
        <ul class="nav-pills nav-stacked" style="list-style-type:none;">
            <li <% if $Active == 'speakers_merge' %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/speakers/merge">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Merge
                </a>
            </li>
        </ul>
    </li>
    <li <% if $Active == 'reports' %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/reports"><span class="fa-stack fa-lg pull-left"><i class="fa fa-list fa-stack-1x "></i></span>Reports</a>
        <ul class="nav-pills nav-stacked" style="list-style-type:none;">
            <li <% if $Active == 'room_metrics' %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/room_metrics">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Room Metrics
                </a>
            </li>
        </ul>
    </li>
    <li <% if $Active == 'promocodes' %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/promocodes"><span class="fa-stack fa-lg pull-left"><i class="fa fa-ticket fa-stack-1x "></i></span>Promo Codes</a>
        <ul class="nav-pills nav-stacked" style="list-style-type:none;">
            <li <% if $Active == 'promocodes_sponsors' %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/promocodes/sponsors">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Sponsors
                </a>
            </li>
            <li <% if $Active == 'promocodes_bulk' %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/promocodes/bulk">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Bulk Actions
                </a>
            </li>
        </ul>
    </li>
</ul>