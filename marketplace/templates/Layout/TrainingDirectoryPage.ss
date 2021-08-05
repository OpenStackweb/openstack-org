<script>
    var AutoCompleteUrls = {
        TopicSearchUrl:    "training/topics",
        LocationSearchUrl: "training/locations",
        LevelSearchUrl:    "training/levels"
    };
    var Results = {
        SearchUrl: "training/search"
    };

</script>
<div class="grey-bar">
    <div class="container">
        <p class="filter-label">Filter Courses</p>
        $CompanyCombo(0)
        $LocationCombo
        $LevelCombo
    </div>
</div>
<div class='container'>
    <div id="training-list" class="col-sm-8">
        <% if Trainings() %>
            <% loop Trainings() %>
                <% include TrainingDirectoryPage_CompanyTraining TrainingLink=$Top.Link %>
            <% end_loop %>
        <% end_if %>
    </div>
    <div class="col-sm-4">
        <% include MarketPlaceHelpLinks %>
        <% if UpcomingCourses %>
            <h3>
                Upcoming Classes <a class="show_all_classes" href="$Top.Link(classes)"> Show All </a>
            </h3>
            <ul class="training-updates">
                <% loop UpcomingCourses %>
                <li>
                    <p class="date-block">
                        <span class="month">$StartDateMonth</span>
                        <span class="day">$StartDateDay</span>
                    </p>
                    <p>
                        <a href="{$Top.Link}{$BookMark}" class="outbound-link">$CourseName</a><br>
                        $City
                    </p>
                </li>
                <% end_loop %>
            </ul>
        <% end_if %>
        <div class="add-your-course">
            <p>
                Does your company offer products or services that belong in the marketplace? <a href="mailto:ecosystem@openstack.org">Email us for details</a> or <a href="https://calendly.com/jimmy-mcarthur">put some time on our calendar</a> to meet remotely.
            </p>
        </div>
    </div>
</div>

