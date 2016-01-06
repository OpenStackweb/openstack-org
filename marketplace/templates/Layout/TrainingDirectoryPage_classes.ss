<div class="grey-bar">
    <div class="container">
        <div class="row">
            <div class="col-md-12"> Filter Classes </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                $CompanyCombo()
                $LocationCombo()
                $LevelCombo
            </div>
        </div>
        <div class="date_filter_div">
            <div class="row">
                <div class="col-md-12"> Start Date Filter </div>
            </div>
            <div class="row">
                <div class="col-md-12"><input id="from_date_filter" /> To <input id="to_date_filter" /> </div>
            </div>
        </div>

    </div>
</div>
<div class="container">
    <div class="row">
        <h2> Classes </h2>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <ul class="training-updates" id="training-list">
                <% loop AllClasses.Limit(40) %>
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
                                <% if $EndDateDay !=  $StartDateDay %>
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
            </ul>
        </div>
    </div>
    <nav>
        <ul class="pagination"></ul>
    </nav>
    <script type="text/javascript">
        var class_count = {$AllClasses.Count};
        var page_count = Math.ceil(class_count/40);
        var current_page = 1;
    </script>
</div>