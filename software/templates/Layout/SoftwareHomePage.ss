<% include SoftwareHomePage_MainNavMenu Active='overview' %>

<div class="software-main-wrapper">
    <!-- Projects Subnav -->
    <% include SoftwareHomePage_SubNavMenu Active=1 %>

    <div class="container inner-software" role="tabpanel" id="overview">
        <!-- Begin Page Content -->
        <div class="row">
            <div class="col-sm-10 col-sm-push-1 center">
                <h3>$IntroTitle</h3>
                <p>
                    <img class="overview-diagram" width="100%" src="/software/images/diagram/overview-diagram.svg" onerror="this.onerror=null; this.src=/software/images/diagram/overview-diagram.png" alt="OpenStack Cloud Software">
                </p>
                <div class="row projects-overview">
                    <div class="col-sm-12">
                     $IntroText
                    </div>
                </div>
            </div>
        </div>
        <!-- 
        <hr/>

        <div class="project-category-section">
            <h3 class="header-center">What can OpenStack Automate?</h3>
            <div class="row">
                <% loop getComponentCategories() %>
                <div class="col-md-4">
                    <a href="/software/project-navigator/openstack-components/">
                        <div class="project-category-tile">
                            <h4>{$Name}</h4>
                            <p class="number-of-projects">
                                {$Count} OpenStack Projects
                            </p>
                        </div>
                    </a>
                </div>
                <% end_loop %>
            </div>
        </div>
        -->
        <div class="row">
            <div class="col-sm-12">
                <a href="$Top.Link(project-navigator)" class="red-btn-lrg">Browse All OpenStack Projects</a>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="starter-pack">
                <h3 class="header-center">Popular Project Set</h3>
                <ul>
                    <li class="nova">
                        <a href="/software/releases/queens/components/nova">
                            <h4>Nova</h4>
                            <p>Compute</p>
                        </a>
                    </li>
                    <li class="neutron">
                        <a href="/software/releases/queens/components/neutron">
                            <h4>Neutron</h4>
                            <p>Networking</p>
                        </a>
                    </li>                     
                    <li class="swift">
                        <a href="/software/releases/queens/components/swift">
                            <h4>Swift</h4>
                            <p>Object Storage</p>
                        </a>
                    </li>
                    <li class="glance">
                        <a href="/software/releases/queens/components/glance">
                            <h4>Glance</h4>
                            <p>Image Service</p>
                        </a>
                    </li>
                    <li class="keystone">
                        <a href="/software/releases/queens/components/keystone">
                            <h4>Keystone</h4>
                            <p>Identity Service</p>
                        </a>
                    </li>          
                    <li class="cinder">
                        <a href="/software/releases/queens/components/cinder">
                            <h4>Cinder</h4>
                            <p>Block Storage</p>
                        </a>
                    </li>                                                                                                      
                </ul>
            </div>
        </div>

        <div class="row">

            <div class="workloads-section">

            <h3 class="header-center">Sample OpenStack Configurations</h3>

                <ul>
                    <li> <a href="/software/sample-configs#web-applications"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> Web Applications</a></li>    

                    <li> <a href="/software/sample-configs#big-data"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> Big Data</a></li>

                    <li> <a href="/software/sample-configs#ecommerce"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> eCommerce</a></li>

                    <li> <a href="/software/sample-configs#video-processing-and-content-delivery"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> Video Processing and Content Delivery</a></li>

                    <li> <a href="/software/sample-configs#high-throughput-computing"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> High Throughput Computing</a></li>

                    <li> <a href="/software/sample-configs#container-optimized"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> Container Optimized</a></li>
                    
                    <li> <a href="/software/sample-configs#web-hosting"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> Web Hosting</a></li>

                    <li> <a href="/software/sample-configs#public-cloud"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> Public Cloud</a></li>

                    <li> <a href="/software/sample-configs#compute-starter-kit"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> Compute Starter Kit</a></li>

                    <li> <a href="/software/sample-configs#dbaas"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png"> DBaaS</a></li>
                </ul>

            </div>
        </div>

        <hr/>

        <p>Find more products, support, and services in the <a href="/marketplace/">OpenStack Marketplace</a></p>


        <!-- End Page Content -->
    </div>

</div>
<!-- Software Tabs UI -->
