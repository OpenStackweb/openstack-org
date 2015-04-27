<div class="grey-bar">
    <div class="container">
        <p class="back-label">
            <a href="$Top.Link">All Clouds</a>
        </p>
    </div>
</div>

<div class="container marketplace-content">
<% include MarketPlaceCompany %>

    <% include OpenStackImplementationCapabilities %>
    <div class="col-lg-6">
        <h3 style="color: #000000 !important;">Other Details</h3>
        <% if Top.PricingSchemas %>
            <div class="info-area">
                <h4 style="color: #{$Company.CompanyColor} !important;">Pricing Options</h4>
                <table class="pricing">
                <tbody>
                <% loop Top.PricingSchemas %>

                    <tr>
                        <td>$Type</td>
                        <td id="enabled_{$ID}"></td>
                    </tr>
                <% end_loop %>
                </tbody>
                </table>
                <script>
                    <% if IsDraft  %>
                        var enabled_schemas = $Top.EnabledPricingSchemasDraft;
                    <% else %>
                        var enabled_schemas = $Top.EnabledPricingSchemas;
                    <% end_if %>
                </script>
            </div>
        <% end_if %>
        <% if HyperVisors %>
            <div class="info-area">
                <h4 style="color: #{$Company.CompanyColor} !important;">Supported Hypervisors</h4>
                <p>
                <% loop HyperVisors %>
                    <% if First == 0  %>,<% end_if %>
                    $Type
                <% end_loop %>
                </p>
            </div>
        <% end_if %>
        <% if Guests %>
            <div class="info-area">
                <h4 style="color: #{$Company.CompanyColor} !important;">Supported Guests</h4>
                <p>
                <% loop Guests %>
                    <% if First == 0  %>,<% end_if %>
                    $Type
                <% end_loop %>
                </p>
            </div>
        <% end_if %>
        <% if DataCenterRegions %>
            <hr>
            <div class="info-area">
                <h4 style="color: #{$Company.CompanyColor} !important;">Regions</h4>
                <table class="regions">
                    <tbody>
                        <% loop DataCenterRegions %>
                        <tr>
                            <td class="region-key">
                                <span style="background-color: #{$Color}"></span>
                            </td>
                            <td>$Name</td>
                        </tr>
                        <% end_loop %>
                    </tbody>
                </table>
            </div>
        <% end_if %>
        <% if DataCenters %>
            <div class="info-area">
                <script type="text/javascript">
                        <% if IsDraft  %>
                            var dc_locations_per_cloud_instance = $Top.CurrentDataCenterLocationsDraftJson;
                        <% else %>
                            var dc_locations_per_cloud_instance = $Top.CurrentDataCenterLocationsJson;
                        <% end_if %>
                </script>

                <h4 style="color: #{$Company.CompanyColor} !important;" >Data Center Locations</h4>
                <p>
                    <% loop DataCenters %>
                        <% if First == 0  %>,<% end_if %>
                        $City
                    <% end_loop %>
                </p>
                <div style="width: 300px; height: 200px; position: relative;" id="mini-map" tabindex="0">
                </div>
                <p>Click any location to see availability zones and API endpoints</p>
            </div>
        <% end_if %>
        <% if RegionalSupports %>
            <div class="info-area">
                <h4 style="color: #{$Company.CompanyColor} !important;">Regions where support is offered</h4>
                <table class="regions">
                    <tbody>
                        <% loop RegionalSupports %>
                        <tr>
                            <% loop Region %>
                                <td>$Name</td>
                            <% end_loop %>
                        </tr>
                        <% end_loop %>
                    </tbody>
                </table>
            </div>
        <% end_if %>
    </div>
    <div class="col-lg-6 marketplace-bottom-right">
        <% if Videos %>
        <div class="info-area">
            <h3 style="color: #{$Top.Company.CompanyColor} !important;">Videos</h3>
            <% loop Videos %>
                <p style="color: #{$Top.Company.CompanyColor} !important;" class="video-title">$Name <span class="video-duration">($FormattedLength)</span></p>
                <iframe frameborder="0" width="100%" height="200" allowfullscreen=""
                        src="//www.youtube.com/embed/{$YouTubeId}?rel=0&amp;showinfo=0&amp;modestbranding=1&amp;controls=2">
                </iframe>
            <% end_loop %>
        </div>
        <% end_if %>
        <% if Resources %>
        <div class="info-area">
            <h3 style="color: #{$Company.CompanyColor} !important;">More Resources</h3>
            <ul>
                <% loop Resources %>
                    <li><a href="{$Uri}" style="color: #{$Company.CompanyColor} !important;" target="_blank" class="outbound-link">$Name</a></li>
                <% end_loop %>
            </ul>
        </div>
        <% end_if %>
        <% if IsPreview  %>
            <% include MarketPlaceReviews_Placeholder %>
        <% else %>
            <% include MarketPlaceReviews %>
        <% end_if %>
    </div>
</div>
</div>