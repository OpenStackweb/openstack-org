<div class="container jobPosting"  id="{$ID}">
    <div class="row">
        <% if RecentJob %>
            <p class="type"><span class="label">Type: </span>New!</p>
        <% else %>
            <p class="type"><span class="label">Type: </span></p>
        <% end_if %>
    </div>
    <div class="row">
        <div class="col-md-8">
            <ul class="details">
                <li class="title">
                    <span class="label">Job Title: </span>
                    <a rel="nofollow" target="_blank" href="/community/jobs/view/$ID/$Slug" class="jobTitle">$Title</a>
                </li>
                <li class="employer">
                    <span class="label">Employer: </span>at <strong>$CompanyName</strong>
                </li>
            </ul>
        </div>
        <div class="col-md-3 postDate">
            <p><span class="label">Date Posted: </span>$PostedDate.format(F) $PostedDate.format(d)</p>
        </div>
    </div>
    <% if FormattedLocation %>
        <div class="row">
            <div class="col-md-12">
                <ul class="location">
                    <li>
                        <span class="label">Location: </span>
                        $FormattedLocation
                    </li>
                </ul>
            </div>
        </div>
    <% end_if %>

    <a href="" class="jobExpand btn btn-default btn-xs">more</a>

    <div class="row jobDescription" >
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div style="max-width:1000px">
                        $Description
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div style="max-width: 1000px">
                        $Instructions2Apply
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-2">
                    <div class="moreInfo">
                        <span class="label">More information: </span>$FormattedMoreInfoLink.RAW
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="moreInfo">
                        <span class="label">Job page: </span>
                        <a rel="nofollow" target="_blank" href="/community/jobs/view/$ID/$Slug">Permalink to this job</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>