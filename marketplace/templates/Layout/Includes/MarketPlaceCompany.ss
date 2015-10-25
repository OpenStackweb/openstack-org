    <div class="col-sm-3">
        $Company.SmallLogoPreview(150)
        <h4 style="color: #{$Company.CompanyColor} !important;">About $Company.Name</h4>
        <p>$Company.Overview</p>
        <hr>
        <div class="pullquote">
            <h4 style="color: #{$Company.CompanyColor} !important;">$Company.Name Commitment</h4>
            <div <% if Company.CommitmentAuthor %>class="commitment"<% end_if %>>$Company.Commitment</div>
            <% if Company.CommitmentAuthor %>
            <p class="author">&mdash;$Company.CommitmentAuthor, $Company.Name</p>
            <% end_if %>
        </div>
    </div>
    <div class="col-sm-9 marketplace-body">
        <div class="row">
            <div class="col-sm-8">
                <h1 style="color: #{$Company.CompanyColor} !important;"> $Name </h1>
            </div>
            <div class="col-sm-4">
                <a style="background-color: #{$Company.CompanyColor}" href="$Call2ActionUri" rel="nofollow" class="marketplace-details-btn">Details &amp; Signup</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>$Overview</p>
            </div>
        </div>
        <% if isOpenStackTested %>
        <div class="row powered-wrapper">
            <div class="col-sm-12">
                <div class="powered-image">
                    <img src="/marketplace/code/ui/frontend/images/openstack-powered.png" alt="">
                </div>
                <div class="powered-description">
                    <p>
                        This product is OpenStack Powered. It contains OpenStack software and has been validated through testing to provide API compatibility for OpenStack core services. <a href="/brand/interop/">Learn more about the testing criteria and core services here</a>.
                        <div class="tested-badge-wrapper">
                            <div class="tested-badge">
                                <div class="tested-icon">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="tested-text">
                                    Tested
                                </div>
                            </div>
                            <div class="tested-description">OpenStack Powered $TestedCapabilityTypeLabel $ProgramVersion.Name .
                            <a href="#" id="see-results-link">See full results [+]</a>.</div>
                        </div>
                    </p>
                </div>
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
