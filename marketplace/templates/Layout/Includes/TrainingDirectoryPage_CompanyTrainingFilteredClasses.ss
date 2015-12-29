<% loop FilteredCourses %>
    <li>
        <div class="row">
            <div class="col-md-2">
                <div class="row image_frame">
                    <img alt='{$CompanyName}_logo' src='{$CompanyLogo}' class='company_logo'/>
                </div>
            </div>
            <div class="col-md-8">
                <h4><a href="$Top.Link{$BookMark}" class="outbound-link">$CourseName</a></h4>
                $CompanyName
                <p class="date-block">
                    <span class="month">$StartDateMonth</span>
                    <span class="day">$StartDateDay</span>
                    <% if $EndDateDay %>
                     to
                    <span class="month">$EndDateMonth</span>
                    <span class="day">$EndDateDay</span>
                    <% end_if %>
                    <% if $EndDateYear %>
                    -
                    $EndDateYear
                    <% end_if %>
                </p>
                $City, $Country <br>
                <b>$Level</b>
            </div>
            <% if $Link %>
            <div class="col-md-2">
                <a class="btn btn-primary" href="$Link" class="outbound-link">Register</a>
            </div>
            <% end_if %>
        </div>

    </li>
<% end_loop %>