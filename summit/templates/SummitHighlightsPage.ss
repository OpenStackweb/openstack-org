<!DOCTYPE html>
<html lang="en">
    <head>
        <% include Head %>
        <% include Analytics %>
    </head>
    <body>
        <div class="main-body">
            $Layout
            <% include JS %>
            <% include Quantcast %>
            <% include Footer %>
            <!-- Hidden Sidebar Nav -->
            <div class="sidebar-nav">
                <nav>
                    <a class="close-panel" href="#"><i class="icon-remove-sign icon-large"></i></a>
                    <ul class="sidebar-menu">
                        <!-- Microsite Navigation -->
                        <ul class="nav nav-tabs">
                            <li class="current">
                                <a href="$Top.Link">Summit Highlights</a>
                            </li>
                            <li class="">
                                <a href="$Top.Link(videos)">Videos</a>
                            </li>
                            <li class="">
                                <a href="$Top.Link(sponsors)">Sponsors</a>
                            </li>
                        </ul>
                        <!-- End Microsite Navigation -->
                    </ul>
                </nav>
            </div>

        </div>
    </body>
</html>