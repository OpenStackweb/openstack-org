<div class="grey-bar">
    <div class="container">
        <div class="back-label">
            <a href="$Top.Link">All Distros &amp; Appliances</a>
        </div>
    </div>
</div>
<div class="container marketplace-content">
<% include MarketPlaceCompany %>

    <% include OpenStackImplementationCapabilities %>
    <div class="col-lg-6">
        <h3 style="color: #000000 !important;">Other Details</h3>
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
        <% if RegionalSupports %>
        <hr>
        <div class="info-area">
            <h3 style="color: #{$Company.CompanyColor} !important;">Regions where support is offered</h3>
            <table class="regions">
                <tbody>
                    <% loop RegionalSupports %>
                    <tr>
                        <% with Region %>
                            <td>$Name</td>
                        <% end_with %>
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
            <iframe frameborder="0" width="100%" height="200" allowfullscreen="" src="//www.youtube.com/embed/{$YouTubeId}?rel=0&amp;showinfo=0&amp;modestbranding=1&amp;controls=2">
            </iframe>
            <% end_loop %>
        </div>
        <% end_if %>
        <% if Resources %>
        <div class="info-area">
            <h3 style="color: #{$Company.CompanyColor} !important;">More Resources</h3>
            <ul>
                <% loop Resources %>
                <li>
                    <a href="{$Uri}" style="color: #{$Company.CompanyColor} !important;" target="_blank" class="outbound-link">$Name</a>
                </li>
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