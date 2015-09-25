<div class="grey-bar">
    <div class="container">
        &nbsp;
    </div>
</div>
<div class="container">
    $Content
    <br>
    <div>
        <span> Filter by Project: </span>
        <select id="project_filter">
            <option value="all"> All </option>
        <% loop getProjects() %>
            <option value="$Pos"> $Project </option>
        <% end_loop %>
        </select>
    </div>
    <br>
    <% cached 'drivertable', ID %>
    <div class="project_all driver_table">
        <table class="table table-striped" >
            <tbody>
                <tr>
                    <th class="project">Project</th>
                    <th class="vendor">Vendor</th>
                    <th class="driver">Driver</th>
                    <th class="ships">Ships with OpenStack</th>
                    <th class="tested">Tested</th>
                </tr>
                <% loop DriverTable() %>
                <tr>
                    <td>$Project</td>
                    <td>$Vendor</td>
                    <td>
                        <a href="{$Url}">$Name</a>
                        <p>$Description</p>
                    </td>
                    <td class="releases">
                        <% if Releases %>
                          <% loop Releases %>
                            <a href="{$Url}">$Name</a>
                          <% end_loop %>
                        <% end_if %>
                    </td>
                    <td class="tested-listing" style="width:90px">
                    <% if $Tested %>
                        <i class="fa fa-check-square"></i>
                        <div class="tested-listing-title">Tested</div>
                    <% end_if %>
                    </td>
                </tr>
                <% end_loop %>
            </tbody>
        </table>
    </div>
    <% end_cached %>
    <% loop getProjects() %>
        <div class="project_$Pos driver_table" style="display:none" >
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th class="project">Project</th>
                        <th class="vendor">Vendor</th>
                        <th class="driver">Driver</th>
                        <th class="ships">Ships with OpenStack</th>
                        <th class="tested">Tested</th>
                    </tr>
                    <% loop $Top.DriverTable($Project) %>

                    <tr>
                        <td>$Project</td>
                        <td>$Vendor</td>
                        <td>
                            <a href="{$Url}">$Name</a>
                            <p>$Description</p>
                        </td>
                        <td class="releases">
                            <% if Releases %>
                              <% loop Releases %>
                                <a href="{$Url}">$Name</a>
                              <% end_loop %>
                            <% end_if %>
                        </td>
                        <td class="tested-listing" style="width:90px">
                        <% if $Tested %>
                            <i class="fa fa-check-square"></i>
                            <div class="tested-listing-title">Tested</div>
                        <% end_if %>
                        </td>
                    </tr>
                    <% end_loop %>
                </tbody>
            </table>
        </div>
    <% end_loop %>
</div>