<div class="row">
    <div class="jumbotron">
        <div class="jobHeader">
            <div class="row">
                <div class="col-sm-6" id="OSJB">
                    <h1>OpenStack Job Board</h1>
                </div>
                <div class="col-sm-3 col-sm-offset-3 backButton">
                    <a href="$JobListLink">
                        <div class="button1" type="button">                            
                            <div class="glyphicon glyphicon-arrow-left"></div>
                            <h5>Back to Job List</h5>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<% with Job %>
<div class="container jobPosting" id="{$ID}">
    <div class="row">
        <div class="col-sm-12 col-xs-12 date-container">
            <% if RecentJob %>
                <span class="label label-danger">NEW</span>
            <% end_if %>
            <h5 class="publishedDate">Published on $PostedDate.format(F) $PostedDate.format(d)</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-10 col-xs-8 col-lg-10">
            <div class="detail_title">
                <a rel="nofollow" target="_blank" href="/community/jobs/view/$ID/$Slug" class="jobTitle job_title_hold">$Title</a>
            </div>
        </div>
        <div class="col-sm-2 col-xs-4 col-lg-2 img-container">
            <% if hasCompanyMemberLevel %>
                <img class="company_member_level $CompanyMemberLevel" id="company_member_level$CompanyID">
            <% end_if %>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 col-sm-10 col-xs-8">
            <div class="jobBasics-2">
                    <div class="glyphicon glyphicon-briefcase"></div>
                        $CompanyName
                    <% if IsCOANeeded %>
                        &nbsp;
                        <div class="glyphicon glyphicon-ok"></div>
                        COA Required
                    <% end_if %>
           </div>
        </div>
    </div>
    <div class="row jobDescription">
        <div class="col-md-11">
            <div class="row">
                <div class="col-md-11">
                    <div style="max-width:1000px">
                        $Description
                         <% if FormattedLocation %>
                            <div class="glyphicon glyphicon-map-marker"></div>
                            $getFormattedLocation(1)
                        <% end_if %>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-11">
                    <div style="max-width: 1000px">
                        $Instructions2Apply
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <% if FormattedMoreInfoLink %>
                        <a rel="nofollow" target="_blank" href="{$FormattedMoreInfoLink}">
                            <div class="button3">
                                <div class="glyphicon glyphicon-share-alt"></div>
                                <h5>Apply Now</h5>
                            </div>
                        </a>
                    <% end_if %>
                </div>
            </div>
        </div>
    </div>
</div>
<% end_with %>