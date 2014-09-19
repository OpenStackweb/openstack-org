<hr>
<div class="container">
    <div class="span-24">
        $Content
    </div>
</div>
<div class="course-filter">
    <div class="container">
        <p class="back-label">
            <a href="$Top.Link">Back To Course List</a>
        </p>
        <h1>$Training.Name</h1>
    </div>
</div>
<div class="container">
<div class="span-7 about-area colborder">
    $Company.MediumLogoPreview
    <p>$Training.RAW_val(Description)</p>
    <div class="span-7 last training-rating">
       <% loop Training %>
           <% include MarketPlaceDirectoryPage_Rating %>
        <% end_loop %>
    </div>
</div>
<div class="span-16 last course-listing">
    <% if Courses %>
        <% loop Courses %>
            <% include TrainingDirectoryPage_Company_CourseBox %>
            <hr>
        <% end_loop %>
    <% end_if %>
</div>
</div>