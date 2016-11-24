<div class="container jobPosting" id="{$ID}">
    <div class="row">
        <div class="col-sm-11">        
            <div class="dateField">
                <div>
                    <% if RecentJob %>
                        <div class="newBox">
                            <h5>NEW</h5>
                        </div>
                    <% end_if %>
                </div>
                <div class="publishedDate">
                    <h5>Published on $PostedDate.format(F) $PostedDate.format(d)</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-11">       
            <div class="detail_title">
                <a rel="nofollow" target="_blank" href="/community/jobs/view/$ID/$Slug" class="jobTitle job_title_hold">$Title</a>                
            </div>
        </div>
        <div class="col-sm-1">
            <% if IsCOANeeded %>
                <img id="coa" src="themes/openstack/images/coa/coa-badge.svg">
            <% end_if %>            
        </div>
    </div>
    <div class="row">
        <div class="col-sm-11">    
            <div class="jobBasics-2">
                    <div class="glyphicon glyphicon-briefcase"></div>
                        $CompanyName
                    <div class="glyphicon glyphicon-map-marker"></div>
                    <% if FormattedLocation %>
                        $FormattedLocation
                    <% end_if %>     
            </div>
        </div>
        <div class="col-sm-1">
            <a href="#" class="jobExpand" data-id="{$ID}" >
                <h5> More info </h5>
            </a>
        </div>
    </div>
    <div class="row jobDescription" style="display:none;">
        <div class="col-md-11">
            <div class="row">
                <div class="col-md-11">
                    <div style="max-width:1000px">
                        $Description
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
            <br>
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