<% include SoftwareHomePage_MainNavMenu Active=$ParentCategory.ID %>
<div class="software-main-wrapper project-details-wrapper">
    <div class="container">
        <div class="outer-project-back">
            <a href="$Top.Link(project-navigator)/{$ParentCategory.Slug}"><i class="fa fa-chevron-left"></i> <%t Software.BACK_TO_NAVIGATOR 'Back to Project Navigator' %></a>
        </div>
    </div>
    <div class="container inner-software">
        <!-- Begin Page Content -->
        <div class="row project-details-intro">
            <div class="col-md-2 col-sm-2">
                <img src="/software/images/mascots/{$Component.MascotRef}.png" width="100%">
            </div>
            <div class="col-md-10 col-sm-10">
                <h2>$Component.CodeName <i class="<% if $Component.Mascot %>$Component.Mascot.Name<% else %>Barbican<% end_if %>"></i></h2>
                <h4>$Component.Name</h4>
                <div class="project-intro-links">
                    <% if $Component.DocsLink().Exists() %>
                        <p>
                            <a href="{$Component.DocsLink().URL}" target="_blank">
                                <%-- do not separate icon from label --%>
                                <i class="fa fa-book" aria-hidden="true" style="margin-right: 8px"></i><span>{$Component.DocsLink().Label}</span>
                            </a>
                        </p>
                    <% end_if %>
                    <% if $Component.hasCodeLink() %>
                    <p>
                        <a href="{$Component.getCodeLink()}" target="_blank">
                            <%-- do not separate icon from label --%>
                            <i class="fa fa-code" aria-hidden="true" style="margin-right: 8px"></i><span>Latest code source release</span>
                        </a>
                    </p>
                    <% end_if %>
                    <% if $Component.DownloadLink().Exists() %>
                        <p>
                            <a href="{$Component.DownloadLink().URL}" target="_blank">
                                <%-- do not separate icon from label --%>
                                <i class="fa fa-cloud-download" aria-hidden="true" style="margin-right: 8px"></i><span>{$Component.DownloadLink().Label}</span>
                            </a>
                        </p>
                    <% end_if %>
                    <% if $Component.HasInstallationGuide %>
                        <p>
                            <a href="http://docs.openstack.org/project-install-guide/{$Top.CurrentRelease.Slug}/" target="_blank">
                                <%t Software.VIEW_INSTALL_GUIDE 'View the install guide' %>
                            </a>
                        </p>
                    <% end_if %>
                    <% if $Component.Since %>
                        <p>
                            <%t Software.FIRST_APPEARANCE 'First appeared in OpenStack' %> '$Component.Since' release
                        </p>
                    <% end_if %>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 about-project-details">
                <h4><%t Software.ABOUT_PROJECT 'About this project' %></h4>
                $Component.Description

                <% if $Component.Links().Count %>
                    <hr style="margin: 40px 0;">
                    <h4><%t Software.RELATED_LINKS 'Related Links' %></h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <% loop $Component.Links() %>
                                <a class="component-link" href="{$URL}">{$Label}</a>
                                <% if not $Last %><span style="margin-right:10px">|</span><% end_if %>
                            <% end_loop %>
                        </div>
                    </div>
                <% end_if %>

                <% if $Component.Dependencies().Count %>
                    <hr style="margin: 40px 0;">
                    <h4><%t Software.DEPENDS_ON 'Depends on' %></h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <% loop $Component.Dependencies() %>
                                <a class="btn btn-default team-button" href="{$getLink}">
                                    <img src="/software/images/mascots/{$MascotRef}.png" width="50px" /><br/>
                                    {$CodeName}
                                </a>
                            <% end_loop %>
                        </div>
                    </div>
                <% end_if %>

                <% if $Component.RelatedComponents().Count %>
                    <hr style="margin: 40px 0;">
                    <h4><%t Software.SEE_ALSO 'See also' %></h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <% loop $Component.RelatedComponents() %>
                                <a class="btn btn-default team-button" href="{$getLink}">
                                    <img src="/software/images/mascots/{$MascotRef}.png" width="50px" /><br/>
                                    {$CodeName}
                                </a>
                            <% end_loop %>
                        </div>
                    </div>
                <% end_if %>

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

                                            <% loop $Component.getMaturityTags() %>
                                            <tr>
                                                <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                                <td>
                                                    {$Top.Component.CodeName} {$getTranslatedLabel()}
                                                    <a href="#" onclick="return false;" data-trigger="focus" data-content="{$getTranslatedDescription()}" title="" data-placement="right" data-toggle="popover" data-original-title="<%t Software.WHAT_DOES_MEAN 'What does this mean?' %>">
                                                        <i class="fa fa-question-circle tag-tooltip"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{$Link}"><%t Openstack.VIEW_DETAILS 'View details' %></a>
                                                </td>
                                            </tr>
                                            <% end_loop %>

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
                                            <% loop $Component.getInfoTags() %>
                                            <tr>
                                                <td>{$getTranslatedLabel()}</td>
                                                <td>
                                                    <ul>
                                                        <li class="on" >
                                                            <i class="fa fa-circle"></i><span><%t Openstack.YES 'Yes' %></span>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <a href="{$Link}"><%t Openstack.VIEW_DETAILS 'View details' %></a>
                                                </td>
                                            </tr>
                                            <% end_loop %>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <% end_if %>
                <% end_if %>
            </div>
        </div>

        <div class="row box">
            <div class="col-sm-12">
                <h4><%t Software.DEVELOPMENT_TEAM 'Development Team' %></h4>

                <a class="btn btn-default team-button" href="{$Component.getProjectLink()}">
                    <img src="/software/images/mascots/{$Component.MascotRef}.png" width="50px" /><br/>
                    {$Component.CodeName}
                </a>
            </div>
        </div>

        <% if $Component.SupportTeams().Count %>
            <h4><%t Software.SUPPORTING_TEAMS 'Supporting Teams' %></h4>
            <div class="row box">
                <div class="col-sm-12">
                    <% loop $Component.SupportTeams() %>
                        <a class="btn btn-default team-button" href="{$getProjectLink()}">
                            <img src="/software/images/mascots/{$MascotRef}.png" width="50px" /><br/>
                            {$CodeName}
                        </a>
                    <% end_loop %>
                </div>
            </div>
        <% end_if %>

        <% if $Component.YouTubeID %>
            <div class="row project-details-ptl box">
                <div class="col-sm-12">
                    <h4>$Component.VideoTitle</h4>
                    <p>$Component.VideoDescription</p>
                    <iframe width="356" height="200" src="//www.youtube.com/embed/{$Component.YouTubeID}" frameborder="0" allowfullscreen=""></iframe>
                </div>
            </div>
        <% end_if %>

        <% if $Component.CapabilityTags().Count %>
            <div class="row project-capabilities box">
                <div class="col-sm-12">
                    <h4><%t Software.CAPABILITIES 'Capabilities' %></h4>
                    <br>
                    <% loop $Component.getSortedCapabilityTags().GroupedBy(CategoryName) %>
                        <div>
                            <% with $Children.First %>
                                <strong
                                        data-toggle="tooltip"
                                        title="{$Category.Description}"
                                        style="cursor: help"
                                >
                                    $Category.Name:
                                </strong>
                            <% end_with %>

                            <br/>
                            <% loop $Children %>
                                <% if $Description %>
                                    <a
                                            href="$Top.Link(project-navigator)/{$Top.ParentCategory.Slug}#{$Name}"
                                            style="color:$getColor()"
                                            data-toggle="tooltip"
                                            title="{$Description}"
                                    >
                                        $Name
                                    </a>
                                <% else %>
                                    <a href="$Top.Link(project-navigator)/{$Top.ParentCategory.Slug}#{$Name}" style="color:$getColor()">
                                        $Name
                                    </a>
                                <% end_if %>
                                &nbsp;
                            <% end_loop %>
                        </div>
                        <br/>
                    <% end_loop %>
                </div>
            </div>
        <% end_if %>

        <div class="row">
            <div class="col-sm-12 project-details-footnotes">
                <a href="https://opendev.org/osf/openstack-map/" target="_blank">Propose changes to this page</a>
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