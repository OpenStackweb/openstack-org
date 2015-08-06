<% if CityIntro %>
<div class="white city-intro">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                $CityIntro
            </div>
        </div>
    </div>
</div>
<% end_if %>
<div class="light city-nav city" id="nav-bar">
    <div class="container">
        <ul class="city-nav-list">
            <li>
                <a href="$Top.Link#venue">
                    <i class="fa fa-map-marker"></i>
                    Venue
                </a>
            </li>
            <li>
                <a href="$Top.Link#hotels">
                    <i class="fa fa-h-square"></i>
                    Hotels &amp; Airport
                </a>
            </li>
            <% if GettingAround  %>
            <li>
                <a href="$Top.Link#getting-around">
                    <i class="fa fa-road"></i>
                    Getting Around
                </a>
            </li>
            <% end_if %>
            <% if TravelSupport  %>
                <li>
                    <a href="$Top.Link#travel-support">
                        <i class="fa fa-globe"></i>
                        Travel Support Program
                    </a>
                </li>
            <% end_if %>
            <% if VisaInformation  %>
            <li>
                <a href="$Top.Link#visa">
                    <i class="fa fa-plane"></i>
                    Visa Info
                </a>
            </li>
            <% end_if %>
            <% if Locals  %>
            <li>
                <a href="$Top.Link#locals">
                    <i class="fa fa-heart"></i>
                    Locals
                </a>
            </li>
            <% end_if %>
        </ul>
    </div>
</div>
<% with $Venue %>
    <div id="venue">
        <div class="venue-row" style="background: rgba(0, 0, 0, 0) url('{$Top.VenueBackgroundImageUrl}') no-repeat scroll left top / cover ;">
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
<div class="white hotels-row" id="hotels">

<% if $CampusGraphic %>

    <div class="venue-map-drawn">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-push-2">
                    <h5 class="section-title">Summit Campus</h5>
                </div>
            </div>
        </div>
        <img class="" src="{$CampusGraphic}.svg" onerror="this.onerror=null; this.src={$CampusGraphic}.png" alt="OpenStack Summit Tokyo Hotels">
    </div>

<% else %>

    <div class="venue-map" id="map-canvas"></div>

<% end_if %>


<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-push-2">
            <h5 class="section-title">Official Summit Hotels</h5>
            <p style="margin-bottom:30px;">
                <i class="fa fa-hotel fa-4x"></i>
            </p>
            $LocationsTextHeader

            <div class="alert alert-danger" role="alert">
                IMPORTANT: You must use the following promo code to receive the Summit discounted rate.<br>
                Please <a href="//openstack.org/assets/pdf-downloads/OS-Tokyo-Hotel-Info-Packet.pdf" target="_blank">read these instructions</a> for room info and additional details.<br><strong style="font-size:1.5em;">Promo Code: OST2015</strong>
            </div>
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

                    <% if not $Top.CampusGraphic %>
                        <a href="$Top.Link#map-canvas" class="marker-link"  data-location-id="{$ID}"  alt="View On Map"><i class="fa fa-map-marker"></i> Map</a>
                    <% end_if %>

                    <% if $DetailsPage %>
                        <a href="{$Top.Link}details/$ID" alt="Visit Bookings Site"><i class="fa fa-home"></i>
                            Booking Info</a>
                    <% else_if $BookingLink %>
                        <a href="{$BookingLink}" target="_blank" alt="Visit Bookings Site"><i class="fa fa-home"></i>
                            Bookings</a>
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

    <!-- Hotel Website Modal -->
    <div class="modal fade" id="Hotel{$ID}">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Important</h4>
            </div>
            <div class="modal-body">
                <p class="center">
                    <i class="fa fa-exclamation-triangle fa-4x" style="color:#DA422F;"></i>
                </p>
                <p>
                    You must use the following promo code to receive the Summit discounted rate.
                    Please <a href="//openstack.org/assets/pdf-downloads/OS-Tokyo-Hotel-Info-Packet.pdf" target="_blank">read these instructions</a> for room info and additional details.
                </p>
                <p>
                    <strong style="font-size:1.5em;">Promo Code: OST2015</strong>
                </p>
            </div>
            <div class="modal-footer">
                <p style="text-align: center;">
                    <a href="{$Website}" class="hotel-alert-btn" target="_blank">Book your hotel room(s)</a>
                </p>
            </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- End Hotel Website Modal -->

    <% end_loop %>
    <div class="row">
        <div class="col-sm-10 col-sm-push-1">
            <h5 class="section-title">More Hotel Details</h5>
            <div class="more-hotel-details">
                <p>
                    <i class="fa fa-calendar-check-o fa-2x"></i>
                </p>
                <p>
                    Hotel discounted rates are limited and vary depending on location and dates. See the following chart for more details.
                </p>
                <p>
                    <a href="/summit/images/tokyo/hotel-discount-chart.png" target="_blank">
                        <img class="hotel-discount-chart" src="/summit/images/tokyo/hotel-discount-chart.svg" onerror="this.onerror=null; this.src=/summit/images/tokyo/hotel-discount-chart.png" alt="OpenStack Summit Tokyo Hotel Discounts">
                    </a>
                </p>
                <hr>
                <p>
                    <i class="fa fa-users fa-2x"></i>
                </p>
                <p>
                    Booking for 10 or more rooms for the Summit?
                </p>
                <p>
                    Contact <a href="mailto:sarah@fntech.com">sarah@fntech.com</a>
                </p>
                <hr>
                <p>
                    <i class="fa fa-hotel fa-2x"></i>
                </p>
                <p>
                    Looking for additional hotels near the Summit venue?
                </p>
                <p>
                    <a href="#" data-toggle="modal" data-target="#otherHotelsModal">List of nearby hotels</a>
                </p>
            </div>
        </div>
    </div>
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
<% if GettingAround  %>
<div class="blue" id="getting-around">
    <div class="container">
        $GettingAround
    </div>
</div>
<% end_if %>
<% if TravelSupport  %>
    <div class="light" id="travel-support">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-push-2">
                    $TravelSupport
                </div>
            </div>
        </div>
    </div>
<% end_if %>
<% if VisaInformation  %>
<div class="white visa-row" id="visa">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                $VisaInformation
            </div>
        </div>
    </div>
</div>
<% end_if %>
<% if Locals %>
<div class="white locals-row" id="locals">
    <div class="container">
        $Locals
    </div>
</div>
<% end_if %>
<% if AboutTheCity %>
<div class="about-city-row" style="background: rgba(0, 0, 0, 0) url('{$AboutTheCityBackgroundImageUrl}') no-repeat scroll left top / cover ">
    $AboutTheCity
    <p>
        <% if $Summit.RegistrationLink %>
            <a href="$Summit.RegistrationLink" class="btn register-btn-lrg">Register Now</a>
        <% end_if %>
    </p>
    <a href="{$AboutTheCityBackgroundImageHeroSource}" class="photo-credit" data-toggle="tooltip" data-placement="left" title="{$AboutTheCityBackgroundImageHero}" target="_blank"><i class="fa fa-info-circle"></i></a>
</div>
<% end_if %>

    <!-- Other Hotels Modal -->
    <div class="modal fade" id="otherHotelsModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Additional Hotels</h4>
          </div>
          <div class="modal-body">
          <p class="center">
              <i class="fa fa-hotel fa-4x"></i>
          </p>
            <p class="center">
                Here is a list of additional hotels near the Summit venue in Tokyo. Take the train line shown to <strong>Shinagawa Station</strong>, across the street from the Summit venue.
            </p>
            <table class="table">
                <tr>
                    <td>Hotel</td>
                    <td>Distance</td>
                    <td>Train Line</td>
                </tr>
                <tr>
                    <td><a href="http://www.shinagawa.keikyu-exinn.co.jp/en/index.html" target="_blank">Keikyu EX Inn Shinagawa Ekimae</a></td>
                    <td>450m - 5 min walk</td>
                    <td>none</td>
                </tr>
                <tr>
                    <td><a href="http://www.intercontinental-strings.jp/eng/index.html" target="_blank">The Strings by InterContinental Tokyo</a></td>
                    <td>900m - 11 min walk</td>
                    <td>none</td>
                </tr>
                <tr>
                    <td><a href="http://www.westin-tokyo.co.jp/" target="_blank">The Westin Tokyo</a></td>
                    <td>11 min walk to Ebisu Station</td>
                    <td>Yamanote Line</td>
                </tr>
                <tr>
                    <td><a href="http://www.miyakohotels.ne.jp/tokyo/english/" target="_blank">Sheraton Miyako Hotel Tokyo</a></td>
                    <td>1.6km - 20 min walk</td>
                    <td>none</td>
                </tr>
                <tr>
                    <td><a href="http://www.tokyo-marriott.com/" target="_blank">Tokyo Marriott</a></td>
                    <td>9 min walk to Kitashinagawa Station</td>
                    <td>Keikyu Main Line</td>
                </tr>
                <tr>
                    <td><a href="http://www.shinagawa.keikyu-exinn.co.jp/en/index.html" target="_blank">Hotel Villa Fontaine Tamachi</a></td>
                    <td>10 min walk to Tamachi Station</td>
                    <td>Keihin Tohoku Line > Yamanote Lineto</td>
                </tr>
                <tr>
                    <td><a href="http://www.hvf.jp/eng/mita.php" target="_blank">Hotel JAL City Tamachi Tokyo</a></td>
                    <td>9 min walk to Tamachi Station</td>
                    <td>Keihin Tohoku Line > Yamanote Lineto</td>
                </tr>
                <tr>
                    <td><a href="http://tamachi.gracery.com/" target="_blank">Hotel Gracery Tamachi</a></td>
                    <td>9 min walk to Tamachi </td>
                    <td>Keihin Tohoku Line > Yamanote Lineto</td>
                </tr>
            </table>
          </div>
          <div class="modal-footer">
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- End Other Hotels Modal -->