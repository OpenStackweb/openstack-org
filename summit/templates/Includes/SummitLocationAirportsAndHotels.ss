<div class="white hotels-row" id="hotels" style="$Style">
    <div class="venue-map" id="map-canvas"></div>
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-push-2">
            <h1>Hotels & Airport</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-lg-push-2">
            <h5 class="section-title">Official Summit Hotels</h5>
            <p style="margin-bottom:30px;">
                <i class="fa fa-hotel fa-4x"></i>
            </p>
            $LocationsTextHeader
        </div>
    </div>
    <% loop Hotels %>
        <% if $First() %>
        <div class="row">
        <% end_if %>
        <div class="col-lg-4 col-md-4 col-sm-4 hotel-block">
            <h3>{$Pos}. $Name</h3>
            <p>
                $Address
            </p>
            <p<% if $IsSoldOut %> class="sold-out-hotel" <% end_if%>>
                <% if $IsSoldOut %>
                    SOLD OUT
                <% else %>
                    <a href="$Top.Link#map-canvas" class="marker-link"  data-location-id="{$ID}"  alt="View On Map"><i
                            class="fa fa-map-marker"></i> Map</a>
                    <% if $BookingLink %>
                        <a href="{$BookingLink}" target="_blank" alt="Visit Bookings Site"><i class="fa fa-home"></i>
                            Bookings</a>
                    <% else %>
                        <a href="{$Website}"><i class="fa fa-home"></i> Website</a>
                    <% end_if %>
                <% end_if %>
            </p>q
        </div>
        <% if Last() %>
        </div>
        <% else_if $MultipleOf(3) %>
        </div>
        <div class="row">
        <% end_if %>
    <% end_loop %>
    <% if $Airports %>
        <% if $AirportsTitle %>
            <div class="row">
                <div class="col-lg-8 col-lg-push-2">
                    <h5 class="section-title">$AirportsTitle</h5>
                    <p>
                        $AirportsSubTitle
                    </p>
                </div>
            </div>
        <% end_if %>
        <div class="row">

            <% loop Airports %>

                <div class="col-sm-4 col-sm-push-2 hotel-block">
                    <h3>$Name</h3>
                    <p>
                        $Address
                    </p>
                    <p>
                        <a class="marker-link" href="$Top.Link#map-canvas" data-location-id="{$ID}" alt="View On Map"><i
                                class="fa fa-map-marker"></i> Map</a>
                        <a href="{$Website}" target="_blank" alt="Visit Website"><i class="fa fa-home"></i> Website</a>
                    </p>
                </div>

            <% end_loop %>
        </div>
    <% end_if %>
    <% if OtherLocations  %>
        <div class="row">
            <div class="col-lg-8 col-lg-push-2 other-hotel-options">
                <h5 class="section-title">House Sharing</h5>
                $OtherLocations
            </div>
        </div>
    <% end_if %>
</div>
</div>