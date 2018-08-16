</div>

    <!-- Begin Page Content -->
    <!-- PTG header -->
    <div class="ptg-hero">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                  <img class="ptg-logo" src="/themes/openstack/static/images/ptg/PTG-logo.png" alt="">
                </div>
            </div>
            <div class="row">
              <div class="col-sm-8 col-sm-push-2 ptg-hero-intro">
                $Header
                <% if $RegisterToAttend %>
                    $RegisterToAttend
                <% end_if %>
              </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs" id="ptg-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#about" aria-controls="about" role="tab" data-toggle="tab">about</a></li>
        <% if $Sponsor %>
            <li role="presentation"><a href="#sponsor" aria-controls="sponsor" role="tab" data-toggle="tab">sponsor</a></li>
        <% end_if %>
        <% if $HotelAndTravel %>
            <li role="presentation"><a href="#travel" aria-controls="travel" role="tab" data-toggle="tab">travel</a></li>
        <% end_if %>
        <li role="presentation"><a href="#faq" aria-controls="faq" role="tab" data-toggle="tab">faq</a></li>
        <% if $PTGSchedule %>
            <li role="presentation"><a href="#schedule" aria-controls="schedule" role="tab" data-toggle="tab">schedule</a></li>
        <% end_if %>
    </ul>

  <div class="tab-content">
      <!--About tab-->
    <div role="tabpanel" class="tab-pane active" id="about">
        <div class="ptg-body">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        $Summary
                        $WhyTheChange
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
                        <h2>Code of Conduct</h2>
                        $CodeOfConduct
                    </div>
                </div>
            </div>
        </div>        
    </div>
    <!--Sponsor tab-->
    <% if $Sponsor %>
        <div role="tabpanel" class="tab-pane" id="sponsor" style="text-align:center;">
            <div class="ptg-body">
                <div class="container">
                    <% if $SponsorLogos %>
                    <div class="row">
                        <div class="col-lg-12">
                            <h5 class="section-title">
                               PTG Sponsors
                            </h5>
                        </div>
                        <div class="col-lg-12">
                            <div class="row"> 
                                $SponsorLogos
                            </div>
                        </div>
                        <div class="col-lg-12">
                          <p>&nbsp;</p>
                          <p>&nbsp;</p>
                        </div>
                    </div>
                    <% end_if %>
                    <div class="row">
                        <div class="col-sm-12">
                            <h2>What are PTG Events & Why Sponsor?</h2>
                            $Sponsor
                            <h2>Steps to Sponsoring the PTG event:</h2>
                            $SponsorSteps
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <% end_if %>
    <!--End sponsor tab-->

    <!--Travel tab-->
    <% if $HotelAndTravel %>
        <div role="tabpanel" class="tab-pane" id="travel">
            <div class="ptg-body">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <h2>Hotel Info</h2>
                            $HotelAndTravel
                            <p>
                                <a class="btn btn-primary hotel-btn" href="$HotelLink" target="_blank">Book Your Room</a>
                            </p>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            $TravelSupport
                            <h2>Apply for Travel Support</h2>
                            $TravelSupportApply
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <% end_if %>
    <!--End Travel tab-->

    <!--Schedule tab-->
    <div role="tabpanel" class="tab-pane" id="schedule">
        <% if $PTGSchedule %>
        <div class="ptg-body">
            <div class="container">
                <h2>PTG Schedule</h2>
                $PTGSchedule
                <% if $ScheduleImage %>
                <a href="$ScheduleImage.getURL()" target="_blank">
                    <img class="ptg-release-timeline" src="$ScheduleImage.getURL()" alt="PTG Schedule">
                </a>
                <% end_if %>
            </div>
        </div>
        <% end_if %>
    </div>
    <!--End Schedule tab-->

    <!--FAQ tab-->
    <div role="tabpanel" class="tab-pane" id="faq">
        <div class="ptg-body">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        $FAQText
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End FAQ tab-->
  </div>

<div class="ptg-cta">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-push-2">
                <h2>Want to find out more?</h2>
                $FindOutMore
                <script type="text/javascript">
                    $(document).ready(function() {

                        // sign up form
                        $('#e2ma_signup').submit(function(){
                            localStorage['ptg_signed_up'] = 1;
                        });

                        if (localStorage['ptg_signed_up']) {
                            $("#formShow").hide();
                            $("#thankyou").show();
                            localStorage.removeItem('ptg_signed_up');
                        } else {
                            $("#thankyou").hide();
                        };

                        // Activate tabs
                        $('#ptg-tabs a').click(function (e) {
                            var scrollHeight = $(document).scrollTop();

                            $(this).tab('show');

                            setTimeout(function() {
                                $(window).scrollTop(scrollHeight );
                            }, 5);
                        })

                        // Change url with tabs
                        if (location.hash !== '') {
                            $('.nav-tabs a[href="' + location.hash.replace('tab_','') + '"]').tab('show');
                        } else {
                            $('.nav-tabs a:first').tab('show');
                        }

                        $('.nav-tabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                            window.location.hash = 'tab_'+  e.target.hash.substr(1) ; 
                            return false;
                        });
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
