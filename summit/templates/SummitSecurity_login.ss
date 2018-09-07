<!DOCTYPE html>
<html lang="en">

<head>
    <% include Head %>
</head>

<body class="presentation-page">
<div class="main-body" style="width: auto;height: auto;position: initial;">
    <% include StaticSummitPageHeaderSmall %>

    <div id="wrap">
        <!-- Begin Page Content -->
        $Layout
        <!-- End Page Content -->
    </div>

    <% include StaticSummitPageFooter %>

    <!-- Hidden Sidebar Nav -->
    <div class="sidebar-nav">
        <nav>
            <a href="#" class="close-panel"><i class="icon-remove-sign icon-large"></i></a>
            <ul class="sidebar-menu">
                <!-- Microsite Navigation -->

                <% include SummitNav %>

                <!-- End Microsite Navigation -->
            </ul>

            <% if $CurrentSummit.RegistrationLink %>
                <a href="$CurrentSummit.RegistrationLink" class="btn register-btn-lrg">Register Now</a>
            <% end_if %>
        </nav>
    </div>

</div>
</body>

</html>
