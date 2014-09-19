<div class="product-box">
    <div class="span-8">
        <div class="logo-area">
            <span style="background-color: #{$Company.CompanyColor}" class="color-bar"></span>
            <a href="<% with $MarketPlace  %><% if Name == "Appliance"  %>$ApplianceLink<% end_if %><% if Name == "Distribution"  %>$DistroLink<% end_if %><% end_with %>/{$Company.URLSegment}/{$Slug}">$Company.SmallLogoPreview(150)</a>
        </div>
    </div>
    <div class="span-9 last">
        <div class="company-details-area">
            <h1>
                <a style="color: #{$Company.CompanyColor}" href="<% with $MarketPlace  %><% if Name == "Appliance"  %>$ApplianceLink<% end_if %><% if Name == "Distribution"  %>$DistroLink<% end_if %><% end_with %>/{$Company.URLSegment}/{$Slug}">
                    $Name
                </a>
            </h1>
            <p>$Overview</p>
            <a style="background-color: #{$Company.CompanyColor}" href="<% with $MarketPlace  %><% if Name == "Appliance"  %>$ApplianceLink<% end_if %><% if Name == "Distribution"  %>$DistroLink<% end_if %><% end_with %>/{$Company.URLSegment}/{$Slug}" class="details-button">Details</a>
        </div>
    </div>
</div>