<script>
    var all_components = $getAllComponentsJSON();
    var releaseId  = '{$getDefaultRelease().getSlug().JS}';
</script>

<% include SoftwareHomePage_MainNavMenu Active='governance' %>

<div class="software-main-wrapper">
    <!-- Begin Page Content -->
    <div class="container inner-software">
        <div class="row">
            <div class="col-sm-12 all-projects-wrapper">
                <h3><%t Software.GOVERNANCE_SECTION_TITLE 'Governance' %></h3>
                <p><%t Software.GOVERNANCE_SECTION_DESCRIPTION 'description' %></p>
                <p></p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <governance-list components="{ all_components }" base_url="{$Top.Link}" release_id="{ releaseId }"></governance-list>
            </div>
        </div>
    </div>

    <!-- End Page Content -->


</div>

$ModuleJS('governance_projects')
<%--$ModuleCSS('software_all_projects')--%>

<!-- Software Tabs UI -->
