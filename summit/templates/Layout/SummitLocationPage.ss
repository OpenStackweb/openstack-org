
<div class="light secondary-nav" id="nav-bar">
    <div class="container">
        <ul class="secondary-nav-list">
            <li>
                <a href="#hotels">
                    <i class="fa fa-h-square"></i>
                    Hotels &amp; Airport
                </a>
            </li>
            <li>
                <a href="#venue">
                    <i class="fa fa-map-marker"></i>
                    Venue
                </a>
            </li>
            <% if Locals  %>
            <li>
                <a href="#locals">
                    <i class="fa fa-heart"></i>
                    Locals
                </a>
            </li>
            <% end_if %>
        </ul>
    </div>
</div>

<div class="white hotels-row" id="hotels">
    <% if not $Top.CampusGraphic %>
        <div class="venue-map" id="map-canvas"></div>
    <% end_if %>
    <div class="container">
        <% if AlternateHotels %>
            <div class="row">
                <div class="col-sm-8 col-sm-push-2">
                    <h5 class="section-title">Hotels</h5>
                    <p style="margin-bottom:30px;">
                        <i class="fa fa-hotel fa-4x"></i>
                    </p>
                    <div class="alert alert-danger" role="alert">
                        <p class="center">
                            All of the discounted Summit hotel room blocks are now sold out. Here are some other hotels where you may reserve a room near the Summit venue. Note that OpenStack does NOT have a contracted room block at any of these hotels.
                        </p>
                    </div>
                </div>
            </div>
        <% end_if %>
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h5 class="section-title">Nearby Hotels</h5>
                <% if not $Top.AlternateHotels %>
                    <p style="margin-bottom:30px;">
                        <i class="fa fa-hotel fa-4x"></i>
                    </p>
                <% end_if %>
                $LocationsTextHeader
            </div>
        </div>
        <% loop Hotels %>
            <% if $First() %>
            <div class="row">
            <% end_if %>
            <div class="col-lg-4 col-md-4 col-sm-4 hotel-block">
                <h3><!-- {$Pos}. -->$Name</h3>
                <p>
                    $Address
                </p>

                <% if $LocationMessage %>
                    <p class="summit-location-message">
                        $LocationMessage
                    </p>
                <% end_if %>
                <% if $Top.thereIsSummitSessionOnHotel($ID) %>
                <p>
                    <em><i class="fa fa-asterisk"></i> There will be Summit sessions at this hotel.</em>
                </p>
                <% end_if %>
                <p<% if $IsSoldOut %> class="sold-out-hotel" <% end_if%>>
                    <% if $IsSoldOut %>
                        SOLD OUT
                    <% else %> 

                        <% if not $Top.CampusGraphic %>
                            <a href="$Top.Link#map-canvas" class="marker-link"  data-location-id="{$ID}"  alt="View On Map"><i class="fa fa-map-marker"></i> Map</a>
                        <% end_if %>

                        <% if $DetailsPage %>
                            <a href="{$Top.Link}details/$ID" alt="Visit Bookings Site"><i class="fa fa-home"></i>
                                Booking Info</a>
                        <% else_if $BookingLink %>
                            <a href="{$BookingLink}" target="_blank" alt="Visit Bookings Site"><i class="fa fa-home"></i>
                                Book a Room</a>
                        <% else %>
                            <a href="#" data-toggle="modal" data-target="#Hotel{$ID}"><i class="fa fa-home"></i> Website</a>
                        <% end_if %>
                    <% end_if %>
                </p>
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
                <div class="col-sm-4 col-sm-push-4 hotel-block">
                    <h3>$Name</h3>
                    <p>
                       $Address
                    </p>
                    <p>
                        <a href="$Top.Link#map-canvas" class="marker-link"  data-location-id="{$ID}"  alt="View On Map"><i class="fa fa-map-marker"></i> Map</a>
                        <a href="{$WebSiteUrl}" target="_blank" alt="Visit Website"><i class="fa fa-home"></i> Website</a>
                    </p>  
                </div>
                <% end_loop %>
            </div>
        <% end_if %>
        <% if OtherLocations  %>
        <div class="row">
            <div class="col-lg-8 col-lg-push-2 other-hotel-options">
                <h5 class="section-title">House Sharing</h5>
                <p>If you plan to bring your family with you to Barcelona or if you would like to have more space than a hotel room offers, then you may want to rent an apartment or condo during your stay. The following sites are available for short-term property rentals.</p>
                $OtherLocations
            </div>
        </div>
        <% end_if %>
    </div>
</div>
<% if CityIntro %>
<div class="white city-intro">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                $CityIntro
            </div>
        </div>
    </div>
</div>
<% end_if %>
<% if $Venue %>
    <div id="venue">
        <div class="venue-row tokyo" style="background: rgba(0, 0, 0, 0) url('{$Top.VenueBackgroundImageUrl}') no-repeat scroll left top / cover ;">
            <div class="container">
                <h1>$Top.VenueTitleText</h1>
                <% loop Venue %>
                <p>
                    <strong>$Name</strong>
                    $Address1<br>
                    $City , $State $ZipCode
                </p>
                <% end_loop %>
            </div>
            <a href="{$Top.VenueBackgroundImageHeroSource}" class="photo-credit" data-toggle="tooltip" data-placement="left" title="{$Top.VenueBackgroundImageHero}" target="_blank">
                <i class="fa fa-info-circle"></i>
            </a>
        </div>
    </div>
<% end_if %>
