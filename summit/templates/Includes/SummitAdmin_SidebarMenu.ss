<ul class="sidebar-nav nav-pills nav-stacked" id="menu">
    <li <% if $Active == 1 %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/dashboard"><span class="fa-stack fa-lg pull-left"><i class="fa fa-dashboard fa-stack-1x "></i></span>Dashboard</a>
    </li>
    <li <% if $Active == 2 %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/attendees"><span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>Attendees</a>
    </li>
    <li <% if $Active == 3 %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/events/schedule"><span class="fa-stack fa-lg pull-left"><i class="fa fa-calendar fa-stack-1x "></i></span>Schedule</a>
        <ul class="nav-pills nav-stacked" style="list-style-type:none;">
            <li <% if $Active == 4 %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/events">
                    <span class="fa-stack fa-lg pull-left"><i class="fa fa-list-alt fa-stack-1x "></i></span>New Event
                </a>
            </li>
        </ul>
    </li>
    <li <% if $Active == 5 %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/reports"><span class="fa-stack fa-lg pull-left"><i class="fa fa-list fa-stack-1x "></i></span>Reports</a>
    </li>
    <!--
    <li>
        <a href="$AdminLink/$SummitID/tickets"> <span class="fa-stack fa-lg pull-left"><i class="fa fa-ticket fa-stack-1x "></i></span>Tickets</a>
    </li>

    <li>
        <a href="#"><span class="fa-stack fa-lg pull-left"><i class="fa fa-wrench fa-stack-1x "></i></span>Settings</a>
        <ul class="nav-pills nav-stacked" style="list-style-type:none;">
            <li><a href="#"><span class="fa-stack fa-lg pull-left"><i class="fa fa-list-alt fa-stack-1x "></i></span>Company Banners</a></li>
        </ul>
    </li>
    -->
</ul>