<div class="container">
	<div class="detail_title">
	    <span class="job_title">$Title</span><span class="label">Employer: </span> at <strong>$JobCompany</strong>
	</div>
    <div class="container jobPosting"  id="{$ID}">
        <div>
            <p>
                $JobPostedDate.format(F) $JobPostedDate.format(d)
                <% if FormattedLocation %>
                    - $FormattedLocation
                <% end_if %>
            </p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div style="max-width:1000px">
                            $Content
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div style="max-width: 1000px">
                            $RAW_val(JobInstructions2Apply)
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="moreInfo">
                            <span class="label">More information: </span><a rel="nofollow" href="$MoreInfoLink" target="_blank" >More About This Job</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>