<script>
    var components = $getComponentsByCategoryJSON($CategorySlug);
    var capabilities = $getComponentCapabilitiesJSON();
    var releases   = $getReleases;
    var releaseId  = '{$getDefaultRelease().getSlug().JS}';
    components.max_maturity_points = $Top.getMaxAllowedMaturityPoints;
    var tileMode = (window.location.hash == '#tiles');

</script>

<% include SoftwareHomePage_MainNavMenu Active=$CategoryId %>

<div class="software-main-wrapper">
    <!-- Begin Page Content -->
    <% if $CategoryDepth > 2 %>
        <% if $ShowSubnav == 1 %>
            <openstack-category-nav groups="{ components.subcategories }"></openstack-category-nav>
        <% end_if %>

        <div class="container inner-software">
            <project-services-with-nav base_url="{$Top.Link}" groups="{ components.subcategories }" release_id="{ releaseId }" ></project-services-with-nav>
        </div>
    <% else %>
        <div class="container inner-software">
            <project-services base_url="{$Top.Link}" groups="{ components.subcategories }" category="{ components.category }" release_id="{ releaseId }" ></project-services>
            <% if CategorySlug == 'deployment-tools' %>
                <p class="deployment-tools-footnote">
                    You can find 3rd-party distributions, appliances and
                    deployment tools for OpenStack in the
                    <a href="/marketplace"}>Marketplace</a>.
                </p>
            <% end_if %>
        </div>
    <% end_if %>
    <!-- End Page Content -->

</div>

$ModuleJS('software_all_projects')
$ModuleCSS('software_all_projects')

<!-- Software Tabs UI -->
