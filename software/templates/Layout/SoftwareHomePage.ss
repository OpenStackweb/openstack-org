<% include SoftwareHomePage_MainNavMenu Active=0 %>
<div class="software-main-wrapper">
    <!-- Projects Subnav -->
     <div class="container">
    </div>
    <div class="container inner-software">
        <!-- Begin Page Content -->
        <div class="row">
            <div class="col-sm-10 col-sm-push-1 center">
                <h3>$IntroTitle</h3>
                <p>
                    <img src="themes/openstack/images/software/openstack-software-diagram.png" class="openstack-diagram">
                </p>
                <div class="row projects-overview">
                    <div class="col-sm-6">
                     $IntroText
                    </div>
                    <div class="col-sm-6"><a rel="shadowbox" href="//www.youtube.com/v/N90ufYN0B6U"><img class="icehouse-video" title="" src="assets/software/mitaka/demo-screenshot.jpg" alt="Mitaka Demo" /></a>
                        <h4>Mitaka OpenStack Overview (4:15)</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="lf-security-promo">
            <div class="row">
                <div class="col-sm-3 center">
                    <img class="security-badge" src="/themes/openstack/images/security/badge.png" alt="">
                </div>
                <div class="col-sm-9">
                    <h2>OpenStack security experts answer your questions</h2>
                    <p>
                        The Security Project team lives and breathes cloud security and answers the most frequently asked questions from enterprises and technologists. The Linux Foundation's Core Infrastructure Initiative has validated OpenStack passes 100% of the best practices for open source security, quality and stability and has issued its Best Practices badge.
                    </p>
                    <p>
                        <a href="/software/security/" class="security-promo-btn">Learn More <i class="fa fa-chevron-circle-right"></i></a>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10 col-sm-push-1 center">
                <hr style="margin:40px 0;">
                <h3>$IntroTitle2</h3>
                $IntroText2
            </div>
        </div>
        <div class="project-intro-boxes" id="select-use-break">
            <div class="step">
                <%t Software.SOFTWARE_STEP_ONE '1<span>st</span>' %>
            </div>
            <p>
                <%t Software.HOW_WILL_YOU_USE 'How will you be <strong>using OpenStack</strong>?' %>
            </p>
            <div class="row">
                <div class="col-sm-10 col-sm-push-1 col-md-8 col-md-push-2">
                    <div class="row">
                        <div class="col-xs-4">
                            <a href="#select-use-break" class="use-os-select" id="choose-compute"><%t Software.COMPUTE 'Compute' %></a>
                        </div>
                        <div class="col-xs-4">
                            <a href="#select-use-break" class="use-os-select" id="choose-storage"><%t Software.OBJECT_STORAGE 'Object Storage' %></a>
                        </div>
                        <div class="col-xs-4">
                            <a href="#select-use-break" class="use-os-select" id="choose-both"><%t Software.COMPUTE_OBJECT_STORAGE 'Compute +<br/>Object Storage' %></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p id="compute-description">
                                <%t Software.COMPUTE_DESCRIPTION 'Compute resources are accessible via APIs for developers building cloud applications and via web interfaces for administrators and users. The compute architecture is designed to scale horizontally on standard hardware, enabling the cloud economics companies have come to expect.' %>
                            </p>
                            <p id="storage-description">
                                <%t Software.STORAGE_DESCRIPTION 'OpenStack Object Storage can be deployed independently of a compute cloud or alongside other computing platforms. It is ideal for cost effective, scale-out storage and provides a fully distributed, API-accessible storage platform that can be integrated directly into applications or used for backup, archiving and data retention.' %>
                            </p>
                            <p id="both-description">
                                <%t Software.BOTH_DESCRIPTION 'Deployed together, Compute and Object Storage provide the full set of OpenStack core services and open APIs. In combination, the platform provides robust cloud computing capabilities with horizontal storage for managing images, snapshots, backups, virtual machines and block devices.' %>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="inbetween-project-steps">
            <i class="fa fa-long-arrow-down"></i>
        </div>
        <div class="project-intro-boxes" id="core-projects-break">
            <div class="step">
                <%t Software.SOFTWARE_STEP_TWO '2<span>nd</span>' %>
            </div>
            <p>
                <%t Software.SELECT_CORE_SERVICES 'Select the <strong>Core Services</strong> you want to use.' %>
            </p>
            <div class="row">
                <% if StorageCoreServices %>
                <div id="show-storage">
                    <% loop $StorageCoreServices %>
                    <div class="col-lg-2 col-sm-4 col-xs-6">
                        <a class="core-project-single" href="{$Top.Link}releases/{$Top.Release.Slug}/components/{$Slug}">
                            <div class="view-project-overlay">
                                <i class="fa fa-search"></i>
                            </div>
                            <div class="top">
                                <i class="fa {$IconClass}"></i>
                            </div>
                            <div class="bottom">
                                <h5>$CodeName</h5>
                                <p>$Name</p>
                            </div>
                        </a>
                    </div>
                    <% end_loop %>
                </div>
                <% end_if %>
                <% if NoneCoreServices %>
                    <% loop $NoneCoreServices %>

                            <div class="col-lg-2 col-sm-4 col-xs-6">
                                <a class="core-project-single" href="{$Top.Link}releases/{$Top.Release.Slug}/components/{$Slug}">
                                    <div class="view-project-overlay">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <div class="top">
                                        <i class="fa {$IconClass}"></i>
                                    </div>
                                    <div class="bottom">
                                        <h5>$CodeName</h5>
                                        <p>$Name</p>
                                    </div>
                                </a>
                            </div>
                    <% end_loop %>
                <% end_if %>
                <% if ComputeCoreServices %>
                <div id="show-compute">
                    <% loop $ComputeCoreServices %>
                        <div class="col-lg-2 col-sm-4 col-xs-6">
                            <a href="{$Top.Link}releases/{$Top.Release.Slug}/components/{$Slug}" class="core-project-single">
                                <div class="view-project-overlay">
                                    <i class="fa fa-search"></i>
                                </div>
                                <div class="top">
                                    <i class="fa {$IconClass}"></i>
                                </div>
                                <div class="bottom">
                                    <h5>$CodeName</h5>
                                    <p>$Name</p>
                                </div>
                            </a>
                        </div>
                    <% end_loop %>
                </div>
                <% end_if %>
            </div>
        </div>
        <div class="inbetween-project-steps">
            <i class="fa fa-long-arrow-down"></i>
        </div>
        <div class="project-intro-boxes">
            <div class="step">
                <%t Software.SOFTWARE_STEP_THREE '3<span>rd</span>' %>
            </div>
            <p>
                <%t Software.SOFTWARE_ADD_ON 'Add on any <strong>Optional Services</strong> to enhance things.' %>
            </p>
            <% if OptionalServices %>
            <div class="optional-services-wrapper">
                <!-- Row 1 -->
                <div class="row">
                    <!-- Sahara -->
                    <% loop $OptionalServices %>
                    <div class="col-sm-4 col-xs-6">
                        <a href="{$Top.Link}releases/{$Top.Release.Slug}/components/{$Slug}" class="optional-services-single">
                            <div class="view-project-overlay">
                                <i class="fa fa-search"></i>
                            </div>
                            <div class="left">
                                <i class="fa fa-cog"></i>
                            </div>
                            <div class="right">
                                <h5>$CodeName</h5>
                                <p>$Name</p>
                            </div>
                        </a>
                    </div>
                    <% end_loop %>
                </div>
                <!-- <div class="optional-services-bottom"></div> -->
            </div>
            <% end_if %>
            <div class="more-optional-projects">
                <p>
                    <a href="$Top.Link(project-navigator)"><%t Software.AND_MANY_MORE '...and many more' %></a>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <a href="$Top.Link(project-navigator)" class="red-btn-lrg"><%t Software.BROWSE_ALL_PROJECTS 'Browse All OpenStack Projects' %></a>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
</div>
<!-- Software Tabs UI -->
