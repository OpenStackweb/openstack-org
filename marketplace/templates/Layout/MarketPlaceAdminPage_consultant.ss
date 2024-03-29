<% if canAdmin(consultants) %>
<div class="container">
    <div style="clear:both">
        <h1 style="width:50%;float:left;">Consultant - Product Details</h1>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center" class="roundedButton save-consultant" href="#" id="save-consultant" name="save-consultant">Save</a>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton publish-consultant" href="#" id="publish-consultant">Publish</a>
        <% if CurrentConsultant %>
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn preview-consultant" href="#" >Preview</a>
        <% end_if %>
        <% if CurrentConsultant %>
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn preview-consultant pdf" href="#" >Download PDF</a>
        <% end_if %>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn" href="$Top.Link(consultants)">&lt;&lt; Back to Products</a>
    </div>
    <% if CurrentConsultant.Published == 0 %>
        <div style="clear:both; color:red">
        THIS VERSION IS NOT CURRENTLY PUBLISHED
        </div>
    <% end_if %>
    <div style="clear:both">
        <fieldset>
            <form id="consultant_form" name="consultant_form">
                <% include MarketPlaceAdminPage_CompanyServiceHeader %>
            </form>
            <% include ExpertiseAreas %>
            <% include ConfigurationManagementExpertise %>
            <% include ReferenceClients %>
            <% include ServicesOffered %>
            <% include SupportChannels %>
            <% include Offices %>
            <% include SpokenLanguages %>
            <% include Videos %>
            <% include AdditionalResources %>
            <% include CustomerCaseStudies %>
        </fieldset>
    </div>
    <div class="footer_buttons">
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center" class="roundedButton save-consultant" href="#" id="save-consultant">Save</a>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton publish-consultant" href="#" id="publish-consultant">Publish</a>
        <% if CurrentConsultant %>
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn preview-consultant" href="#" >Preview</a>
        <% end_if %>
        <% if CurrentConsultant %>
            <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn preview-consultant pdf" href="#" >Download PDF</a>
        <% end_if %>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:50px;" class="roundedButton addDeploymentBtn" href="$Top.Link(consultants)">&lt;&lt; Back to Products</a>
    </div>
    <script type="text/javascript">
        <% if CurrentConsultant %>
        var consultant = $CurrentConsultantJson;
        <% end_if %>
        var component_releases = $ReleasesByComponent;
        var listing_url = "$Top.Link(consultants)";
        var product_url = "$Top.Link(consultant)";
    </script>
</div>
<% else %>
    <p>You are not allowed to administer Consultants.</p>
<% end_if %>