<% include SoftwareHomePage_MainNavMenu Active=1 %>
<div class="software-main-wrapper">
    <div class="container">
        <div class="outer-project-back">
            <a href="$Top.Link(project-navigator)"><i class="fa fa-chevron-left"></i> Back to Project Navigator</a>
        </div>
    </div>
    <div class="container inner-software">
        <!-- Begin Page Content -->
        <div class="row project-details-intro">
            <div class="col-lg-8 col-md-7 col-sm-6">
                <h2>$Component.CodeName <i class="fa <% if $Component.IconClass %>$Component.IconClass<% else %>fa-cogs<% end_if %>"></i></h2>
                <h4>$Component.Name</h4>
                <div class="project-intro-links">
                    <% if $Component.WikiUrl %>
                    <p>
                        <a href="{$Component.WikiUrl}" target="_blank">Project wiki page</a>
                    </p>
                    <% end_if %>
                    <% if $Component.HasInstallationGuide %>
                    <p>
                        <a href="http://docs.openstack.org/{$Top.CurrentRelease.Slug}/" target="_blank">View the install guide</a>
                    </p>
                    <% end_if %>
                    <p>
                        <a href="/marketplace" target="_blank">Find this service in the Marketplace</a>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-5 col-sm-6">
                <div class="core-services-single-full small">
                    <div class="core-stats-wrapper">
                        <div class="row">
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    {$Component.Adoption}%
                                </div>
                                <div class="core-stat-title">
                                    Adoption
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    $Component.MaturityPoints <span>of</span> $Top.getMaxAllowedMaturityPoints
                                </div>
                                <div class="core-stat-title">
                                    Maturity
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    $Component.Age <span>yrs</span>
                                </div>
                                <div class="core-stat-title">
                                    Age
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="core-bottom">
                        <a data-target="#statsInfoModal" data-toggle="modal" class="projects-stats-tip" href="#"><i class="fa fa-question-circle"></i> What does this mean?</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 about-project-details">
                <h4>About this project</h4>
                $Component.Description
                <hr style="margin: 40px 0;">
                <h4>Project details</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive project-tags-table table-hover">
                            <table class="table maturity-table">
                                <thead>
                                <tr>
                                    <th>Maturity Indicators</th>
                                    <th></th>
                                    <th>Tag Details</th>
                                    <th>Meets Maturity<br>Requirements?</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Is there an install guide for this project guide (at docs.openstack.org)?
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.HasInstallationGuide %>class="on"<% end_if %>>
                                                <% if $Component.HasInstallationGuide %>
                                                    <i class="fa fa-circle"></i><span>Yes</span>
                                                    <% loop $Component.getCaveatsForReleaseType($Top.CurrentRelease.ID, InstallationGuide) %>
                                                        <i class="fa fa-sticky-note tag-caveat-note" data-container="body" data-toggle="popover" data-placement="right" data-content="{$Label} : {$Description}"></i>
                                                    <% end_loop %>
                                                <% else %>
                                                    <i class="fa fa-circle-o"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <% if $Component.HasInstallationGuide %>
                                            <a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-docs-install-guide.rst">View Install Guide</a>
                                        <% else %>
                                            &nbsp;
                                        <% end_if %>
                                    </td>
                                    <td>
                                        <ul>
                                            <% if $Component.HasInstallationGuide %>
                                                <li class="on">Yes</li>
                                            <% else %>
                                                <li>No</li>
                                            <% end_if %>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Number of software development kits (SDKs) which support this project.
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.SDKSupport %>class="on"<% end_if %>>
                                                <i class="fa fa-circle"></i><span>{$Component.SDKSupport}</span>
                                                <% loop $Component.getCaveatsForReleaseType($Top.CurrentRelease.ID, SDKSupport) %>
                                                    <i class="fa fa-sticky-note tag-caveat-note" data-container="body" data-toggle="popover" data-placement="right" data-content="{$Label} : {$Description}"></i>
                                                <% end_loop %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-sdk-support.rst">View Details</a></td>
                                    <td>
                                        <ul>
                                            <% if $Component.SDKSupport > 7 %>
                                                <li class="on">Yes</li>
                                            <% else %>
                                                <li>No</li>
                                            <% end_if %>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Percentage of deployments using this project in production environments.
                                        <i data-content="Adoption data is derived from the latest <a href='//www.openstack.org/user-survey'>user survey</a>." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="How is this calculated?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li class="on">
                                                <a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-production-use.rst">
                                                    <i class="fa fa-circle"></i><span> {$Component.Adoption}%</span>
                                                    <% loop $Component.getCaveatsForReleaseType($Top.CurrentRelease.ID, ProductionUse) %>
                                                            <i class="fa fa-sticky-note tag-caveat-note" data-container="body" data-toggle="popover" data-placement="right" data-content="{$Label} : {$Description}"></i>
                                                    <% end_loop %>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-production-use.rst">View Details</a></td>
                                    <td>
                                        <ul>
                                            <% if $Component.Adoption > 75 %>
                                                <li class="on">Yes</li>
                                            <% else %>
                                                <li>No</li>
                                            <% end_if %>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Has this project team achieved corporate diversity?
                                        <i data-content="A project with this tag has achieved a level of diversity in the affiliation of contributors that is indicative of a healthy collaborative project. This tag exists in the ‘team’ category, which as the name implies, covers information about the team itself. Another example of a tag that could exist in this category is one that conveys the size of the team that is actively contributing." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What does this mean?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.HasTeamDiversity %>class="on" <% end_if %>>
                                                <% if $Component.HasTeamDiversity %>
                                                    <i class="fa fa-circle"></i><span>Yes</span>
                                                <% else %>
                                                    <i class="fa fa-circle-o"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/team_diverse-affiliation.html">View Details</a></td>
                                    <td>
                                        <ul>
                                            <% if $Component.HasTeamDiversity %>
                                                <li class="on">Yes</li>
                                            <% else %>
                                                <li>No</li>
                                            <% end_if %>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Does this project have stable branches?
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.HasStableBranches %>class="on"<% end_if %>>
                                                <% if $Component.HasStableBranches %>
                                                    <i class="fa fa-circle"></i><span>Yes</span>
                                                <% else %>
                                                    <i class="fa fa-circle-o"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/release_has-stable-branches.html">View Details</a></td>
                                    <td>
                                        <ul>
                                            <% if $Component.HasStableBranches %>
                                                <li class="on">Yes</li>
                                            <% else %>
                                                <li>No</li>
                                            <% end_if %>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Does this project follows standard deprecation?
                                        <i data-content="The “assert:follows-standard-deprecation” tag asserts that the project will follow standard feature deprecation rules" title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What does this mean?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.FollowsStandardDeprecation %>class="on" <% end_if %>>
                                                <% if $Component.FollowsStandardDeprecation %>
                                                    <i class="fa fa-circle"></i><span>Yes</span>
                                                <% else %>
                                                    <i class="fa fa-circle-o"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/assert_follows-standard-deprecation.html">View Details</a></td>
                                    <td>
                                        <ul>
                                            <% if $Component.FollowsStandardDeprecation %>
                                                <li class="on">Yes</li>
                                            <% else %>
                                                <li>No</li>
                                            <% end_if %>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Does this project support minimal cold (offline) upgrade capabilities?
                                        <i data-content="asserts that the project will support minimal cold (offline) upgrade capabilities" title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What does this mean?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.SupportsUpgrade %>class="on" <% end_if %>>
                                                <% if $Component.SupportsUpgrade %>
                                                    <i class="fa fa-circle"></i><span>Yes</span>
                                                <% else %>
                                                    <i class="fa fa-circle-o"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/assert_supports-upgrade.html">View Details</a></td>
                                    <td>
                                        <ul>
                                            <% if $Component.SupportsUpgrade %>
                                                <li class="on">Yes</li>
                                            <% else %>
                                                <li>No</li>
                                            <% end_if %>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Does this project support minimal rolling upgrade capabilities?
                                        <i data-content="tag asserts that the project will support minimal rolling upgrade capabilities." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What does this mean?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.SupportsRollingUpgrade %>class="on" <% end_if %>>
                                                <% if $Component.SupportsRollingUpgrade %>
                                                    <i class="fa fa-circle"></i><span>Yes</span>
                                                <% else %>
                                                    <i class="fa fa-circle-o"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/assert_supports-rolling-upgrade.html">View Details</a></td>
                                    <td>
                                        <ul>
                                            <% if $Component.SupportsRollingUpgrade %>
                                                <li class="on">Yes</li>
                                            <% else %>
                                                <li>No</li>
                                            <% end_if %>
                                        </ul>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive project-tags-table table-hover">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Additional Information</th>
                                    <th></th>
                                    <th>Tag Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        How is this project released?
                                        <i data-content="OpenStack development happens on a six-month cycle. Projects can choose to release on this cycle with oversight of the release management team, or to release independently of the cycle." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="How are projects released?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.ReleaseMileStones %>class="on"<% end_if %>>
                                                <a href="http://governance.openstack.org/reference/tags/release_cycle-with-milestones.html">
                                                    <i class="fa <% if $Component.ReleaseMileStones %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span>Cycle with milestones</span>
                                                </a>
                                            </li>
                                            <li <% if $Component.ReleaseCycleWithIntermediary %>class="on" <% end_if %>>
                                                <a href="http://governance.openstack.org/reference/tags/release_cycle-with-intermediary.html">
                                                    <i class="fa <% if $Component.ReleaseCycleWithIntermediary %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span>Cycle with intermediary</span>
                                                </a>
                                            </li>
                                            <li <% if $Component.ReleaseIndependent %>class="on" <% end_if %>>
                                                <a href="http://governance.openstack.org/reference/tags/release_independent.html">
                                                    <i class="fa <% if $Component.ReleaseIndependent %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span>Independent</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/release_cycle-with-milestones.html">View Details</a></td>
                                </tr>

                                <tr>
                                    <td>
                                         Existence and quality of packages for this project in popular distributions.
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.QualityOfPackages == 'good' %>class="on"<% end_if %>>
                                                <a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-packaged.rst" target="_blank">
                                                    <i class="fa <% if $Component.QualityOfPackages == 'good' %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span>Good</span>
                                                </a>
                                                <% if $Component.QualityOfPackages == 'good' %>
                                                    <% loop $Component.getCaveatsForReleaseType($Top.CurrentRelease.ID, QualityOfPackages) %>
                                                        <i class="fa fa-sticky-note tag-caveat-note" data-container="body" data-toggle="popover" data-placement="right" data-content="{$Label} : {$Description}"></i>
                                                    <% end_loop %>
                                                <% end_if %>
                                            </li>
                                            <li <% if $Component.QualityOfPackages == 'no' %>class="on"<% end_if %>>
                                                <a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-packaged.rst" target="_blank">
                                                    <i class="fa <% if $Component.QualityOfPackages == 'no' %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span>No</span>
                                                </a>
                                                <% if $Component.QualityOfPackages == 'no' %>
                                                    <% loop $Component.getCaveatsForReleaseType($Top.CurrentRelease.ID, QualityOfPackages) %>
                                                        <i class="fa fa-sticky-note tag-caveat-note" data-container="body" data-toggle="popover" data-placement="right" data-content="{$Label} : {$Description}"></i>
                                                    <% end_loop %>
                                                <% end_if %>
                                            </li>
                                            <li <% if $Component.QualityOfPackages == 'warning' %>class="on"<% end_if %>>
                                                <a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-packaged.rst" target="_blank">
                                                    <i class="fa <% if $Component.QualityOfPackages == 'warning' %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span>Warning</span>
                                                </a>
                                                <% if $Component.QualityOfPackages == 'warning' %>
                                                    <% loop $Component.getCaveatsForReleaseType($Top.CurrentRelease.ID, QualityOfPackages) %>
                                                        <i class="fa fa-sticky-note tag-caveat-note" data-container="body" data-toggle="popover" data-placement="right" data-content="{$Label} : {$Description}"></i>
                                                    <% end_loop %>
                                                <% end_if %>
                                            </li>
                                            <li <% if $Component.QualityOfPackages == 'beginning' %>class="on"<% end_if %>>
                                                <a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-packaged.rst" target="_blank">
                                                    <i class="fa <% if $Component.QualityOfPackages == 'beginning' %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span>Beginning</span>
                                                </a>
                                                <% if $Component.QualityOfPackages == 'beginning' %>
                                                    <% loop $Component.getCaveatsForReleaseType($Top.CurrentRelease.ID, QualityOfPackages) %>
                                                        <i class="fa fa-sticky-note tag-caveat-note" data-container="body" data-toggle="popover" data-placement="right" data-content="{$Label} : {$Description}"></i>
                                                    <% end_loop %>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-packaged.rst" target="_blank">View Details</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        Are vulnerability issues managed by the OpenStack security team?
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.VulnerabilityManaged %>class="on" <% end_if %>>
                                                <% if $Component.VulnerabilityManaged %>
                                                    <i class="fa fa-circle"></i><span>Yes</span>
                                                <% else %>
                                                    <i class="fa fa-circle-o"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/vulnerability_managed.html">View Details</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <% if $Component.ContributionsJson %>
        <div class="row">
            <div class="col-sm-12 project-details-chart-section">
                <h4>Contributions to $Component.CodeName</h4>
                <div style="width: 100%; height: 120px; margin-top: 15px; position: relative;" id="timeline">
                </div>
            </div>
        </div>
        <% end_if %>
        <% if $Component.LatestReleasePTL %>
        <div class="row project-details-ptl">
            <div class="col-sm-12">
                <h4>PTL for Latest Release</h4>
            </div>
            <div class="row">
                <div class="ptl-left">
                    <img alt="" src="{$Component.LatestReleasePTL.ProfilePhotoUrl}" class="ptl-bio-pic">
                    <div class="ptl-details">
                        <h4>$Component.LatestReleasePTL.FullName</h4>
                        <p>
                            $Component.LatestReleasePTL.CurrentPosition
                        </p>
                        <p>
                            <a target="_blank" href="community/members/profile/{$Component.LatestReleasePTL.ID}">
                                <img alt="OpenStack Profile" src="themes/openstack/images/software/ptl-openstack.png"></a>
                            <% if $Component.LatestReleasePTL.TwitterName %>
                            <a target="_blank" href="https://twitter.com/{$Component.LatestReleasePTL.TwitterName}">
                                <img alt="Twitter Profile" src="themes/openstack/images/software/ptl-twitter.png">
                            </a>
                            <% end_if %>
                        </p>
                    </div>
                </div>
                <div class="project-details-ptl-bio">
                    <p>
                        $Component.LatestReleasePTL.Bio
                    </p>
                </div>
            </div>
        </div>
        <% end_if %>
        <% if $Component.YouTubeID %>
        <div class="row project-details-ptl">
            <div class="col-sm-12">
                <h4>$Component.VideoTitle</h4>
                <p>$Component.VideoDescription</p>
                <iframe width="356" height="200" src="//www.youtube.com/embed/{$Component.YouTubeID}" frameborder="0" allowfullscreen=""></iframe>
            </div>
        </div>
        <% end_if %>
        <div class="row project-details-other">
            <div class="col-sm-6">
                <% if $MostActiveCompanyContributors %>
                <h4>Most Active Contributors by Company</h4>
                <ul>
                    <% loop $MostActiveCompanyContributors %>
                        <li>$Name</li>
                    <% end_loop %>
                </ul>
                <% end_if %>
                <% if $MostActiveIndividualContributors %>
                <h4>Most Active Individual Contributors</h4>
                <ul>
                    <% loop $MostActiveIndividualContributors %>
                        <li>$Name</li>
                    <% end_loop %>
                </ul>
                <% end_if %>
                <% if $Component.RelatedContent %>
                <h4>Related Content</h4>
                <ul>
                    <% loop $Component.RelatedContent %>
                    <li><a href="{$Url}" target="_blank">{$Title}</a></li>
                    <% end_loop %>
                </ul>
                <% end_if %>
            </div>
            <div class="col-sm-6 right">
                <h4>API Version History</h4>
                <div class="project-timeline">
                    <ul>
                        <% loop Releases %>
                            <li <% if $Status == Current %>class="timeline-current"<% end_if %><% if $Status == Future %>class="timeline-future"<% end_if %>>
                                <a href="http://docs.openstack.org/releases/releases/{$Slug}.html" target="_blank">
                                    Version {$getVersionLabel($Top.Component.ID)} ({$Name}) <% if $Status == Current %>- LATEST RELEASE<% end_if %>
                                </a>
                            </li>
                        <% end_loop %>
                        <li class="timeline-past">
                            Previous Versions Deprecated
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 project-details-footnotes">
                <h6><a target="_blank" href="http://stackalytics.com">Statistics and charts provided by stackalytics.com</a></h6>
            </div>
        </div>
        <!-- Stats 'what does this mean?' Modal -->
        <div id="statsInfoModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">What Do These Stats Mean?</h4>
                    </div>
                    <div class="modal-body">
                        <p class="download-text">
                        </p>
                        <hr>
                        <p>
                            <strong>Adoption</strong> is the percentage of production deployments running the project based on the latest biannual user survey results.
                        </p>
                        <p>
                            <strong>Maturity</strong> comes from looking at 5 distinct tags that indicate stability and sustainability. The current criteria includes whether or not the project has an install guide, whether it is supported by 7 or more SDKs, if the adoption percentage is greater than 75%, whether or not the team has achieved corporate diversity and whether or not there are stable branches.
                        </p>
                        <p>
                            <strong>Age</strong> is the number of years the project has been in development.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- End Modal -->
        <!-- End Page Content -->
    </div>
</div>
