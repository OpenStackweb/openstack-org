<div class="row">
    <div class="col-sm-8">
        <h1 style="color: #{$Company.CompanyColor} !important;"> $Name </h1>
    </div>
    <div class="col-sm-4">
        <a style="background-color: #{$Company.CompanyColor}; color: #ffffff;" href="$Call2ActionUri" rel="nofollow" class="marketplace-details-btn">Details &amp; Signup</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <p>$Overview</p>
    </div>
</div>
<% if isOpenStackPowered %>
<div class="row powered-wrapper">
    <div class="col-sm-12">
        <div class="powered-image">
            <img src="/marketplace/code/ui/frontend/images/openstack-powered.png" alt="OpenStack Powered" width="170">
        </div>
        <div class="powered-description">
            <p>
                This product is OpenStack Powered. It contains OpenStack software and has been approved by the OpenInfra Foundation. <a href="/brand/openstack-powered/">Learn more about becoming an OpenStack Powered product here</a>.
            </p>
        </div>
        <% if isOpenStackTested %>
            <div class="test-details-list">
                <h4>OpenStack Powered $TestedCapabilityTypeLabel $ProgramVersion.Name</h4>
                <table>
                    <tbody>
                    <thead>
                    <tr>
                        <th>$TestedCapabilityTypeLabel Capabilities</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <% loop TestedCapabilities %>
                        <tr>
                            <td>$Name</td>
                            <td><i class="fa fa-check"></i></td>
                        </tr>
                    <% end_loop %>
                    </tbody>
                    <tbody>
                    <thead>
                    <tr>
                        <th>Designated Sections</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <% loop DesignatedSections %>
                        <tr>
                            <td>$Guidance</td>
                            <td><i class="fa fa-check"></i></td>
                        </tr>
                    <% end_loop %>
                    </tbody>
                </table>
            </div>
        <% end_if %>
    </div>
</div>
<% end_if %>
<% if UsesIronic %>
<div class="row powered-wrapper">
    <div class="col-sm-12">
        <div class="powered-image">
            <img src="/marketplace/code/ui/frontend/images/baremetal-logo-program.svg" alt="Ironic" width="168">
        </div>
        <div class="powered-description ironic">
            <p>
                This product uses <a href="{$BaseHref}bare-metal">OpenStack's Ironic Bare Metal</a> service. Ironic allows users to manage bare metal
                infrastructure like they would virtual machines and provides ideal infrastructure to run container orchestration
                frameworks like Kubernetes to optimize performance.
            </p>
        </div>
    </div>
</div>
<% end_if %>
<% if isCompatibleWithFederatedIdentity %>
<div class="row powered-wrapper">
    <div class="col-sm-12">
        <div class="powered-image">
            <img src="/marketplace/code/ui/frontend/images/federated-identity-badge.png" alt="Federated Identity" width="168">
        </div>
        <div class="powered-description federated-identity">
            <p>
                This product supports OpenStack Federated Identity, allowing it to connect to other OpenStack clouds for Authentication and Authorization.
            </p>
        </div>
    </div>
</div>
<% end_if %>