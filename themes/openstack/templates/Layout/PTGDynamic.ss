</div>

    <!-- Begin Page Content -->
<div class="ptg-hero">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                $Header
            </div>
        </div>
    </div>
</div>
<div class="ptg-body">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>Summary</h2>
                $Summary
                <h2>Why the change</h2>
                $WhyTheChange
                <a href="$Graph.getURL()" target="_blank">
                    <img class="ptg-release-timeline" src="$Graph.getURL()" alt="Project Teams Gathering vs Design Summit">
                </a>
            </div>
        </div>
    </div>
    <div class="ptg-hotel-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-push-2">
                    <i class="fa fa-hotel fa-4x"></i>
                    <h2 id="hotels">Hotel & Travel</h2>
                    $HotelAndTravel
                    <p>
                        <a class="btn btn-primary hotel-btn" href="$HotelLink" target="_blank">Book Your Room</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="ptg-who">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="who-icons">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h2>Who Should Attend</h2>
                    $WhoShouldAttend
                </div>
                <div class="col-sm-6">
                    <div class="who-icons">
                        <i class="fa fa-times-circle"></i>
                    </div>
                    <h2>Who Should Not Attend</h2>
                    $WhoShouldNotAttend
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>How can this benefit my organization?</h2>
                $Benefits
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>What are PTG Events & Why Sponsor?</h2>
                $Sponsor
                <h2>Steps to Sponsoring the PTG event:</h2>
                $SponsorSteps
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>Travel Support Program</h2>
                $TravelSupport
                <h2>Apply for Travel Support</h2>
                $TravelSupportApply
            </div>
        </div>
    </div>
    <% if $RegisterToAttend %>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>Register to Attend</h2>
                $RegisterToAttend
                <h2>PTG Schedule</h2>
                $PTGSchedule
                <a href="$ScheduleImage.getURL()" target="_blank">
                    <img class="ptg-release-timeline" src="$ScheduleImage.getURL()" alt="PTG Schedule">
                </a>
            </div>
        </div>
    </div>
    <% end_if %>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>Code of Conduct</h2>
                $CodeOfConduct
            </div>
        </div>
    </div>
</div>
<div class="ptg-cta">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-push-2">
                <h2>Want to find out more?</h2>
                $FindOutMore
                <script type="text/javascript">
                    $(document).ready(function() {
                        if (window.location.hash) {
                            $("#formShow").hide();
                            $("#thankyou").show();
                        } else {
                            $("#thankyou").hide();
                        };
                    });
                </script>
                <div class="box" id="formShow">
                    <script type="text/javascript" src="https://app.e2ma.net/app2/audience/tts_signup_refresh/1821066/dc3896463a1fcdd2f57f993be48d29f6/1771360/"></script><div id="load_check" class="signup_form_message" >This form needs Javascript to display, which your browser doesn't support. <a href="https://app.e2ma.net/app2/audience/signup/1821066/1771360/"> Sign up here</a> instead </div><script type="text/javascript">signupFormObj.drawForm();</script>
                </div>
                <div class="box" id="thankyou">
                    <h3>Thank you for your interest in the Project Teams Gathering. <br> You will be notified of any important updates.<h3>
                </div>            
            </div>
        </div>
    </div>
</div>
    <!-- End Page Content -->
