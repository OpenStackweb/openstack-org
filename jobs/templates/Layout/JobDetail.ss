<div class="row">
    <div class="jumbotron">
        <div class="jobHeader">
            <div class="row">
                <div class="col-sm-6" id="OSJB">
                    <h1>OpenStack Job Board</h1>
                </div>
                <div class="col-sm-3 col-sm-offset-3 backButton">
                    <a href="$PostJobLink">
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



<div class="">
    <div class="col-sm-8">
        <div class="row">
            <div class="dateField">
                <div class="newBox">
                    <h5>NEW</h5>
                </div>
                <div class="publishedDate">
                    <h5>Published on $JobPostedDate.format(F) $JobPostedDate.format(d)</h5>
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="detail_title">
        	    <div class="job_title">$Title</div>
                
        	</div>
        </div>
    </div>
    <div class="jobPosting"  id="{$ID}">
        <div class="jobBasics">
            <p>
                <span class="glyphicon glyphicon-briefcase"></span>
                $JobCompany
            </p>

            <p>
                <span class="glyphicon glyphicon-map-marker"></span>
                <% if FormattedLocation %>
                    $FormattedLocation
                <% end_if %>
            </p>
             <p>
                <img id="coa" src="https://www.openstack.org/themes/openstack/images/coa/coa-badge.svg">
            </p>
           
        </div>

                    
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div style="max-width: 80%">
                            $Content
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div style="max-width: 80%">
                            $JobInstructions2Apply
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                </div>
        </div>       
        <div class="row">        
                <div class="col-sm-3">
                    <a href="">
                        <div class="button2" type="button">                            
                            <div class="glyphicon glyphicon-share-alt"></div>
                            <h5>Apply Now</h5>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>