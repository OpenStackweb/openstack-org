<h1>Dashboard</h1>
<hr />
<div style="margin:20px;text-align:center;color:red;font-size:20px;">
    PLEASE USE CAUTION. SOME OF THE DATA CONTAINED HEREIN MAY BE CONFIDENTIAL
</div>
<div class="span-24 last">
    <h2>Live Metrics</h2>
		<div class="span-8 featuredUserStory">
			<div class="wrapper">
			    <div>
			    <strong>Individual Members:</strong><br/>
			    $IndividualMemberCount
			    </div>
                <div>
                    <strong>Community Members:</strong><br/>
                    $CommunityMemberCount
                </div>
			    <div>
			    <strong>Newsletter Subscribers:</strong><br/>
			    $NewsletterMemberCount ($NewsletterPercentage%)
			    </div>
			    <div>
			    <strong>User Stories / Logos:</strong><br/>
			    $UserStoryCount / $UserLogoCount
			    </div>
                <div>
                    <strong># Total Votes</strong><br/>
					$SpeakerVotesCount
                </div>
                <div>
                    <strong>Average # of votes per submission:</strong><br/>
					$AverageVotesPerSubmmit
                </div>
			</div>
		</div>
      	<div class="span-8 featuredUserStory">
			<div class="wrapper">
			    <div>
			    <strong>Platinum Members:</strong>
			    $PlatinumMemberCount
			    </div>
			    <div>
			    <strong>Gold Members:</strong>
			    $GoldMemberCount
			    </div>
			    <div>
			    <strong>Corporate Sponsors:</strong>
			    $CorporateSponsorCount
			    </div>
			    <div>
			    <strong>Startup Sponsors:</strong>
			    $StartupSponsorCount
			    </div>
			    <div>
			    <strong>Supporting Organizations:</strong>
			    $SupportingOrganizationCount
			    </div>
			    <div><br/>
			    <strong>Total Organizations:</strong>
			    $TotalOrganizationCount
			    </div>
			</div>
		</div>
 		<div class="span-8 featuredUserStory last">
			<div class="wrapper">
			    <div>
			    <strong>Non-US Newsletter Subscribers:</strong><br/>
			    $NewsletterInternationalCount ($NewsletterInternationalPercentage%)
			    </div>
			    <div>
			    <strong>Individual Member Country Count:</strong><br/>
			    $IndividualMemberCountryCount
			    </div>
			    <div>
			    <strong>Non-US Organizations:</strong><br/>
			    $InternationalOrganizationCount ($OrgsInternationalPercentage%)
			    </div>
			</div>
		</div>
</div>
<hr class="space" />
<div class="span-24 last">
    <h2>User Data</h2>
    <ul>
        <li>Export Data</li>
            <ul>
                <li><a href="$Link(ExportDataUsersByRole)">Users by Role</a></li>
                <li><a href="$Link(ExportDataGerritUsers)">Gerrit Users</a></li>
                <li><a href="$Link(exportDupUsers)">Duplicated Users</a></li>
            </ul>
        <li>Statistics</li>
            <ul>
                <li><a href="$Top.Link(GerritStatisticsReport)">Gerrit Statistics Report</a></li>
            </ul>
        <li>Regional Data</li>
            <ul>
                <li><a href="$Link(ViewUsersPerRegion)">Users Per Region</a></li>
            </ul>
    </ul>

    <h2>Company Data</h2>
    <ul>
        <li>Export Data</li>
            <ul>
                <li><a href="$Link(ExportDataCompanyData)">Company Data</a></li>
            </ul>
        <li><a href="$Top.Link(ViewICLACompanies)">CLA/ICLA Status</a></li>
    </ul>

    <h2>Deployments / Deployment Survey</h2>
    <ul>
        <li>Surveys Data</li>
        <ul>
            <li><a href="$Link(SurveyBuilderListSurveys)">View Deployment Survey</a></li>
            <li><a href="$Link(SurveyBuilderListDeployments)">View Deployments</a></li>
        </ul>

        <li>Statistics</li>
            <ul>
                <li><a href="/sangria/ViewSurveysStatisticsSurveyBuilder">Deployment Survey Stats</a></li>
                <li><a href="/sangria/ViewDeploymentStatisticsSurveyBuilder">Deployment Stats</a></li>
            </ul>
        <li>Export Data</li>
            <ul>
                <li>
                    <a href="#" class="cvs_download_link">Deployment Survey CSV Download</a>
                    <div class="export_filters hidden">
                        $DateFilters(ExportSurveyResults,true, 1)
                    </div>
                </li>
                <li>
                    <a href="#" class="cvs_download_link">Deployment Survey CSV Download (Flat Format)</a>
                    <div class="export_filters hidden">
                        $DateFilters(ExportSurveyResultsFlat,true,  1)
                    </div>
                </li>
                <li>
                    <a href="#" class="cvs_download_link">Surveys by company CSV Download</a>
                    <div class="export_filters hidden">
                        $DateFilters(ExportSurveyResultsByCompany,true,  1)
                    </div>
                </li>
            </ul>
        <li>Regional Data</li>
            <ul>
                <li><a href="$Link(ViewDeploymentsPerRegion)">Deployments Per Region</a></li>
                <li><a href="$Link(ViewDeploymentSurveysPerRegion)">Deployment Surveys Per Region</a></li>
            </ul>
        <li>User Stories / Deployment Details</li>
            <ul>
                <li><a href="/sangria/ViewCurrentStories">Manage User Stories</a></li>
                <li><a href="/sangria/ViewDeploymentDetails">Manage Deployment Details</a></li>
            </ul>
    </ul>

    <h2>Events</h2>
    <ul>
        <li><a href="$Top.Link(ViewEventDetails)">New Event Submissions</a></li>
        <li><a href="$Top.Link(ViewPostedEvents)">Edit Posted Events</a></li>
        <li><a href="$Top.Link(ViewOpenstackDaysEvents)">Openstask Days Events</a></li>
    </ul>

    <h2>Jobs</h2>
    <ul>
        <li><a href="$Top.Link(ViewJobsDetails)">New Job Submissions</a></li>
        <li><a href="$Top.Link(ViewPostedJobs)">Edit Posted Jobs</a></li>
    </ul>

    <h2>Marketplace</h2>
    <ul>
        <li><a href="$Top.Link(ViewReviews)">Approve Product Reviews</a></li>
    </ul>

    <h2>Speakers</h2>
    <ul>
        <%-- <li><a href="/sangria/ViewSpeakingSubmissions">View Speaking Submissions</a></li> --%>
        <%-- <li><a href="$Link(ExportSpeakersData)">Speakers Users</a></li> --%>
        <li><a href="$Link(ExportSpeakersSubmissions)">Export Speakers Submissions</a></li>
    </ul>

    <h2>DB Cleanup</h2>
    <ul>
        <li><a href="/sangria/StandardizeOrgNames" id="stand_orgs">Standardize Organizations</a></li>
    </ul>
</div>
<br>
<br>
