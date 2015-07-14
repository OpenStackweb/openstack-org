<% with $Venue %>
    <div id="venue" style="$Style">
        <div class="venue-row">
            <div class="container">
                <h1>$Top.VenueTitleText</h1>
                <p>
                    <strong>$Name</strong>
                    $Address
                </p>
            </div>
        </div>
        <a href="{$Top.VenueBackgroundImageHeroSource}" class="photo-credit" data-toggle="tooltip" data-placement="left" title="{$Top.VenueBackgroundImageHero}" target="_blank">
            <i class="fa fa-info-circle"></i>
        </a>
    </div>
<% end_with %>