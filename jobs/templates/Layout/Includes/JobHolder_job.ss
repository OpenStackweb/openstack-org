<div class="container jobPosting" id="{$ID}">
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <% if RecentJob %>
                <span class="label label-danger">NEW</span>
            <% end_if %>
            <h5 class="publishedDate">Published on $PostedDate.format(F) $PostedDate.format(d)</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-10 col-xs-6 col-lg-10">
            <div class="detail_title">
                <a rel="nofollow" target="_blank" href="/community/jobs/view/$ID/$Slug" class="jobTitle job_title_hold">$Title</a>
            </div>
        </div>
        <div class="col-sm-2 col-xs-6 col-lg-2 img-container">
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
                    <div class="glyphicon glyphicon-map-marker"></div>
                    <% if FormattedLocation %>
                        $FormattedLocation
                    <% end_if %>
                    <% if IsCOANeeded %>
                        &nbsp;
                        <div class="glyphicon glyphicon-ok"></div>
                        COA Required
                    <% end_if %>
           </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-xs-4 cta-container">
            <a href="#" class="jobExpand" data-id="{$ID}" >
                <h5> Learn More </h5>
            </a>
        </div>
    </div>
    <div class="row jobDescription" style="display:none;">
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
                    <a rel="nofollow" target="_blank" href="/community/jobs/view/$ID/$Slug">
                        <div class="button2">                            
                            <div class="glyphicon glyphicon-plus"></div>
                            <h5>Read full description</h5>
                        </div>
                    </a>
                </div> 
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