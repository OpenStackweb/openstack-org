<div class="container software">
    <div class="row">
        <div class="col-sm-12">
            <h1>Software</h1>
        </div>
    </div>
</div>
<!-- Projects Tabs -->
<div class="software-tab-wrapper">
    <div class="container">
        <ul class="nav nav-tabs project-tabs">
            <li class=""><a href="$Top.Link">Overview</a></li>
            <li class="active"><a href="$Top.Link(all-projects)">All Projects</a></li>
        </ul>
    </div>
</div>
<div class="software-tab-dropdown">
    <div class="dropdown">
        <button aria-expanded="true" aria-haspopup="true" data-toggle="dropdown" id="dropdownMenu1" type="button" class="dropdown-toggle projects-dropdown-btn">
            All Projects
            <i class="fa fa-caret-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li class=""><a href="$Top.Link">Overview</a></li>
            <li class="active"><a href="$Top.Link(all-projects)">All Projects</a></li>
        </ul>
    </div>
</div>
<div class="software-main-wrapper">
    <!-- Projects Subnav -->
    <div class="container">
        <div class="outer-project-back">
            <a href="$Top.Link(all-projects)"><i class="fa fa-chevron-left"></i> Back to Project Browser</a>
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
                        <a href="http://docs.openstack.org/{$Release.Slug}/install-guide/install/apt/content/{$Component.InstallationGuideDocName}" target="_blank">View the install guide</a>
                    </p>
                    <% end_if %>
                    <p>
                        <a href="#" target="_blank">Search the marketplace</a>
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
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Features</th>
                                    <th></th>
                                    <th>Tag Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Is there an install guide for this project guide (at docs.openstack.org)?
                                        <i data-content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta dolor minus quaerat provident dolorum omnis mollitia delectus qui animi deleniti, sunt sit est nesciunt aspernatur quis quibusdam tempora doloribus et." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What does this mean?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.HasInstallationGuide %>class="on"<% end_if %>>
                                                <% if $Component.HasInstallationGuide %>
                                                <i class="fa fa-check-circle"></i><span>Yes</span>
                                                <% else %>
                                                <i class="fa fa-times-circle"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <% if $Component.HasInstallationGuide %>
                                        <a href="http://docs.openstack.org/{$Release.Slug}/install-guide/install/apt/content/{$Component.InstallationGuideDocName}">View Install Guide</a>
                                        <% else %>
                                        &nbsp;
                                        <% end_if %>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Is this project included in the compute starter kit?
                                        <i data-content="The Compute Starter Kit is a common starting point for a Compute oriented OpenStack cloud that can be expanded over time to include more of the OpenStack universe." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What is the Compute Starter Kit?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li class="on">
                                                <i class="fa fa-check-circle"></i><span>Yes</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/starter-kit_compute.html">View Tag Details</a></td>
                                </tr>
                                <tr>
                                    <td>Is this project recommended by the Technical Committee (TC)?
                                        <i data-content="This tag is used to indicate the projects the TC recommends to the OpenStack Foundation Board as candidates for trademark use under the OpenStack Foundation trademark policy." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What is the Compute Starter Kit?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li  <% if $Component.TCApprovedRelease %>class="on"<% end_if %>>
                                                <% if $Component.TCApprovedRelease %>
                                                    <i class="fa fa-check-circle"></i><span>Yes</span>
                                                <% else %>
                                                    <i class="fa fa-times-circle"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/tc-approved-release.html">View Tag Details</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        How is this project released?
                                        <i data-content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta dolor minus quaerat provident dolorum omnis mollitia delectus qui animi deleniti, sunt sit est nesciunt aspernatur quis quibusdam tempora doloribus et." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="How are projects released?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.ReleaseMileStones %>class="on"<% end_if %>>
                                                <a href="http://governance.openstack.org/reference/tags/release_cycle-with-milestones.html">
                                                    <i class="fa <% if $Component.ReleaseMileStones %>fa-check-circle<% else %>fa-times-circle<% end_if %>"></i><span>Cycle with milestones</span></a>
                                            </li>
                                            <li <% if $Component.ReleaseCycleWithIntermediary %>class="on" <% end_if %>>
                                                <a href="http://governance.openstack.org/reference/tags/release_cycle-with-intermediary.html">
                                                    <i class="fa <% if $Component.ReleaseCycleWithIntermediary %>fa-check-circle<% else %>fa-times-circle<% end_if %>"></i><span>Cycle with intermediary</span>
                                                </a>
                                            </li>
                                            <li <% if $Component.ReleaseIndependent %>class="on" <% end_if %>>
                                                <a href="http://governance.openstack.org/reference/tags/release_independent.html">
                                                    <i class="fa <% if $Component.ReleaseIndependent %>fa-check-circle<% else %>fa-times-circle<% end_if %>"></i><span>Independent</span></a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/release_cycle-with-milestones.html">View Tag Details</a></td>
                                </tr>
                                <tr>
                                    <td>Number of software development kits (SDKs) which support this project.
                                        <i data-content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta dolor minus quaerat provident dolorum omnis mollitia delectus qui animi deleniti, sunt sit est nesciunt aspernatur quis quibusdam tempora doloribus et." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What does this mean?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li class="on">
                                                <i class="fa fa-check-circle"></i><span>15</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/starter-kit_compute.html">View Tag Details</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        Percentage of deployments using this project in production environments.
                                        <i data-content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta dolor minus quaerat provident dolorum omnis mollitia delectus qui animi deleniti, sunt sit est nesciunt aspernatur quis quibusdam tempora doloribus et." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="How are projects released?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li class="on">
                                                <a href="http://governance.openstack.org/reference/tags/release_cycle-with-milestones.html">
                                                    <i class="fa fa-check-circle"></i><span> {$Component.Adoption}%</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/release_cycle-with-milestones.html">View Tag Details</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        Has this project team acheived corporate diversity?
                                        <i data-content="A project with this tag has achieved a level of diversity in the affiliation of contributors that is indicative of a healthy collaborative project. This tag exists in the ‘team’ category, which as the name implies, covers information about the team itself. Another example of a tag that could exist in this category is one that conveys the size of the team that is actively contributing." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What does this mean?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li <% if $Component.HasTeamDiversity %>class="on" <% end_if %>>
                                                <% if $Component.HasTeamDiversity %>
                                                <i class="fa fa-check-circle"></i><span>Yes</span>
                                                <% else %>
                                                <i class="fa fa-times-circle"></i><span>No</span>
                                                <% end_if %>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/vulnerability_managed.html">View Tag Details</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        Existence and quality of packages for this project in distributions like Red Hat/Fedora, Ubuntu	6 and openSUSE/SLES.
                                        <i data-content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta dolor minus quaerat provident dolorum omnis mollitia delectus qui animi deleniti, sunt sit est nesciunt aspernatur quis quibusdam tempora doloribus et." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="How are projects released?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li class="on">
                                                <a href="https://review.openstack.org/#/c/186633"><i class="fa fa-check-circle"></i><span>Good</span></a>
                                            </li>
                                            <li>
                                                <a href="https://review.openstack.org/#/c/186633"><i class="fa fa-times-circle"></i><span>No</span></a>
                                            </li>
                                            <li>
                                                <a href="https://review.openstack.org/#/c/186633"><i class="fa fa-times-circle"></i><span>Warning</span></a>
                                            </li>
                                            <li>
                                                <a href="https://review.openstack.org/#/c/186633"><i class="fa fa-times-circle"></i><span>Beginning</span></a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="https://review.openstack.org/#/c/186633">View Tag Details</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        Is vulnerability managed?
                                        <i data-content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta dolor minus quaerat provident dolorum omnis mollitia delectus qui animi deleniti, sunt sit est nesciunt aspernatur quis quibusdam tempora doloribus et." title="" data-placement="right" data-toggle="popover" class="fa fa-question-circle tag-tooltip" data-original-title="What does this mean?"></i>
                                    </td>
                                    <td>
                                        <ul>
                                            <li class="on">
                                                <i class="fa fa-check-circle"></i><span>Yes</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a href="http://governance.openstack.org/reference/tags/vulnerability_managed.html">View Tag Details</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 project-details-chart-section">
                <h4>Contributions to $Component.CodeName</h4>
                <img alt="" src="http://cdn.getforge.com/os-new-software.getforge.io/1443794583/images/projects/contributor-chart.png" class="contributor-chart">
            </div>
        </div>
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
                            <a target="_blank" href="community/members/profile/{$Component.LatestReleasePTL.ID}"><img alt="OpenStack Profile" src="http://cdn.getforge.com/os-new-software.getforge.io/1443794583/images/projects/ptl-openstack.png"></a>
                            <% if $Component.LatestReleasePTL.TwitterName %>
                            <a target="_blank" href="https://twitter.com/{$Component.LatestReleasePTL.TwitterName}"><img alt="Twitter Profile" src="http://cdn.getforge.com/os-new-software.getforge.io/1443794583/images/projects/ptl-twitter.png"></a>
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
        <div class="row project-details-other">
            <div class="col-sm-6">
                <h4>Most Active Contributors by Company</h4>
                <ul>
                    <li><a href="#">Rackspace</a></li>
                    <li><a href="#">Redhat</a></li>
                    <li><a href="#">IBM</a></li>
                    <li><a href="#">Mirantis</a></li>
                    <li><a href="#">HP</a></li>
                    <li><a href="#">VMware</a></li>
                    <li><a href="#">Intel</a></li>
                    <li><a href="#">NEC</a></li>
                    <li><a href="#">Huawai</a></li>
                    <li><a href="#">Yahoo!</a></li>
                </ul>

                <h4>Most Active Individual Contributors</h4>
                <ul>
                    <li><a href="#">John Garbutt</a></li>
                    <li><a href="#">Gary Kotton</a></li>
                    <li><a href="#">Matt Riedemann</a></li>
                    <li><a href="#">Daniel Berrange</a></li>
                    <li><a href="#">Jay Pipes</a></li>
                    <li><a href="#">Alex Xu (He Jie Xu)</a></li>
                    <li><a href="#">Dan Smith</a></li>
                    <li><a href="#">Sean Dague</a></li>
                    <li><a href="#">Ken’ichi Ohmichi</a></li>
                    <li><a href="#">jichenjc</a></li>
                </ul>

                <h4>Related Content</h4>
                <ul>
                    <li><a href="#">Upgrading Nova to Kilo with minimal downtime</a></li>
                    <li><a href="#">ebay in Production: Migration from Nova-Network to Neutron</a></li>
                    <li><a href="#">Nova Updates - Kilo Edition</a></li>
                </ul>
            </div>
            <div class="col-sm-6 right">
                <h4>Project History</h4>
                <div class="project-timeline">
                    <ul>
                        <li class="timeline-future">
                            <a href="http://docs.openstack.org/releases/releases/liberty.html">
                                Next Release (Liberty): October 15, 2015
                            </a>
                        </li>
                        <li class="timeline-current">
                            <a href="http://docs.openstack.org/releases/releases/kilo.html">
                                Version 2015.1.1 (Kilo) - LATEST RELEASE
                            </a>
                        </li>
                        <li>
                            <a href="http://docs.openstack.org/releases/releases/juno.html">
                                Version 2014.2.3 (Juno)
                            </a>
                        </li>
                        <li>
                            <a href="http://docs.openstack.org/releases/releases/icehouse.html">
                                Version 2014.1.5 (Icehouse)
                            </a>
                        </li>
                        <li>
                            <a href="http://docs.openstack.org/releases/releases/havana.html">
                                Version 2013.2.4 (Havana)
                            </a>
                        </li>
                        <li>
                            <a href="http://docs.openstack.org/releases/releases/grizzly.html">
                                Version 2013.1.5 (Grizzly)
                            </a>
                        </li>
                        <li>
                            <a href="http://docs.openstack.org/releases/releases/folsom.html">
                                Version 2012.2.4 (Folsom)
                            </a>
                        </li>
                        <li>
                            <a href="http://docs.openstack.org/releases/releases/essex.html">
                                Version 2012.1.3 (Essex)
                            </a>
                        </li>
                        <li>
                            <a href="http://docs.openstack.org/releases/releases/diablo.html">
                                Version 2011.3.1 (Diablo)
                            </a>
                        </li>
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
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis quaerat, error cumque ducimus nesciunt, eligendi aliquid. Culpa iure, magni quasi quaerat quia commodi quam! Veritatis hic maxime, sequi odio quia!
                        </p>
                        <hr>
                        <p>
                            <strong>Adoption</strong> is nulla ipsam veniam quis eos, voluptatibus veritatis magni, molestias magnam doloribus!
                        </p>
                        <p>
                            <strong>Maturity</strong> comes from looking at {$Top.getMaxAllowedMaturityPoints} distinct tags. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae non nulla odio expedita itaque, soluta assumenda a saepe omnis illum earum officiis aliquid eum error. Ducimus accusantium quod, debitis obcaecati.
                        </p>
                        <p>
                            <strong>Age</strong> is the age of the project, consisting of maxime placeat quasi, eos, obcaecati blanditiis eaque cum cumque quaerat, harum dolorem magnam saepe quam.
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