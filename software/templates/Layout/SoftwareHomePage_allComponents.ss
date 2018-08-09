<script>
    var components = $getComponentsByCategoryJSON($CategoryId);
    var releases   = $getReleases;
    var releaseId  = '{$getDefaultRelease().getSlug().JS}';
    components.max_maturity_points = $Top.getMaxAllowedMaturityPoints;
    var tileMode = (window.location.hash == '#tiles');

</script>

<% include SoftwareHomePage_MainNavMenu Active=$CategoryId %>

<div class="software-main-wrapper">
    <!-- Projects Subnav -->
    <%--<div class="container">
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
    </div>--%>

    <!-- Begin Page Content -->
    <% if $CategoryDepth > 2 %>
        <openstack-category-nav groups="{ components.subcategories }"></openstack-category-nav>

        <div class="container inner-software">
            <project-services-with-nav base_url="{$Top.Link}" groups="{ components.subcategories }" release_id="{ releaseId }" ></project-services-with-nav>
        </div>
    <% else %>
        <div class="container inner-software">
            <project-services base_url="{$Top.Link}" groups="{ components.subcategories }" category="{ components.category }" release_id="{ releaseId }" ></project-services>
        </div>
    <% end_if %>
    <!-- End Page Content -->


</div>

$ModuleJS('software_all_projects')
$ModuleCSS('software_all_projects')

<!-- Software Tabs UI -->
