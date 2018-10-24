<div class="nav-first-group project-groups row">
    <div class="line"><div class="triangle"></div></div>
    <div class="col-md-12">
        <h4>Select a project group or view the <a href="https://docs.openstack.org/contributors/" target="_blank">Contributor Guide</a> <a href="https://docs.openstack.org/contributors/" target="_blank" class="photo-credit" title="Start with the contributor guide to get your accounts ,tools, and environments setup for developing code or documentation."><i class="fa fa-info-circle"></i></a></h4>
    </div>
    <% loop $getCategoriesWithComponents %>
        <div class="col-md-4 col-sm-6 nav-first-group-btn">
            <a class="project-group-button btn btn-success" data-toggle="collapse" data-target="#projectgroup_{$ID}">
                $Name
            </a>
        </div>
    <% end_loop %>
</div>

<div class="project-groups-selected row">
    <div class="line"><div class="triangle"></div></div>
    <div class="col-md-4 col-sm-6">
        <h4>Selected Project Group:</h4>
        <a href="" class="clear-groups">Select another project group</a>
    </div>
    <div class="col-md-4 col-sm-6">
        <h3 class="project-group-button-selected btn btn-success">
            Compute
        </h3>
    </div>
</div>

<% loop $getCategoriesWithComponents %>
    <div id="projectgroup_{$ID}" class="collapse project-options row">
        <div class="line"><div class="triangle"></div></div>
        <div class="col-md-12">
            <h2>Select the project you would like to contribute to...</h2>
        </div>
        <div class="subtitle col-md-12">
            Once you select the project you are interested in contributing to, you will see an in depth guide to contributing.
            The first step is as easy as that! Don't see the project you are interested in? <a href="" class="clear-groups">Select another project group</a>
        </div>
        <% loop $OpenStackComponents() %>
            <div class="col-md-4 col-sm-6">
                <a class="project-button btn btn-success" href="https://docs.openstack.org/{$Slug}" >
                    <div class="row">
                        <div class="col-xs-3">
                            <img src="$Top.MascotImage($Slug)" />
                        </div>
                        <div class="col-xs-7">
                            <span class="code-name"> $CodeName </span><br>
                            <span class="name"> $Name </span>
                        </div>
                        <div class="col-xs-2">
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </a>
            </div>
        <% end_loop %>
    </div>
<% end_loop %>