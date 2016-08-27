<div class="product-box row" style="border-left-color: #{$Company.CompanyColor}">
    <div class="col-lg-3 col-md-5 col-sm-6">
        <div class="logo-area">
            <span style="background-color: #{$Company.CompanyColor}" class="color-bar"></span>
            <a href="<% with $MarketPlace  %><% if Name == "Appliance"  %>$ApplianceLink<% end_if %><% if Name == "Distribution"  %>$DistroLink<% end_if %><% end_with %>/{$Company.URLSegment}/{$Slug}">
                <img alt='{$Company.Name}_small_logo' src='{$Company.Logo().getURL()}' class='small-logo-company company-logo'/>
            </a>
        </div>
    </div>
    <div class="col-lg-8 col-md-7 col-sm-6">
        <div class="company-details-area">
            <h4>
                <a style="color: #{$Company.CompanyColor}" href="<% with $MarketPlace  %><% if Name == "Appliance"  %>$ApplianceLink<% end_if %><% if Name == "Distribution"  %>$DistroLink<% end_if %><% end_with %>/{$Company.URLSegment}/{$Slug}">
                    $Name
                </a>
            </h4>
            <p>$Overview</p>
            <% if isOpenStackTested %>
                <div class="tested-listing">
                    <i class="fa fa-check-square"></i>
                    <div class="tested-listing-title">Tested</div>
                    <div class="tested-listing-description">OpenStack Powered $TestedCapabilityTypeLabel $ProgramVersion.Name</div>
                </div>
            <% end_if %>
            <a style="background-color: #{$Company.CompanyColor}" href="<% with $MarketPlace  %><% if Name == "Appliance"  %>$ApplianceLink<% end_if %><% if Name == "Distribution"  %>$DistroLink<% end_if %><% end_with %>/{$Company.URLSegment}/{$Slug}" class="details-button">Details</a>
        </div>
    </div>
</div>