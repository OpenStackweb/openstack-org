
<div class="light secondary-nav" id="nav-bar">
    <div class="container">
        <ul class="secondary-nav-list">
            <!-- <li>
                <a href="#hotels">
                    <i class="fa fa-h-square"></i>
                    Hotels &amp; Airport
                </a>
            </li> -->
            <li>
                <a href="#travel-support">
                    <i class="fa fa-plane"></i>
                    Travel Support &amp; Visa Info
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
<!-- <div class="blue" id="getting-around">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>Getting Around In Austin</h1>
                <p>
                    There are several safe and reliable transportation options in Austin. Here are a few options to consider.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="getting-options">
                    <div class="getting-around-item">
                        <a href="//www.capmetro.org/airport/" target="_blank"><i class="fa fa-bus"></i>MetroAirport<span>(bus)</span></a>
                    </div>
                    <div class="getting-around-item">
                        <a href="//www.uber.com/cities/austin" target="_blank"><i class="fa fa-car"></i>Uber</a>
                    </div>
                    <div class="getting-around-item">
                        <a href="//www.lyft.com/cities/austin" target="_blank"><i class="fa fa-car"></i>Lyft</a>
                    </div>
                    <div class="getting-around-item">
                        <a href="//www.austintexas.gov/department/ground-transportation" target="_blank"><i class="fa fa-plane"></i>Airport Transportation</a>
                    </div>
                    <div class="getting-around-item">
                        <a href="#" target="_blank"><i class="fa fa-car"></i>Rental Cars</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
-->
<% if Locals %>
    <div class="about-city-row austin" style="background: rgba(0, 0, 0, 0) url('{$AboutTheCityBackgroundImageUrl}') no-repeat scroll left top / cover ">
        <p>
            Legendary music, epic BBQ, history, food trucks and neon...
        </p>
        <h1>Come Join Us In Austin</h1>
        <a href="{$AboutTheCityBackgroundImageHeroSource}" class="photo-credit" data-toggle="tooltip" data-placement="left" title="{$AboutTheCityBackgroundImageHero}" target="_blank"><i class="fa fa-info-circle"></i></a>
    </div>
    <div class="white locals-row" id="locals">
        <div class="container">
            $Locals
        </div>
    </div>
<% end_if %>
