<script>
    var components = $getComponentsByCategoryJSON;
    var releases   = $getReleases;
    components.max_maturity_points = $Top.getMaxAllowedMaturityPoints;
    var tileMode = (window.location.hash == '#tiles');
</script>
<% include SoftwareHomePage_MainNavMenu Active=1 %>
<div class="software-main-wrapper">
    <!-- Projects Subnav -->
    <div class="container">
        <div class="row outer-all-projects-subnav">
            <form class="all-projects-search-form">
                <openstack-components-free-search></openstack-components-free-search>
                <openstack-releases-ddl releases="{ releases }"></openstack-releases-ddl>
                <div class="col-xs-1 all-projects-filter-link">
                    <i title="" data-placement="right" data-toggle="tooltip" id="toggle-all-projects-filters" class="fa fa-filter" data-original-title="Toggle Advanced Filters"></i>
                </div>
            </form>
        </div>
        <openstack-components-filters max_maturity_points="{ components.max_maturity_points }"></openstack-components-filters>
    </div>
    <div class="container inner-software">
        <!-- Begin Page Content -->
        <div class="row">
            <div class="col-sm-12 all-projects-wrapper">
                <h3><%t Software.BROWSE_ALL_OS_PROJECTS 'Browse All OpenStack Projects' %></h3>
                <p>
                    <%t Software.PROJECT_NAVIGATOR_DESCRIPTION 'The Project Navigator is aimed at helping users make informed decisions about how to consume the software. Data used to power the Project Navigator website is provided by the OpenStack Technical and User Committees.' %>
                </p>
                <p></p>
            </div>
        </div>
        <project-services base_url="{$Top.Link}" groups="{ components.grouped_components }" max_maturity_points="{ components.max_maturity_points }" tilemode="{ tileMode }">
        </project-services>
        <!-- End Page Content -->
    </div>
</div>

$ModuleJS('software_all_projects')
$ModuleCSS('software_all_projects')

<!-- Software Tabs UI -->
