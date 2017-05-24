<% include SoftwareHomePage_MainNavMenu Active=1 %>
<div class="software-main-wrapper">
    <div class="container">
        <div class="outer-project-back">
            <a href="$Top.Link(project-navigator)"><i class="fa fa-chevron-left"></i> <%t Software.BACK_TO_NAVIGATOR 'Back to Project Navigator' %></a>
        </div>
    </div>
    <div class="container inner-software">
        <!-- Begin Page Content -->
        <div class="row project-details-intro">
            <div class="col-lg-2 col-md-2 col-sm-2">
                <img src="/software/images/mascots/{$Component.MascotRef}.png" width="100%">
            </div>
            <div class="col-lg-6 col-md-5 col-sm-4">
                <h2>$Component.CodeName <i class="<% if $Component.Mascot %>$Component.Mascot.Name<% else %>Barbican<% end_if %>"></i></h2>
                <h4>$Component.Name</h4>
                <div class="project-intro-links">
                    <% if $Component.WikiUrl %>
                    <p>
                        <a href="{$Component.WikiUrl}" target="_blank"><%t Software.PROJECT_WIKI 'Project wiki page' %></a>
                    </p>
                    <% end_if %>
                    <% if $Component.HasInstallationGuide %>
                    <p>
                        <a href="http://docs.openstack.org/project-install-guide/{$Top.CurrentRelease.Slug}/" target="_blank">
                            <%t Software.VIEW_INSTALL_GUIDE 'View the install guide' %>
                        </a>
                    </p>
                    <% end_if %>
                    <p>
                        <a href="/marketplace" target="_blank"><%t Software.FIND_SERVICE_MARKETPLACE 'Find this service in the Marketplace' %></a>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-5 col-sm-6">
                <div class="core-services-single-full small">
                    <div class="core-stats-wrapper">
                        <div class="row">
                            <div class="col-sm-4 col-xs-4">
                                <% if $Component.Adoption > 0 %>
                                    <div class="core-stat-graphic">
                                        {$Component.Adoption}%
                                    </div>
                                <% else %>
                                    <div class="core-stat-graphic off"></div>
                                <% end_if %>
                                <div class="core-stat-title <% if $Component.Adoption == 0 %>off<% end_if %>">
                                    <%t Software.ADOPTION 'Adoption' %>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <% if $Component.MaturityPoints > 0 %>
                                    <div class="core-stat-graphic">
                                        $Component.MaturityPoints
                                        <span><%t Openstack.RANGE_OF 'of' %></span>
                                        $Top.getMaxAllowedMaturityPoints
                                    </div>
                                <% else %>
                                    <div class="core-stat-graphic off"></div>
                                <% end_if %>
                                <div class="core-stat-title <% if $Component.MaturityPoints == 0 %>off<% end_if %>">
                                    <%t Software.MATURITY 'Maturity' %>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    $Component.Age <span>yrs</span>
                                </div>
                                <div class="core-stat-title">
                                    <%t Software.AGE 'Age' %>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="core-bottom">
                        <a data-target="#statsInfoModal" data-toggle="modal" class="projects-stats-tip" href="#">
                        <i class="fa fa-question-circle"></i>
                        <%t Software.WHAT_DOES_MEAN 'What does this mean?' %></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 about-project-details">
                <h4><%t Software.ABOUT_PROJECT 'About this project' %></h4>
                $Component.Description
                <% if $HasAdditionalInfo || $HasMaturityIndicators %>
                    <hr style="margin: 40px 0;">
                    <h4><%t Software.PROJECT_DETAILS 'Project details' %></h4>
                    <% if $HasMaturityIndicators %>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive project-tags-table table-hover">
                                <table class="table maturity-table">
                                    <thead>
                                    <tr>
                                        <th colspan="2"><%t Software.MATURITY_INDICATORS 'Maturity Indicators' %></th>
                                        <th><%t Software.TAG_DETAILS 'Tag Details' %></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <% if $Component.Adoption > 75 %>
                                        <tr>
                                            <td class="maturity">{$Component.Adoption}%</td>
                                            <td>
                                                <%t Software.PERCENTAGE_OF_DEPLOYMENTS 'of deployments using this project in production environments.' %>
                                                <a href="#" onclick="return false;" data-html="true" data-trigger="focus" data-content="<%t Software.ADOPTION_DATA_CONTENT "Adoption data is derived from the latest <a href='//www.openstack.org/user-survey'>user survey</a>." %>" title="" data-placement="right" data-toggle="popover" data-original-title="<%t Software.HOW_CALCULATED 'How is this calculated?' %>"><i class="fa fa-question-circle tag-tooltip"></i></a>
                                            </td>
                                            <td><a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-production-use.rst"><%t Openstack.VIEW_DETAILS 'View Details' %></a></td>
                                        </tr>
                                    <% end_if %>

                                    <% if $Component.SDKSupport > 7 %>
                                        <tr>
                                            <td class="maturity">$Component.SDKSupport</td>
                                            <td><%t Software.NUMBER_OF_SDK 'software development kits (SDKs) support' %>  $Component.CodeName </td>
                                            <td>
                                                <a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-sdk-support.rst">
                                                    <%t Openstack.VIEW_DETAILS 'View Details' %>
                                                </a>
                                            </td>
                                        </tr>
                                    <% end_if %>


                                    <% if $Component.HasInstallationGuide %>
                                        <tr>
                                            <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                            <td> $Component.CodeName <%t Software.IS_THERE_INSTALL 'is included in the install guide.' %></td>
                                            <td>
                                                <a href="http://docs.openstack.org/project-install-guide/{$Top.CurrentRelease.Slug}/" target="_blank">
                                                    <%t Software.VIEW_INSTALL_GUIDE 'View the install guide' %>
                                                </a>
                                            </td>
                                        </tr>
                                    <% end_if %>

                                    <% if $Component.HasTeamDiversity %>
                                        <tr>
                                            <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                            <td>
                                                $Component.CodeName <%t Software.USED_IN_CORPORATE ' team has achieved corporate diversity' %>
                                                <a href="#" onclick="return false;" data-trigger="focus" data-content="<%t Software.PROJECT_DIVERSITY "A project with this tag has achieved a level of diversity in the affiliation of contributors that is indicative of a healthy collaborative project. This tag exists in the ‘team’ category, which as the name implies, covers information about the team itself. Another example of a tag that could exist in this category is one that conveys the size of the team that is actively contributing." %>" title="" data-placement="right" data-toggle="popover" data-original-title="<%t Software.WHAT_DOES_MEAN 'What does this mean?' %>"><i class="fa fa-question-circle tag-tooltip"></i></a>
                                            </td>
                                             <td><a href="http://governance.openstack.org/reference/tags/team_diverse-affiliation.html"><%t Openstack.VIEW_DETAILS 'View details' %></a></td>
                                        </tr>
                                    <% end_if %>

                                    <% if $Component.HasStableBranches %>
                                        <tr>
                                            <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                            <td>
                                                $Component.CodeName <%t Software.STABLE_BRANCHES 'is maintained following the common Stable branch policy' %>
                                            </td>
                                            <td><a href="http://docs.openstack.org/project-team-guide/stable-branches.html"><%t Openstack.VIEW_DETAILS 'View details' %></a></td>
                                        </tr>
                                    <% end_if %>

                                    <% if $Component.FollowsStandardDeprecation %>
                                        <tr>
                                            <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                            <td>
                                                $Component.CodeName <%t Software.FOLLOW_DEPRECATION 'follows standard deprecation' %>
                                                <a href="#" onclick="return false;" data-trigger="focus" data-content="<%t Software.DEPRECATION_TAG_DESCRIPTION 'The “assert:follows-standard-deprecation” tag asserts that the project will follow standard feature deprecation rules' %>" title="" data-placement="right" data-toggle="popover" data-original-title="<%t Software.WHAT_DOES_MEAN 'What does this mean?' %>"><i class="fa fa-question-circle tag-tooltip"></i></a>
                                            </td>
                                            <td><a href="http://governance.openstack.org/reference/tags/assert_follows-standard-deprecation.html"><%t Openstack.VIEW_DETAILS 'View details' %></a></td>
                                        </tr>
                                    <% end_if %>

                                    <% if $Component.SupportsUpgrade %>
                                        <tr>
                                            <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                            <td>
                                                $Component.CodeName <%t Software.MINIMAL_UPGRADE 'supports minimal cold (offline) upgrade capabilities' %>
                                                <a href="#" onclick="return false;" data-trigger="focus" data-content="<%t Software.MINIMAL_UPGRADE_DESCRIPTION 'asserts that the project will support minimal cold (offline) upgrade capabilities' %>" title="" data-placement="right" data-toggle="popover" data-original-title="<%t Software.WHAT_DOES_MEAN 'What does this mean?' %>"><i class="fa fa-question-circle tag-tooltip"></i></a>
                                            </td>
                                            <td><a href="http://governance.openstack.org/reference/tags/assert_supports-upgrade.html"><%t Openstack.VIEW_DETAILS 'View details' %></a></td>
                                        </tr>
                                    <% end_if %>

                                    <% if $Component.SupportsRollingUpgrade %>
                                        <tr>
                                            <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                            <td>
                                                $Component.CodeName <%t Software.MINIMAL_ROLLING 'supports minimal rolling upgrade capabilities' %>
                                                <a href="#" onclick="return false;" data-trigger="focus" data-content="<%t Software.MINIMAL_ROLLING_DESCRIPTION 'tag asserts that the project will support minimal rolling upgrade capabilities.' %>" title="" data-placement="right" data-toggle="popover" data-original-title="<%t Software.WHAT_DOES_MEAN 'What does this mean?' %>"><i class="fa fa-question-circle tag-tooltip"></i></a>
                                            </td>
                                            <td><a href="http://governance.openstack.org/reference/tags/assert_supports-rolling-upgrade.html"><%t Openstack.VIEW_DETAILS 'View details' %></a></td>
                                        </tr>
                                    <% end_if %>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <% end_if %>
                    <% if $HasAdditionalInfo %>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive project-tags-table table-hover">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th><%t Software.PROJECT_INFO 'Project Info' %></th>
                                        <th></th>
                                        <th><%t Software.TAG_DETAILS 'Tag Details' %></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <% if $HasReleaseDesc %>
                                    <tr>
                                        <td>
                                            <%t Software.HOW_RELEASED 'How is this project released?' %>
                                            <a href="#" onclick="return false;" data-trigger="focus" data-content="<%t Software.HOW_RELEASED_DESCRIPTION 'OpenStack development happens on a six-month cycle. Projects can choose to release on this cycle with oversight of the release management team, or to release independently of the cycle.' %>" title="" data-placement="right" data-toggle="popover" data-original-title="<%t Software.HOW_PROJECTS_RELEASED 'How are projects released?' %>"><i class="fa fa-question-circle tag-tooltip"></i></a>
                                        </td>
                                        <td>
                                            <ul>
                                                <li <% if $Component.ReleaseMileStones %>class="on"<% end_if %>>
                                                    <a target="_blank" href="https://releases.openstack.org/reference/release_models.html#cycle-with-milestones">
                                                        <i class="fa <% if $Component.ReleaseMileStones %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span><%t Software.CYCLE_WITH_MILESTONES 'Cycle with milestones' %></span>
                                                    </a>
                                                </li>
                                                <li <% if $Component.ReleaseCycleWithIntermediary %>class="on" <% end_if %>>
                                                    <a target="_blank" href="https://releases.openstack.org/reference/release_models.html#cycle-with-intermediary">
                                                        <i class="fa <% if $Component.ReleaseCycleWithIntermediary %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span><%t Software.CYCLE_WITH_INTERMEDIARY 'Cycle with intermediary' %></span>
                                                    </a>
                                                </li>
                                                <li <% if $Component.ReleaseTrailing %>class="on" <% end_if %>>
                                                    <a target="_blank" href="https://releases.openstack.org/reference/release_models.html#cycle-trailing">
                                                        <i class="fa <% if $Component.ReleaseTrailing %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span><%t Software.TRAILING 'Trailing' %></span>
                                                    </a>
                                                </li>
                                                <li <% if $Component.ReleaseIndependent %>class="on" <% end_if %>>
                                                    <a target="_blank" href="https://releases.openstack.org/reference/release_models.html#independent">
                                                        <i class="fa <% if $Component.ReleaseIndependent %>fa-circle<% else %>fa-circle-o<% end_if %>"></i><span><%t Software.INDEPENDENT 'Independent' %></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td><a target="_blank" href="https://releases.openstack.org/reference/release_models.html"><%t Openstack.VIEW_DETAILS 'View details' %></a></td>
                                    </tr>
                                    <% end_if %>
                                    <% if $Component.VulnerabilityManaged %>
                                    <tr>
                                        <td>
                                            <%t Software.VULNERABILITY_ISSUES 'Are vulnerability issues managed by the OpenStack security team?' %>
                                        </td>
                                        <td>
                                            <ul>
                                                <li <% if $Component.VulnerabilityManaged %>class="on" <% end_if %>>
                                                    <% if $Component.VulnerabilityManaged %>
                                                        <i class="fa fa-circle"></i><span><%t Openstack.YES 'Yes' %></span>
                                                    <% else %>
                                                        <i class="fa fa-circle-o"></i><span><%t Openstack.NO 'No' %></span>
                                                    <% end_if %>
                                                </li>
                                            </ul>
                                        </td>
                                        <td><a href="http://governance.openstack.org/reference/tags/vulnerability_managed.html"><%t Openstack.VIEW_DETAILS 'View details' %></a></td>
                                    </tr>
                                    <% end_if %>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <% end_if %>
                <% end_if %>
            </div>
        </div>
        <% if $Component.ContributionsJson %>
        <div class="row">
            <div class="col-sm-12 project-details-chart-section">
                <h4><%t Software.CONTRIBUTIONS_TO 'Contributions to {codename}' codename=$Component.CodeName %></h4>
                <div style="width: 100%; height: 120px; margin-top: 15px; position: relative;" id="timeline">
                </div>
            </div>
        </div>
        <% end_if %>
        <% if $Component.LatestReleasePTL %>
        <div class="row project-details-ptl">
            <div class="col-sm-12">
                <h4><%t Software.PTL_FOR_RELEASE 'PTL for Latest Release' %></h4>
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
                                <img alt="OpenStack Profile" src="themes/openstack/images/foundation-staff/icon_openstack.png"></a>
                            <% if $Component.LatestReleasePTL.TwitterName %>
                            <a target="_blank" href="https://twitter.com/{$Component.LatestReleasePTL.TwitterName}">
                                <img alt="Twitter Profile" src="themes/openstack/images/foundation-staff/icon_twitter.png">
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
                <h4><%t Software.MOST_ACTIVE_CONTRIBUTORS 'Most Active Contributors by Company' %></h4>
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
                <h4><%t Software.RELATED_CONTENT 'Related Content' %></h4>
                <ul>
                    <% loop $Component.RelatedContent %>
                    <li><a href="{$Url}" target="_blank">{$Title}</a></li>
                    <% end_loop %>
                </ul>
                <% end_if %>
            </div>
            <div class="col-sm-6 right">
                <h4><%t Software.API_HISTORY 'API Version History' %></h4>
                <div class="project-timeline">
                    <ul>
                        <% loop Releases %>
                            <li <% if $Status == Current %>class="timeline-current"<% end_if %><% if $Status == Future %>class="timeline-future"<% end_if %>>
                                <a href="https://releases.openstack.org/{$Slug}/index.html#{$Slug}-{$Top.Component.Slug}" target="_blank">
                                    <%t Software.VERSION 'Version' %> {$getCurrentSupportedApiVersionLabel($Top.Component.ID)} ({$Name}) <% if $Status == Current %>- LATEST RELEASE<% end_if %>
                                </a>
                            </li>
                        <% end_loop %>
                        <li class="timeline-past">
                            <%t Software.PREVIOUS_DEPRECATED 'Previous Versions Deprecated' %>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 project-details-footnotes">
                <h6><a target="_blank" href="http://stackalytics.com"><%t Software.CHARTS_ATTR 'Statistics and charts provided by stackalytics.com' %></a></h6>
            </div>
        </div>
        <!-- Stats 'what does this mean?' Modal -->
        <div id="statsInfoModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only"><%t Openstack.CLOSE 'Close' %></span></button>
                        <h4 class="modal-title"><%t Software.WHAT_STATS_MEAN 'What Do These Stats Mean?' %></h4>
                    </div>
                    <div class="modal-body">
                        <p class="download-text">
                        </p>
                        <hr>
                        <p>
                            <%t Software.ADOPTION_DESCRIPTION '<strong>Adoption</strong> is the percentage of production deployments running the project based on the latest biannual user survey results.' %>
                        </p>
                        <p>
                            <%t Software.MATURITY_DESCRIPTION '<strong>Maturity</strong> comes from looking at 5 distinct tags that indicate stability and sustainability. The current criteria includes whether or not the project has an install guide, whether it is supported by 7 or more SDKs, if the adoption percentage is greater than 75%, whether or not the team has achieved corporate diversity and whether or not there are stable branches.' %>
                        </p>
                        <p>
                            <%t Software.AGE_DESCRIPTION '<strong>Age</strong> is the number of years the project has been in development.' %>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button"><%t Openstack.CLOSE 'Close' %></button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- End Modal -->
        <!-- End Page Content -->
    </div>
</div>
