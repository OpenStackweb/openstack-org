<script>
    var components = $getDefaultComponents;
    var releases   = $getReleases;
    components.max_maturity_points = $Top.getMaxAllowedMaturityPoints;
</script>
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
            <li class=""><a href="$Top.Link">Overview</a></li>
            <li class="active"><a href="$Top.Link(all-projects)">All Projects</a></li>
        </ul>
    </div>
</div>
<div class="software-tab-dropdown">
    <div class="dropdown">
        <button aria-expanded="true" aria-haspopup="true" data-toggle="dropdown" id="dropdownMenu1" type="button" class="dropdown-toggle projects-dropdown-btn">
            All Projects
            <i class="fa fa-caret-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li class=""><a href="$Top.Link">Overview</a></li>
            <li class="active"><a href="$Top.Link(all-projects)">All Projects</a></li>
        </ul>
    </div>
</div>
<div class="software-main-wrapper">
    <!-- Projects Subnav -->
    <div class="container">
        <div class="row outer-all-projects-subnav">
            <form class="all-projects-search-form">
                <openstack-components-free-search></openstack-components-free-search>
                <openstack-releases-ddl releases="{ releases }"></openstack-releases-ddl>
            </form>
        </div>
    </div>
    <div class="container inner-software">
        <!-- Begin Page Content -->
        <div class="row">
            <div class="col-sm-12 all-projects-wrapper">
                <h3>Browse All OpenStack Projects</h3>
                <p>
                </p>
                <p>
                    <a data-target="#statsInfoModal" data-toggle="modal" href="#">What do the stats shown on each project mean?</a>
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
                        <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">What Do These Stats Mean?</h4>
                    </div>
                    <div class="modal-body">
                        <p class="download-text">
                        </p>
                        <hr>
                        <p>
                            <strong>Adoption</strong> is nulla ipsam veniam quis eos, voluptatibus veritatis magni, molestias magnam doloribus!
                        </p>
                        <p>
                            <strong>Maturity</strong> comes from looking at {$Top.getMaxAllowedMaturityPoints} distinct tags. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae non nulla odio expedita itaque, soluta assumenda a saepe omnis illum earum officiis aliquid eum error. Ducimus accusantium quod, debitis obcaecati.
                        </p>
                        <p>
                            <strong>Age</strong> is the age of the project, consisting of maxime placeat quasi, eos, obcaecati blanditiis eaque cum cumque quaerat, harum dolorem magnam saepe quam.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- End Modal -->
        <!-- End Page Content -->
    </div>
</div>
<script src="software/js/public/all_projects.bundle.js"></script>
<!-- Software Tabs UI -->