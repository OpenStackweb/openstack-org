<% require themedCSS(conference) %>

<% loop Parent %>
$HeaderArea
<% end_loop %>

<div class="span-5">
    <p><strong>The OpenStack Summit</strong><br />$Parent.MenuTitle.XML</p>
    <ul class="navigation">
        <% loop Parent %>
        <li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode"><span>Overview</span></a></li>
        <% end_loop %>
        <% loop Menu(3) %>
        <li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode"><span>$MenuTitle.XML</span></a></li>
        <% end_loop %>
    </ul>


    <% if HeadlineSponsors %>
    <div class="headline-sponsors">
    <hr>
    <h3>Our Headline Sponsors</h3>
    <p>
        <% loop HeadlineSponsors %>
        <a rel="nofollow" href="{$SubmitLandPageUrl}">
            $SidebarLogoPreview
        </a>
        <% end_loop %>
     </p>
     </div>
    <% end_if %>

</div>

<!-- Content Area -->

<div class="prepend-1 span-11" id="news-feed">

    <div class="span-18 last">
        <div class="sponsor-logos">
            <h1>Thank You To The OpenStack Summit Sponsors</h1>
            <p>&nbsp;</p>
            <!-- HeadlineSponsors -->
            <% if HeadlineSponsors %>
            <hr/>
            <h2>Headline Sponsors</h2>
            <p>
                <% loop HeadlineSponsors %>
                <a rel="nofollow" href="{$SubmitLandPageUrl}">
                    $SubmitLogo
                </a>
                <% end_loop %>
            </p>
            <p>&nbsp;</p>
            <% end_if %>
            <!-- PremierSponsors -->
            <% if PremierSponsors %>
            <hr/>
            <h2>Premier Sponsors</h2>
            <p>

                <% loop PremierSponsors %>
                <a rel="nofollow" href="{$SubmitLandPageUrl}">
                    $SubmitLogo
                </a>
                <% end_loop %>
            </p>
            <p>&nbsp;</p>
            <% end_if %>
            <!-- SpotlightSponsors -->
            <% if SpotlightSponsors %>
            <hr/>
            <h2>Spotlight Sponsors</h2>
            <p>
                <% loop SpotlightSponsors %>
                <a rel="nofollow" href="{$SubmitLandPageUrl}">
                    $SubmitLogo
                </a>
                <% end_loop %>
            </p>
            <p>&nbsp;</p>
            <% end_if %>
            <!-- EventSponsors -->
            <% if EventSponsors %>
            <hr/>
            <h2>Event Sponsors</h2>
            <p>

                <% loop EventSponsors %>
                <a rel="nofollow" href="{$SubmitLandPageUrl}">
                    $SubmitLogo
                </a>

                <% end_loop %>
            </p>
            <p>&nbsp;</p>
            <% end_if %>
            <!-- StartupSponsors -->
            <% if StartupSponsors %>
            <hr/>
            <h2>Startup Sponsors</h2>
            <p>
                <% loop StartupSponsors %>
                <a rel="nofollow" href="{$SubmitLandPageUrl}">
                    $SubmitLogo
                </a>
                <% end_loop %>
            </p>
            <p>&nbsp;</p>
            <% end_if %>
            <!-- InKindSponsors -->
            <% if InKindSponsors %>
            <hr/>
            <h2>Community Partners</h2>
            <p>
                <% loop InKindSponsors %>
                <a rel="nofollow" href="{$SubmitLandPageUrl}">
                    $SubmitLogo
                </a>
                <% end_loop %>
            </p>
            <p>&nbsp;</p>
            <% end_if %>
        </div>
    </div>
</div>

