<% include SoftwareHomePage_MainNavMenu Active=0 %>
<div class="software-main-wrapper">
    <!-- Projects Subnav -->
    <div class="container"></div>
    <div class="container inner-software">
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

        <hr/>

        <div class="project-category-section">
            <h3 class="header-center">What can OpenStack Automate?</h3>
            <div class="row">
                <% loop getComponentCategories() %>
                <div class="col-md-4">
                    <a href="$Top.Link(project-navigator)#{$Id}">
                        <div class="project-category-tile">
                            <img class="category-icon" width="114px" src="/software/images/icons/{$MascotClass}.svg" onerror="this.onerror=null; this.src=/software/images/icons/{$MascotClass}.png" alt="OpenStack Cloud Software">
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

        <div class="row">
            <div class="col-sm-12">
                <a href="$Top.Link(project-navigator)" class="red-btn-lrg">Browse All OpenStack Projects</a>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="workloads-section">

            <h3 class="header-center">Sample OpenStack Configurations</h3>

                <ul>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> Web Applications</a></li>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> eCommerce</a></li>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> Video Processing and Content Delivery</a></li>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> High Throughput Computing</a></li>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> Container Optimized</a></li>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> Web Hosting</a></li>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> Public Cloud</a></li>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> Compute Starter Kit</a></li>
                    <li> <a href="#"> <img class="workloads-icon" src="/software/images/icons/sample-config.svg" onerror="this.onerror=null; this.src=/software/images/icons/sample-config.png" alt="OpenStack Cloud Software"> DBaaS</a></li>
                </ul>

            </div>
        </div>

        <hr/>

        <p>Find more products, support, and services in the <a href="/marketplace/">OpenStack Marketplace</a></p>


        <!-- End Page Content -->
    </div>
</div>
<!-- Software Tabs UI -->
