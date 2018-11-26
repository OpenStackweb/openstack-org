<% include SoftwareHomePage_MainNavMenu Active=1 %>
<div class="software-main-wrapper">
    <div class="container">
        <div class="outer-project-back">
            <a href=""><i class="fa fa-chevron-left"></i> Back to Project Navigator </a>
        </div>
    </div>
    <div class="container inner-software">
        <!-- Begin Page Content -->
        <div class="row project-details-intro">
            <div class="col-md-2 col-sm-2">
                <img src="/software/images/mascots/openstack-charms.png" width="100%">
            </div>
            <div class="col-md-10 col-sm-10">
                <h2 data-toggle="tooltip" title="openstack-map/master -> name">OpenStack-Charms</h2>
                <h4 data-toggle="tooltip" title="openstack-map/master -> title">Deploys OpenStack in containers using Charms and Juju</h4>
                <div class="project-intro-links">
                    <p>
                        <a href="" target="_blank" data-toggle="tooltip" title="openstack-map/master -> docs-title & docs-url">
                            <%-- do not separate icon from label --%>
                            <i class="fa fa-book" aria-hidden="true" style="margin-right: 8px"></i><span>Docs</span>
                        </a>
                    </p>
                    <p>
                        <a href="" target="_blank" data-toggle="tooltip" title="openstack-map/master -> download-title & download-url">
                            <%-- do not separate icon from label --%>
                            <i class="fa fa-cloud-download" aria-hidden="true" style="margin-right: 8px"></i><span>Find OpenStack Charms in the Charm Store</span>
                        </a>
                    </p>
                    <p>
                        <a href="" target="_blank" data-toggle="tooltip" title="ops-docs-install-guide.json">
                            View the install guide
                        </a>
                    </p>
                    <p data-toggle="tooltip" title="openstack-map/master -> since">
                        First appeared in Pike
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 about-project-details">
                <h4>About this project</h4>
                <p data-toggle="tooltip" title="openstack-map/master -> desc">
                    Collection of Charms to deploy OpenStack using the Juju framework.
                </p>

                <hr style="margin: 40px 0;">
                <h4>Related Links</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <a class="component-link" href="" data-toggle="tooltip" title="openstack-map/master -> links: label=>link">
                            Juju Solutions for OpenStack
                        </a>
                        <span style="margin-right:10px">|</span>
                        <a class="component-link" href="" data-toggle="tooltip" title="openstack-map/master -> links: label=>link">
                            Install OpenStack
                        </a>
                        <span style="margin-right:10px">|</span>
                        <a class="component-link" href="" data-toggle="tooltip" title="openstack-map/master -> links: label=>link">
                            Install single-server OpenStack with conjure-up
                        </a>
                    </div>
                </div>

                <hr style="margin: 40px 0;">
                <h4>Suporting Teams</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <a class="component-link" href="" data-toggle="tooltip" title="openstack-map/master -> support-teams: label=>link">
                            I18n
                        </a>
                        <span style="margin-right:10px">|</span>
                        <a class="component-link" href="" data-toggle="tooltip" title="openstack-map/master -> support-teams: label=>link">
                            Docs
                        </a>
                    </div>
                </div>

                <hr style="margin: 40px 0;">
                <h4>Project details</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive project-tags-table table-hover">
                            <table class="table maturity-table">
                                <thead>
                                <tr>
                                    <th colspan="2">Maturity Indicators</th>
                                    <th>Tag Details</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr data-toggle="tooltip" title="legacy, only show if data in DB">
                                    <td class="maturity">83%</td>
                                    <td>
                                        of deployments using this project in production environments.
                                        <a href="#" onclick="return false;" data-html="true" data-trigger="focus" data-content="Adoption data is derived from the latest <a href='//www.openstack.org/user-survey'>user survey</a>." title="" data-placement="right" data-toggle="popover" data-original-title="How is this calculated?"><i class="fa fa-question-circle tag-tooltip"></i></a>
                                    </td>
                                    <td><a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-production-use.rst">View Details</a></td>
                                </tr>

                                <tr data-toggle="tooltip" title="legacy, only show if data in DB">
                                    <td class="maturity">11</td>
                                    <td>software development kits (SDKs) support OpenStack-Charms </td>
                                    <td>
                                        <a href="https://github.com/openstack/ops-tags-team/blob/master/descriptions/ops-sdk-support.rst">
                                            View Details
                                        </a>
                                    </td>
                                </tr>


                                <tr data-toggle="tooltip" title="legacy, only show if data in DB">
                                    <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                    <td> OpenStack-Charms is included in the install guide.</td>
                                    <td>
                                        <a href="http://docs.openstack.org/project-install-guide/" target="_blank">
                                            View the install guide
                                        </a>
                                    </td>
                                </tr>

                                <tr data-toggle="tooltip" title="projects.yaml -> tags">
                                    <td class="maturity"><i class="fa fa-check" aria-hidden="true"></i></td>
                                    <td>
                                        OpenStack-Charms follows standard deprecation
                                        <a href="#" onclick="return false;" data-trigger="focus" data-content="The “assert:follows-standard-deprecation” tag asserts that the project will follow standard feature deprecation rules" title="" data-placement="right" data-toggle="popover" data-original-title="What does this mean?">
                                            <i class="fa fa-question-circle tag-tooltip"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="">View details</a>
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
                                    <th>Project Info</th>
                                    <th></th>
                                    <th>Tag Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        How is this project released?
                                        <a href="#" onclick="return false;" data-trigger="focus" data-content="OpenStack development happens on a six-month cycle. Projects can choose to release on this cycle with oversight of the release management team, or to release independently of the cycle." title="" data-placement="right" data-toggle="popover" data-original-title="How are projects released?"><i class="fa fa-question-circle tag-tooltip"></i></a>
                                    </td>
                                    <td>
                                        <ul>
                                            <li class="on" data-toggle="tooltip" title="deliverables -> cycle-with-milestones">
                                                <a target="_blank" href="https://releases.openstack.org/reference/release_models.html#cycle-with-milestones">
                                                    <i class="fa fa-circle"></i><span>Cycle with milestones</span>
                                                </a>
                                            </li>
                                            <li class="on" data-toggle="tooltip" title="deliverables -> cycle-with-intermediary">
                                                <a target="_blank" href="https://releases.openstack.org/reference/release_models.html#cycle-with-intermediary">
                                                    <i class="fa fa-circle"></i><span>Cycle with intermediary</span>
                                                </a>
                                            </li>
                                            <li class="on" data-toggle="tooltip" title="deliverables -> cycle-trailing">
                                                <a target="_blank" href="https://releases.openstack.org/reference/release_models.html#cycle-trailing">
                                                    <i class="fa fa-circle"></i><span>Trailing</span>
                                                </a>
                                            </li>
                                            <li class="on" data-toggle="tooltip" title="deliverables -> independant">
                                                <a target="_blank" href="https://releases.openstack.org/reference/release_models.html#independent">
                                                    <i class="fa fa-circle"></i><span>Independent</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td><a target="_blank" href="https://releases.openstack.org/reference/release_models.html">View details</a></td>
                                </tr>
                                <tr data-toggle="tooltip" title="projects.yaml -> tags -> info">
                                    <td>Label</td>
                                    <td>
                                        <ul>
                                            <li class="on" >
                                                <i class="fa fa-circle"></i><span>Yes</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <a href="">View details</a>
                                    </td>
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
                <h4 data-toggle="tooltip" title="http://stackalytics.com/api/1.0/stats/timeline">Contributions to OpenStack-Charms</h4>
                <div style="width: 100%; height: 120px; margin-top: 15px; position: relative;" id="timeline">
                    TIMELINE
                </div>
            </div>
        </div>
        <div class="row project-details-ptl" data-toggle="tooltip" title="projects.yaml -> ptl">
            <div class="col-sm-12">
                <h4>PTL for Latest Release</h4>
            </div>
            <div class="row">
                <div class="ptl-left">
                    <img alt="" src="https://www.openstack.org/assets/profile-images/_resampled/SetWidth100-BryantJayx07.jpg" class="ptl-bio-pic">
                    <div class="ptl-details">
                        <h4>Jay Bryant</h4>
                        <p>
                            Lenovo Inc, Cloud Storage Lead
                        </p>
                        <p>
                            <a target="_blank" href="community/members/profile">
                                <img alt="OpenStack Profile" src="themes/openstack/images/foundation-staff/icon_openstack.png"></a>
                            <a target="_blank" href="https://twitter.com/">
                                <img alt="Twitter Profile" src="themes/openstack/images/foundation-staff/icon_twitter.png">
                            </a>
                        </p>
                    </div>
                </div>
                <div class="project-details-ptl-bio">
                    <p>
                        In January of 2017, I took the opportunity to act of the Cloud Storage Lead for Lenovo's Cloud Technology Center.  In this new role I am working to develop Lenovo's storage offerings and how they can best integrate with OpenStack.  Through this new role I am working to increase Lenovo's presence in the OpenStack community and looking to continue to enhance enterprise adoption of OpenStack as a cloud solution.
                    </p>
                </div>
            </div>
        </div>
        <div class="row project-details-ptl" data-toggle="tooltip" title="components -> video">
            <div class="col-sm-12">
                <h4>Video Title</h4>
                <p>Video Desc</p>
                <iframe width="356" height="200" src="//www.youtube.com/embed/8kADjGCuSVI" frameborder="0" allowfullscreen=""></iframe>
            </div>
        </div>
        <div class="row project-details-other">
            <div class="col-sm-6">
                <h4 data-toggle="tooltip" title="http://stackalytics.com/api/1.0/stats/companies">Most Active Contributors by Company</h4>
                <ul>
                    <li>Name 1</li>
                    <li>Name 2</li>
                    <li>Name 3</li>
                    <li>Name 4</li>
                </ul>
                <h4 data-toggle="tooltip" title="CMS">Related Content</h4>
                <ul>
                    <li><a href="" target="_blank">Title 1</a></li>
                    <li><a href="" target="_blank">Title 2</a></li>
                    <li><a href="" target="_blank">Title 3</a></li>
                </ul>
            </div>
            <div class="col-sm-6 right">
                <h4 data-toggle="tooltip" title="http://stackalytics.com/api/1.0/stats/engineers">Most Active Individual Contributors</h4>
                <ul>
                    <li>Name 1</li>
                    <li>Name 2</li>
                    <li>Name 3</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 project-details-footnotes">
                <a href="https://git.openstack.org/cgit/openstack/openstack-map" target="_blank">Propose changes to this page</a> | <a target="_blank" href="http://stackalytics.com"><%t Software.CHARTS_ATTR 'Statistics and charts provided by stackalytics.com' %></a>
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
