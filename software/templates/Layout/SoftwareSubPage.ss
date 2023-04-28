<div class="software-announcement-banner">
<% include SoftwareHomePage_MainNavMenu Active='overview' %>
</div>
<div class="software-main-wrapper">
    <!-- Projects Subnav -->
    <% include SoftwareHomePage_SubNavMenu Active=$Top.getActive %>
    <div class="container inner-software">
        $Content
    </div>
</div>