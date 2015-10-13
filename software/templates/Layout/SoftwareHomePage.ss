<!-- Software Tabs UI -->
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
            <li class="active"><a href="$Top.Link">Overview</a></li>
            <li class=""><a href="$Top.Link(all-projects)">All Projects</a></li>
        </ul>
    </div>
</div>
<div class="software-tab-dropdown">
    <div class="dropdown">
        <button class="dropdown-toggle projects-dropdown-btn" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            Overview
            <i class="fa fa-caret-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li class="active"><a href="$Top.Link">Overview</a></li>
            <li class=""><a href="$Top.Link(all-projects)">All Projects</a></li>
        </ul>
    </div>
</div>
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
                    <div class="col-sm-6"><a rel="shadowbox" href="//www.youtube.com/v/3UINKsGw95A"><img src="themes/openstack/images/software/kilo-screencast.png"/></a>
                        <h4>OpenStack Overview (5:14)</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10 col-sm-push-1 center">
                <hr style="margin:40px 0;">
                <h3>$IntroTitle2</h3>
                <p>
                    $IntroText2
                </p>
            </div>
        </div>
        <div class="project-intro-boxes" id="select-use-break">
            <div class="step">
                1<span>st</span>
            </div>
            <p>
                How will you be <strong>using OpenStack</strong>?
            </p>
            <div class="row">
                <div class="col-sm-10 col-sm-push-1 col-md-8 col-md-push-2">
                    <div class="row">
                        <div class="col-xs-4">
                            <a href="#select-use-break" class="use-os-select" id="choose-compute">Compute</a>
                        </div>
                        <div class="col-xs-4">
                            <a href="#select-use-break" class="use-os-select" id="choose-storage">Object Storage</a>
                        </div>
                        <div class="col-xs-4">
                            <a href="#select-use-break" class="use-os-select" id="choose-both">Compute +<br/>Object Storage</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p id="compute-description">
                                Compute is lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis rerum placeat molestias harum facere, iusto necessitatibus, quia aperiam natus, nostrum sit temporibus. Minus repudiandae ipsum repellat aliquam, veritatis quae autem.
                            </p>
                            <p id="storage-description">
                                Object Storage is lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis rerum placeat molestias harum facere, iusto necessitatibus, quia aperiam natus, nostrum sit temporibus. Minus repudiandae ipsum repellat aliquam, veritatis quae autem.
                            </p>
                            <p id="both-description">
                                Compute + Object Storage is lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis rerum placeat molestias harum facere, iusto necessitatibus, quia aperiam natus, nostrum sit temporibus. Minus repudiandae ipsum repellat aliquam, veritatis quae autem.
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
                2<span>nd</span>
            </div>
            <p>
                Select the <strong>Core Services</strong> you want to use.
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
                3<span>rd</span>
            </div>
            <p>
                Add on any <strong>Optional Services</strong> to enhance things.
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
                    <a href="$Top.Link(all-projects)">...and many more</a>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <a href="$Top.Link(all-projects)" class="red-btn-lrg">Browse All OpenStack Projects</a>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
</div>
<!-- Software Tabs UI -->