<div style="clear:both;">
    <h2>Search Company Products</h2>
    <div class="addDeploymentForm">
        <form id="search_remote_clouds" name="search_remote_clouds" action="{$Top.Link}remote-clouds">
            <table class="main-table">
                <thead>
                <tr>
                    <th>Filter Products</th>
                    <th>Company Name</th>
                    <th>Search</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <input type="text" value="" name="name" id="name">
                    </td>
                    <td>
                        <select name="company_id" id="company_id">
                            <option  value="">--select--</option>
                            <% if Companies %>
                                <% loop Companies %>
                                    <option  value="$ID">$Name</option>
                                <% end_loop %>
                            <% end_if %>
                        </select>
                    </td>
                    <td>
                        <input type="submit" style="white-space: nowrap;" value="Search" class="roundedButton">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div style="clear:both;">
        <h2>Company Products</h2>
        <p>Click heading to sort:</p>
        <table class="main-table">
            <thead>
                <tr>
                    <th><a href="$Top.Link?sort=company">Company ^</a></th>
                    <th><a href="$Top.Link?sort=name">Product Name ^</a></th>
                    <th>Published</th>
                    <th>Draft</th>
                    <th><a href="$Top.Link?sort=status">Status ^</a></th>
                    <th><a href="$Top.Link?sort=updated">Last Update ^</a></th>
                    <% if Top.isSuperAdmin %>
                    <th><a href="$Top.Link?sort=updatedby">Updated By ^</a></th>
                    <% end_if %>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <% if RemoteClouds %>
                    <% loop RemoteClouds %>
                    <tr>
                        <td>
                            $Company.Name
                        </td>
                        <td>
                            $Name
                        </td>
                        <td>
                            <% if isDraft() == 1 %>
                                <div style="text-align:center"> - </div>
                            <% else %>
                                Published
                            <% end_if %>
                        </td>
                        <td>
                            <% if isDraft() == 1 || hasPublishedDraft() == 0 %>
                                Pending
                            <% else %>
                                <div style="text-align:center"> - </div>
                            <% end_if %>
                        </td>
                        <td>
                            <% if Active %>Active<% else %>Deactivated<% end_if %>
                        </td>
                        <td>$LastEdited</td>
                        <% if Top.isSuperAdmin %>
                        <td>
                            <% if EditedBy %>
                            <% with EditedBy %>
                                $Email ($CurrentCompany)
                            <% end_with %>
                            <% else %>
                                N/A
                            <% end_if %>
                        </td>
                        <% end_if %>
                        <td style="min-width: 300px" width="30%">
                            <a class="product-button roundedButton addDeploymentBtn" href="$Top.Link(remote_cloud)?id=$ID&is_draft=$isDraft">Edit</a>
                            <% if isDraft() %>
                                <a target="_blank" class="product-button roundedButton addDeploymentBtn" href="$Top.Link(remote_cloud)/$ID/draft_preview">Preview Draft</a>
                                <a target="_blank" class="product-button roundedButton addDeploymentBtn" href="$Top.Link(remote_cloud)/$ID/draft_pdf">PDF</a>
                            <% else %>
                                <a target="_blank" class="product-button roundedButton addDeploymentBtn" href="$Top.Link(remote_cloud)/$ID/preview">Preview Live</a>
                                <a target="_blank" class="product-button roundedButton addDeploymentBtn" href="$Top.Link(remote_cloud)/$ID/pdf">PDF</a>
                            <% end_if %>

                            <a class="roundedButton delete-remote-cloud product-button addDeploymentBtn" href="#"
                               data-id="{$ID}"
                               data-is_draft="{$isDraft}"
                               data-class="remote_cloud">Delete</a>
                        </td>
                    </tr>
                    <% end_loop %>
                <% end_if %>
            </tbody>
        </table>
    </div>
</div>
