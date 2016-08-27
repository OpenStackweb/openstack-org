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
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>New Event
                </a>
            </li>
            <li <% if $Active == 11 %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/events/bulk">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Bulk Actions
                </a>
            </li>
        </ul>
    </li>
    <li <% if $Active == 6 %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/speakers"><span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>Speakers</a>
        <ul class="nav-pills nav-stacked" style="list-style-type:none;">
            <li <% if $Active == 10 %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/speakers/merge">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Merge
                </a>
            </li>
        </ul>
    </li>
    <li <% if $Active == 5 %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/reports"><span class="fa-stack fa-lg pull-left"><i class="fa fa-list fa-stack-1x "></i></span>Reports</a>
    </li>
    <li <% if $Active == 7 %> class="active" <% end_if %>>
        <a href="$AdminLink/$SummitID/promocodes"><span class="fa-stack fa-lg pull-left"><i class="fa fa-ticket fa-stack-1x "></i></span>Promo Codes</a>
        <ul class="nav-pills nav-stacked" style="list-style-type:none;">
            <li <% if $Active == 8 %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/promocodes/sponsors">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Sponsors
                </a>
            </li>
            <li <% if $Active == 9 %> class="active" <% end_if %>>
                <a href="$AdminLink/$SummitID/promocodes/bulk">
                    <span class="fa-stack pull-left"><i class="fa fa-chevron-right fa-stack-1x" style="padding-top: 5px;"></i></span>Bulk Actions
                </a>
            </li>
        </ul>
    </li>
</ul>