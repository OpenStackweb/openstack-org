<div class="container">
    <div class="col-sm-12">
        $Content
    </div>
</div>
<div class="course-filter grey-bar">
    <div class="container">
        <div class="back-label">
            <a href="$Top.Link">Back To Course List</a>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>$Training.Name</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 training-list">
            <% if Courses %>
                <% loop Courses %>
                    <% include TrainingDirectoryPage_Company_CourseBox CompanyColor=$Top.Company.CompanyColor %>
                    <hr>
                <% end_loop %>
            <% end_if %>
        </div>

        <div class="col-sm-4 marketplace-training-sidebar">
            $Company.MediumLogoPreview
            <% if $Company.isCOAPartner %>
                <img class="coa-partner-badge" src="/themes/openstack/images/coa/coa-badge.jpg" title="COA Training Partner" alt="COA Training Partner">
            <% end_if %>
            <p>$Training.RAW_val(Description)</p>
            <div class="span-7 last training-rating">
               <% include MarketPlaceReviews %>
            </div>
        </div>
    </div>
</div>