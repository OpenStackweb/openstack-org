
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>IMPORTANT | Tokyo Hotel Information | OpenStack Open Source Cloud Computing Software</title>

    <!-- Bootstrap Core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/css/combined.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Page-specific CSS -->
    
        
            <link href=/css/datepicker.css rel='stylesheet' type='text/css'>
        
    

</head>

<body>

<div class="hotel-landing-header">
    <a href="/">
        <img class="summit-hero-logo" src="/summit/images/summit-logo-small.svg" onerror="this.onerror=null; this.src='/summit/images/summit-logo-small.png'" alt="OpenStack Summit">
    </a>
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-12 hotel-landing-intro">
            <h1>Please Read Before Booking Your Hotel Room</h1>
            <h4>How To Book Yout Reservation At <strong>{$Location.Name}</strong>.</h4>
        </div>
    </div>
    <div class="row">
    	<div class="col-sm-8 col-sm-push-2">
    		<div class="hotel-landing-select">
    			Select one of the following options that correspond with your dates.
    			<h5>Important: The dates you will be staying at the hotel impact how you will book your reservation.</h5>
    		</div>
    	</div> 
    </div>
    <div class="row">
		<div class="col-sm-12">
			<div class="hotel-landing-choice in">
			<h4>Book a stay for dates <strong>between {$Location.BookingStartDate.Month} {$Location.BookingStartDate.DayOfMonth} and {$Location.BookingEndDate.Month} {$Location.BookingEndDate.DayOfMonth}</strong></h4>
				<p>
				<em>Select this option if you are checking in no earlier than {$Location.BookingStartDate.Month} {$Location.BookingStartDate.DayOfMonth} and checking out no later than {$Location.BookingEndDate.Month} {$Location.BookingEndDate.DayOfMonth}. You are staying within our hotel block window</em>
				</p>
				<p>
					<img src="{$Location.InRangeBookingGraphic}.svg" onerror="this.onerror=null; this.src={$Location.InRangeBookingGraphic}.png" alt="">
				</p>
				<p>
					<a href="#" class="hotel-landing-select-btn" id="in-block-btn">Select this option</a>
				</p>
			</div>
			<div class="hotel-landing-choice out">
			<h4>Book a stay inlcuding dates <strong>before {$Location.BookingStartDate.Month} {$Location.BookingStartDate.DayOfMonth} or after {$Location.BookingEndDate.Month} {$Location.BookingEndDate.DayOfMonth}</strong></h4>
				<p>
				<em>Select this option if you are planning to stay for any nights earlier than {$Location.BookingStartDate.Month} {$Location.BookingStartDate.DayOfMonth} or later than {$Location.BookingEndDate.Month} {$Location.BookingEndDate.DayOfMonth}. You are staying partially outside of our block window.</em>
				</p>
				<p>
					<img src="{$Location.OutOfRangeBookingGraphic}.svg" onerror="this.onerror=null; this.src={$Location.OutOfRangeBookingGraphic}.png" alt="">
				</p>
				<p>
					<a href="#" class="hotel-landing-select-btn" id="out-block-btn">Select this option</a>
				</p>
			</div>
		</div>
    </div>
    <div class="row">
    	<div class="col-sm-12">
			<div class="inside-block">
				<h3>All days within our hotel block</h3>
				<p>
	    		If you are checking in no earlier than {$Location.BookingStartDate.Month} {$Location.BookingStartDate.DayOfMonth} and checking out no later than {$Location.BookingEndDate.Month} {$Location.BookingEndDate.DayOfMonth}, you are staying within our hotel block window and can register online using this promo code:
		    	</p>
		    	<p>
		    		Go here to book your room:<br/>
		    		<a href="{$Location.BookingLink}" target="_blank">{$Location.BookingLink}</a>
		    	</p>
		    	<hr>
		    	<p class="hotel-alert">
		    		IMPORTANT: You must enter the following promo code to receive the OpenStack Summit discount!
		    	</p>
		    	<p class="hotel-promo">
		    		Promo/Access Code: OST2015
		    	</p>
		    	<hr>
		    	<p class="center">
		    		<img src="/themes/openstack/images/summit/tokyo/hotel-promo-pic.png" alt="">
		    	</p>
		    </div>
		    <div class="outside-block">
		    	<h3>Some days outside of our hotel block</h3>
		    	<p>
	    		If you are planning to stay at <strong>{$Location.Name}</strong> for any nights earlier than {$Location.BookingStartDate.Month} {$Location.BookingStartDate.DayOfMonth} or later than {$Location.BookingEndDate.Month} {$Location.BookingEndDate.DayOfMonth}, you are staying partially outside of our block window. The only way we can guarantee our special discounted rate is if you book with the hotel via email. To make it as simple as possible, we’ve provided the following template below that you can use for your email:
		    	</p>
		    	<hr> 
		    	<div class="row">
		    		<div class="col-sm-12">
		    			<h3>Email Template to Copy/Paste</h3>
			    		<p>
			    			<strong>To:</strong> <a href="mailto:ph-ptmc@princehotels.co.jp">ph-ptmc@princehotels.co.jp</a>
			    		</p>
			    		<p>
			    			<strong>Subject:</strong> [YOUR NAME] - OpenStack Summit Hotel Reservation
			    		</p>
		    			<p>
		    				<strong>Template Email To Copy/Paste:</strong><br>
		    				<em style="color:red;">Modify the appropriate details, then copy & paste the template below to the body of your email. Feel free to adjust items as necessary, this is provided only for your reference.</em>
		    			</p>
		    			<textarea class="form-control" rows="18">
Hello,

I will be attending the OpenStack Summit in October.

This email serves as an official request for a hotel room reservation. Please respond to this email within 24 hours to confirm the reservation. The guest information and other reservation details are provided below.

Thank you,
[YOUR NAME]


HOTEL ROOM RESERVATION DETAILS:

Preferred Hotel [select one]
- Grand Prince Hotel New Takanawa (closest to main conference sessions and the marketplace expo hall)
- The Prince Sakura Tokyo Tower
- Grand Prince Hotel Takanawa (closest to the design summit sessions)
- Shinagawa Prince Hotel

Arrival Date (Check-in):
[Day / Month / Year]

Departure Date (Check-out):
[Day / Month / Year]

Quantity of Rooms:
[#]
(If booking 10 or more rooms please contact Sarah@FNtech.com)

Your Contact Information:
- Title
- First Name
- Last Name
- Company Name
- Your email address
- Your direct phone number (including country code)

Type of Room:
  - King
  - Double

Smoking preference:
  - Non smoking
  - Smoking

Do You Require Handicap Accessible Room?
  - No
  - Yes

Do you with to include breakfast in the package (for additional charge)?
  - No
  - Yes

Request early check-In time:
  - No
  - Yes

Please provide the following Guest Information for each hotel room:

Room 1, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email

Room 2, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email

Room 3, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email

Room 4, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email

Room 5, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email

Room 6, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email

Room 7, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email

Room 8, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email

Room 9, Guest Information:
- Title
- First Name
- Last Name
- Company Name
- Email
</textarea>
		    		</div> 
		    	</div>
		    	<div class="row">
		    		<div class="col-sm-12">
		    			<p>
		    				&nbsp;
		    			</p>
		    			<h3>Do not email credit card details</h3>
		    			<p>
		    				 Credit card details are NOT required to place a room reservation if you are booking outside the contracted dates. And we highly discourage you from emailing your credit card details.
		    			</p>
		    			<hr>
		    			<h3>Booking of 10 rooms or more</h3>
		    			<p>If booking 10 or more rooms please contact <a href="mailto:sarah@fntech,com">Sarah@FNtech.com</a>.</p>
		    			<hr>
		    			<h3>Cancellation Policy</h3>
		    			<ul>
		    				<li>No penalty for a cancellation received up to 2 days prior to arrival</li>
		    				<li>20% penalty on the first night stay for a cancellation received 1 day prior ot arrival</li>
		    				<li>80% penalty on the first night stay for a cancellation received on the day of arrival</li>
		    				<li>100% penalty for no shows In case of a no show, one night's charge per room will be automatically charged to the credit card used by the attendee who requested the reservation.</li>
		    			</ul>
		    			<hr>
		    			<h3>When you know your reservation is received</h3>
		    			<p>
		    				The hotels will respond to all emails sent to <a href="mailto:ph-ptmc@princehotels.co.jp">ph-ptmc@princehotels.co.jp</a> within 24 hours including weekends and holidays. The reply will contain a confirmation number for your reservation.
		    			</p>
		    		</div>
		    	</div>
		    </div>
    	</div>
    </div>
</div>
    <!-- End Page Content -->


    <!-- jQuery Version 1.11.0 -->
    <script src="/summit/javascript/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/summit/javascript/bootstrap.min.js"></script>

    <!-- The rest of the JS -->
    <script src="/summit/javascript/navigation.js"></script>
    <script src="/summit/javascript/openstack-home.js"></script>

    <!-- Javascript for this page -->
    <script src="/summit/javascript/hotel-landing.js"></script>


</body>

</html>