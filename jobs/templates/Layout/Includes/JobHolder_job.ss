<div class="jobPosting span-24 last" id="{$ID}">
    <div class="span-2">
        <% if RecentJob %>
            <p class="type"><span class="label">Type: </span>New!</p>
        <% else %>
            <p class="type"><span class="label">Type: </span></p>
        <% end_if %>
    </div>
    <div class="span-19 jobBlock">

        <ul class="details">
            <li class="title"><span class="label">Job Title: </span><a href="#" class="jobTitle">$Title</a></li>
            <li class="employer"><span class="label">Employer: </span>at <strong>$JobCompany</strong></li>
        </ul>
        <% if FormattedLocation %>
            <br>
            <ul class="location">
                <li>
                    <span class="label">Location: </span>
                    $FormattedLocation
                </li>
            </ul>
        <% end_if %>
    </div>
    <div class="span-3 last postDate">
        <p><span class="label">Date Posted: </span>$JobPostedDate.format(F) $JobPostedDate.format(d)</p>
    </div>

    <div class="span-22 last prepend-2 jobDescription">

        $Content
        $JobInstructions2Apply
        <div class="moreInfo">
            <span class="label">More information: </span><a rel="nofollow" href="$JobMoreInfoLink">More About This Job</a>
        </div>
    </div>

</div>