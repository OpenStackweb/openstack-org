<div class="nav-first-group project-groups row">
    <div class="line"><div class="triangle"></div></div>
    <div class="col-md-12">
        <h4>Select a project group</h4>
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
                    <div class="col-md-2 col-sm-2 col-xs-2">
                        <img src="$Top.MascotImage($Slug)" />
                    </div>
                    <div class="col-md-8 col-sm-8 col-xs-8">
                        <span class="code-name"> $CodeName </span><br>
                        <span class="name"> $Name </span>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                    </div>
                </a>
            </div>
        <% end_loop %>
    </div>
<% end_loop %>