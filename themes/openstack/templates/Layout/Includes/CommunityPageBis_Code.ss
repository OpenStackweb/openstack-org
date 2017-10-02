<div class="project-groups row">
    <div class="col-md-12">
        <h4>Select a project group</h4>
    </div>
    <% loop $ProjectGroups %>
        <div class="col-md-4">
            <a class="project-group-button btn btn-success" data-toggle="collapse" data-target="#projectgroup_{$Key}">
                $Name
            </a>
        </div>
    <% end_loop %>
</div>

<div class="project-groups-selected row">
    <div class="col-md-4">
        <h4>Selected Project Group:</h4>
        <a href="" class="clear-groups">Select another project group</a>
    </div>
    <div class="col-md-4">
        <div class="project-group-button-selected btn btn-success">
            Compute
        </div>
    </div>
</div>

<% loop $ProjectGroups %>
    <div id="projectgroup_{$Key}" class="collapse project-options row">
        <div class="col-md-12">
            <h2>Select the project you would like to contribute to...</h2>
        </div>
        <div class="subtitle col-md-12">
            Once you select the project you are interested in contributing to, you will see an in depth guide to contributing.
            The first step is as easy as that! Don't see the project you are interested in? <a href="" class="clear-groups">Select another project group</a>
        </div>
        <% loop $Top.getComponentsByGroup($Name) %>
            <div class="col-md-4">
                <a class="project-button btn btn-success" >
                    <img src="$Mascot.getImageDir()/OpenStack_Project_{$Mascot.CodeName}_mascot.jpg" />
                    $CodeName
                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                </a>
            </div>
        <% end_loop %>
    </div>
<% end_loop %>