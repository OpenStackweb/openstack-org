<% if canAdmin(remote_clouds) %>
<div class="container">
    <div style="clear:both">
        <h1 style="width:50%;float:left;">Remotely Managed Private Clouds - Product Details</h1>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center" class="roundedButton save-remote-cloud" href="#" id="save-remote-cloud">Save</a>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton publish-remote-cloud" href="#" id="publish-remote-cloud">Publish</a>
        <% if CurrentRemoteCloud %>
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn preview-remote-cloud" href="#" >Preview</a>
        <% end_if %>
        <% if CurrentRemoteCloud %>
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn preview-remote-cloud pdf" href="#" >Download PDF</a>
        <% end_if %>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn" href="$Top.Link">&lt;&lt; Back to Products</a>
    </div>
    <% if CurrentRemoteCloud.Published == 0 %>
        <div style="clear:both; color:red">
        THIS VERSION IS NOT CURRENTLY PUBLISHED
        </div>
    <% end_if %>
    <div style="clear:both">
            <fieldset>
            <form id="remote_cloud_form" name="remote_cloud_form">
                <% include MarketPlaceAdminPage_CompanyServiceHeader %><BR>
                <% include MarketPlaceAdminPage_OpenStackPowered %>
             </form>
            <% include Components %>
            <% include Hypervisors %>
            <% include GuestOSSupport %>
            <% include Videos %>
            <% include SupportChannels %>
            <% include AdditionalResources %>
            </fieldset>
        </div>
        <div class="footer_buttons">
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center" class="roundedButton save-remote-cloud" href="#" id="save-remote-cloud2">Save</a>
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton publish-remote-cloud" href="#" id="publish-remote-cloud">Publish</a>
            <% if CurrentRemoteCloud %>
                <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn preview-remote-cloud" href="#" >Preview</a>
            <% end_if %>
            <% if CurrentRemoteCloud %>
                <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn preview-remote-cloud pdf" href="#" >Download PDF</a>
            <% end_if %>
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:50px;" class="roundedButton addDeploymentBtn" href="$Top.Link">&lt;&lt; Back to Products</a>
        </div>
        <script type="text/javascript">
                <% if CurrentRemoteCloud %>
                    var remote_cloud = $CurrentRemoteCloudJson;
                <% end_if %>
                var component_releases = $ReleasesByComponent;
                var listing_url = "{$Top.Link(remote_clouds)}";
                var product_url = "{$Top.Link(remote_cloud)}";
            </script>
        </div>
    </div>
    <% else %>
        <p>You are not allowed to administer Remote Clouds.</p>
    <% end_if %>