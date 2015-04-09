<hr>
<div class="container">
    <div class="span-24">
        $Content
    </div>
</div>
<div class="course-filter grey-bar">
    <div class="container">
        <p class="back-label">
            <a href="$Top.Link">Back To Course List</a>
        </p>

    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>$Training.Name</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9 col-md-9 col-sm-9 training-list">
            <% if Courses %>
                <% loop Courses %>
                    <% include TrainingDirectoryPage_Company_CourseBox CompanyColor=$Top.Company.CompanyColor %>
                    <hr>
                <% end_loop %>
            <% end_if %>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-3 marketplace-training-sidebar">
            $Company.MediumLogoPreview
            <p>$Training.RAW_val(Description)</p>
            <div class="span-7 last training-rating">
               <% include MarketPlaceReviews %>
            </div>
        </div>
    </div>
</div>