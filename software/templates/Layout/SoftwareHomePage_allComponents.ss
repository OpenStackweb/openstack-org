<script>
    var components = $getDefaultComponents;
    var releases   = $getReleases;
    components.max_maturity_points = $Top.getMaxAllowedMaturityPoints;
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
                <p>
                    <a data-target="#statsInfoModal" data-toggle="modal" href="#"><%t Software.STATS_MEANING_PROJECT 'What do the stats shown on each project mean?' %></a>
                </p>
                <hr>
            </div>
        </div>
        <core-services base_url="{$Top.Link}" components="{ components.core_components }" max_maturity_points="{ components.max_maturity_points }">
        </core-services>
        <optional-services base_url="{$Top.Link}" components="{ components.optional_components }" max_maturity_points="{ components.max_maturity_points }"></optional-services>
        <!-- Stats 'what does this mean?' Modal -->
        <div id="statsInfoModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only"><%t Openstack.CLOSE 'Close' %></span></button>
                        <h4 class="modal-title"><%t Software.STATS_MEANING 'What Do These Stats Mean?' %></h4>
                    </div>
                    <div class="modal-body">
                        <p class="download-text">
                        </p>
                        <hr>
                        <p>
                            <%t Software.ADOPTION_DESCRIPTION '<strong>Adoption</strong> is the percentage of production deployments running the project based on the latest biannual user survey results.' %>
                        </p>
                        <p>
                            <%t Software.MATURITY_DESCRIPTION '<strong>Maturity</strong> comes from looking at {points} distinct tags that indicate stability and sustainability. The current criteria includes whether or not the project has an install guide, whether it is supported by 7 or more SDKs, if the adoption percentage is greater than 75%, whether or not the team has achieved corporate diversity and whether or not there are stable branches.' points=$Top.getMaxAllowedMaturityPoints %>
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
$ModuleJS('software_all_projects')
<!-- Software Tabs UI -->
