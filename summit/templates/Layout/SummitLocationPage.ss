
                        <div class="white city-intro">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<blockquote>
					<strong>You’re gorgeous, baby, you’re sophisticated, you live well...</strong>
					Vancouver is Manhattan with mountains. It’s a liquid city, a tomorrow city, equal parts India, China, England, France and the Pacific Northwest. It’s the cool North American sibling.
				</blockquote>
				<div class="testimonial-attribute">
					<img src="/summit/images/nytimes.png">
					<p>New York Times on Vancouver</p>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="light city-nav city" id="nav-bar">
	<div class="container">
		<ul class="city-nav-list">
			<li>
				<a href="#venue">
					<i class="fa fa-map-marker"></i>
					Venue
				</a>
			</li>
			<li>
				<a href="#hotels">
					<i class="fa fa-h-square"></i>
					Hotels &amp; Airport
				</a>
			</li>
			<li>
				<a href="#getting-around">
					<i class="fa fa-road"></i>
					Getting Around
				</a>
			</li>
			<li>
				<a href="#visa">
					<i class="fa fa-plane"></i>
					Visa Info
				</a>
			</li>
			<li>
				<a href="#locals">
					<i class="fa fa-heart"></i>
					Locals
				</a>
			</li>
		</ul>
	</div>
</div>
<% with $Venue %>
<div id="venue">
	<div class="venue-row">
		<div class="container">
			<h1>The Venue</h1>
			<p>
				<strong>$Name</strong>
				$Address
			</p>
		</div>
		<a href="https://flic.kr/p/8rYHEd" class="photo-credit" data-toggle="tooltip" data-placement="left" title="Photo by Nick Sinclair" target="_blank"><i class="fa fa-info-circle"></i></a>
	</div>
</div>
<% end_with %>
<div class="white hotels-row" id="hotels">
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
				<p>
					We've negotiated discount rates with six hotels adjacent to the Vancouver Convention Centre (Summit venue). Please move quickly to reserve a room before they sell out!
				</p>
			</div>
		</div>
        <% loop Hotels %>
           <% if $First || $MultipleOf(3) %>
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
                            <a href="#hotels" onclick="myClick({$Pos});" target="_blank" alt="View On Map"><i class="fa fa-map-marker"></i> Map</a>                       
                            <% if $BookingLink %>
                            <a href="{$BookingLink}" target="_blank" alt="Visit Bookings Site"><i class="fa fa-home"></i> Bookings</a>
                            <% else %>
                            <a href="{$Website}"><i class="fa fa-home"></i> Website</a>                        
                            <% end_if %>
                        <% end_if %>
                    </p>
                </div>
           <% if $MultipleOf(3) || $Last %>                
            </div>
            <% end_if %>
		<% end_loop %>
		
		<% if $Airport %>
		<% with Airport %>
		<div class="row">
			<div class="col-lg-8 col-lg-push-2 hotel-block">
				<h5 class="section-title">$Name</h5>
				<p>
					$Description
				</p>
				<h3>$Name</h3>
				<p>
					$Address
				</p>
				<p>
					<a href="#map-canvas" onclick="myClick(9);" target="_blank" alt="View On Map"><i class="fa fa-map-marker"></i> Map</a>
					<a href="{$Website}" target="_blank" alt="Visit Website"><i class="fa fa-home"></i> Website</a>
				</p>
			</div>
		</div>
		<% end_with %>
		<% end_if %>
		
		
		<div class="row">
			<div class="col-lg-8 col-lg-push-2 other-hotel-options">
				<h5 class="section-title">House Sharing</h5>
				<p>
					If you plan to bring your family with you to Vancouver or if you would like to have more space than a hotel room offers then you may want to rent an apartment or condo during your stay. The following sites are recommended for short-term property rentals.
				</p>
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-4">
						<a target="_blank" href="http://www.vrbo.com/vacation-rentals/canada/british-columbia/vancouver-area/vancouver?from-date=2015-05-18&to-date=2015-05-22&datesfirm=">VRBO</a>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4">
						<a target="_blank" href="https://www.airbnb.com/s/Vancouver--BC--Canada?checkin=05%2F18%2F2015&checkout=05%2F22%2F2015&ss_id=xwszyia1">Airbnb</a>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4">
						<a target="_blank" href="http://www.homeaway.com/search/british-columbia/vancouver/region:6437/arrival:2015-05-18/departure:2015-05-22">HomeAway</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="blue" id="getting-around">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-push-2">
				<h1>Getting Around In Vancouver</h1>
				<p>
					There are several safe and reliable transportation options in Vancouver. Here are a few options to consider to get you from Vancouver International Airport to The Vancouver Convention Centre.
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="getting-options">
					<div class="getting-around-item">
						<a href="http://www.translink.ca" target="_blank"><i class="fa fa-bus"></i>Translink<span>(public transit)</span></a>
					</div>
					<div class="getting-around-item">
						<a href="http://www.yvr.ca/en/getting-to-from-yvr/taxis.aspx" target="_blank"><i class="fa fa-cab"></i>Taxi</a>
					</div>
					<div class="getting-around-item">
						<a href="http://www.yvr.ca/en/getting-to-from-yvr/courtesy-shuttles.aspx" target="_blank"><i class="fa fa-plane"></i>Shuttles<span>(to specific hotels)</span></a>
					</div>
					<div class="getting-around-item">
						<a href="http://www.yvr.ca/en/getting-to-from-yvr/car-rentals.aspx" target="_blank"><i class="fa fa-car"></i>Rental Car</a>
					</div>
					<div class="getting-around-item">
						<a href="http://www.zipcar.ca/how?zipfleet_id=40436214" target="_blank"><i class="fa zipcar-icon">Z</i>zipcar<span>(car sharing)</span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="light visa-row" id="visa">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-push-2">
                $VisaInformation
			</div>
		</div>
	</div>
</div>
<div class="white locals-row" id="locals">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-push-2">
				<h1>In The Words Of The Locals</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-8 col-lg-push-2 col-md-8 col-md-push-2 col-sm-8 col-sm-push-2 local-block">
				<blockquote>
                    Vancouver is addictive.<br/>I came to Vancouver for a 2 week vacation over 15 years ago and never left.
                </blockquote>
				<img class="testimonial-author-img" src="/summit/images/DianeMueller.jpeg">
				<div class="testimonial-attribute">
					<div class="testimonial-name">Diane Mueller</div>
					<div class="testimonial-title">Community Development, Red Hat</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 local-place-block">
				<img src="/summit/images/therailwayclub.jpg">
				<h3>Railway Club</h3>
				<p>
					For my money, the coolest place in town was hip before hipster was a word&mdash;The Railway Club, where KD Lang got her start. Check out the latest indie upstarts, while drinking local brews with their famous Rubin sandwich or my fav the espresso martini and watch the model railway cars run around the ceiling.
				</p>
				<p>
					<a href="http://therailwayclub.com">therailwayclub.com</a>
				</p>
			</div>
			<div class="col-md-4 local-place-block">
				<img src="/summit/images/MOA-UBC.jpeg">
				<h3>Museum of Anthropology</h3>
				<p>
					If you are in Vancouver, you are on traditional territories of Musqueam First Nations, and any visit should take into consideration the heritage and history that spans thousands of years and continues to inform and enrich life here in Vancouver. The Museum of Anthropology at UBC does an amazing job of showcasing the rich art &amp; living culture of the First Nations and for my money it’s the one thing you must not leave town without experiencing. 
				</p>
				<p>
					<a href="http://moa.ubc.ca">moa.ubc.ca</a>
				</p>
			</div>
			<div class="col-md-4 local-place-block">
				<img src="/summit/images/gibsons.jpg">
				<h3>Sunshine Coast</h3>
				<p>
					If you really want to “see” BC, you’ll need to hop on a ferry and come to my stomping grounds on the Sunshine Coast. First stop: hit up the local Beer Farm in Gibsons. Yep, we Canadians grow our own beer too. Persphone’s Brewery has a “Wee Heavy” Stout that cannot be missed!  
				</p>
				<p>
					<a href="http://bigpacific.com">bigpacific.com</a>
				</p>
			</div>
		</div>
	</div>
</div>
<div class="about-city-row">
	<p>
		Mountains, ocean, culture, nightlife all rolled into one beautiful city...
	</p>
	<h1>Come Join Us In Vancouver</h1>
	<p>
		<% if $CurrentSummit.RegistrationLink %>
		<a href="$CurrentSummit.RegistrationLink" class="btn orange-btn">Register Now</a>
		<% end_if %>
	</p>
	<a href="https://flic.kr/p/adaKoH" class="photo-credit" data-toggle="tooltip" data-placement="left" title="Photo by Magnus Larsson" target="_blank"><i class="fa fa-info-circle"></i></a>
</div>
<script type="text/javascript">
// Google Maps
// Define your locations: HTML content for the info window, latitude, longitude
    var locations = [
        ['<h5>Vancouver Convention Centre</h5><p>1055 Canada Pl, Vancouver, BC<br>V6C 0C3, Canada</p>', 49.289431, -123.116381],
        ['<h5>Pan Pacific Vancouver Hotel</h5><p>300-999 Canada Place Way | Suite 300<br>Vancouver, British Columbia V6C3B5, Canada<br><a href="http://www.panpacificvancouver.com" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.288137, -123.113232],
        ['<h5>Fairmont Waterfront</h5><p>900 Canada Place Way<br>Vancouver, British Columbia V6C 3L5, Canada<br></p><p class="sold-out-hotel">SOLD OUT</p>', 49.287546, -123.113393],
        ['<h5>Fairmont Pacific Rim</h5><p>1038 Canada Place<br>Vancouver, British Columbia V6C 0B9, Canada<br><a href="http://www.fairmont.com/pacific-rim-vancouver/" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.288427, -123.116851],
        ['<h5>Pinnacle Vancouver Harbourfront Hotel<span>formerly Renaissance Vancouver Harbourside</span></h5><p>1133 West Hastings Street<br>Vancouver, British Columbia V6E3T3, Canada<br><a href="http://www.marriott.com/hotels/travel/yvrrd-renaissance-vancouver-harbourside-hotel/" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.288617, -123.121028],
        ['<h5>Vancouver Marriott Downtown</h5><p>1128 West Hastings Street<br>Vancouver, British Columbia V6E 4R5, Canada<br><a href="http://www.marriott.com/hotels/travel/yvrdt-vancouver-marriott-pinnacle-downtown-hotel/" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.288186, -123.120250],
        ['<h5>Fairmont Hotel Vancouver</h5><p>900 West Georgia Street<br>Vancouver, British Columbia V6C 2W6, Canada<br><a href="http://www.fairmont.com/hotel-vancouver/" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.283901, -123.120957],
        ['<h5>Hyatt Regency Vancouver</h5><p>655 Burrard Street<br>Vancouver, British Columbia V6C 2R7, Canada<br><a href="http://vancouver.hyatt.com/en/hotel/home.html" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.285695, -123.119663],
        ['<h5>Four Seasons Hotel Vancouver</h5><p>791 West Georgia Street<br>Vancouver, British Columbia V6C 2T4, Canada<br><a href="http://www.fourseasons.com/vancouver/" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.283805, -123.117930],
        ['<h5>Vancouver International Airport</h5><p>791 West Georgia Street<br>Vancouver, British Columbia V6C 2T4, Canada<br><a href="http://www.fourseasons.com/vancouver/" target="_blank" alt="Visit Website">Visit Website</a></p>', 49.193537, -123.179974]
    ];
</script>
