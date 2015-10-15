<!-- Projects Tabs -->
<div class="software-tab-wrapper">
    <div class="container">
        <ul class="nav nav-tabs project-tabs">
            <li <% if Active == 0 %>class="active"<% end_if %>><a href="$Top.Link">Overview</a></li>
            <li <% if Active == 1 %>class="active"<% end_if %>><a href="$Top.Link(all-projects)">All Projects</a></li>
            <% if $Top.HasAvailableSampleConfigTypes %>
            <li <% if Active == 3 %>class="active"<% end_if %>><a href="$Top.Link(sample-configs)">Sample Configurations</a></li>
            <% end_if %>
        </ul>
    </div>
</div>
<div class="software-tab-dropdown">
    <div class="dropdown">
        <button aria-expanded="true" aria-haspopup="true" data-toggle="dropdown" id="dropdownMenu1" type="button" class="dropdown-toggle projects-dropdown-btn">
            All Projects
            <i class="fa fa-caret-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li <% if Active == 0 %>class="active"<% end_if %>><a href="$Top.Link">Overview</a></li>
            <li <% if Active == 1 %>class="active"<% end_if %>><a href="$Top.Link(all-projects)">All Projects</a></li>
            <% if $Top.HasAvailableSampleConfigTypes %>
                <li <% if Active == 3 %>class="active"<% end_if %>><a href="$Top.Link(sample-configs)">Sample Configurations</a></li>
            <% end_if %>
        </ul>
    </div>
</div>