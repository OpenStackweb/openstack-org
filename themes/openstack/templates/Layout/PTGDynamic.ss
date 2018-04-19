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
        <div role="tabpanel" class="tab-pane" id="sponsor">
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
                        <h2>Frequently Asked Questions</h2>
                        <p>
                            <strong>Q: What is the price of registration?</strong>
                        </p>
                        <p>
                            A: Ticket prices for this PTG will be tiered, and are significantly subsidized to help cover part of the overall event cost:
                            <ul>
                                <li>Early Bird: USD $199 (Deadline May 11 at 6:59 UTC)</li>
                                <li>Regular: USD $399 (Deadline August 23 at 6:59 UTC)</li>
                                <li>Late/Onsite: USD $599</li>
                            </ul>
                        </p>

                        <p>

                        <p>
                            <strong>Q: Are OpenStack operators welcome to the PTG?</strong>
                        </p>
                        <p>
                            A: Yes! Engaged operators and developers are all contributors helping to make OpenStack better. If the teams, workgroups or SIGs they are involved with are taking the opportunity to meet at the PTG, all team members should join to get work done with their co-workers.</p>

                        <p>
                            <strong>Q: Which groups can meet at the PTG?</strong>
                        </p>
                        <p>
                            A: Contributing to OpenStack and helping to make it better comes in various shapes and forms. We have upstream project teams, User Committee working groups, Special Interest Groups (SIGs), pop-up work groups around a specific issue, and other styles of teams. Event organizers have taken a pretty liberal approach as to which group can request a “track” at the event: if you’re engaged and openly collaborating to make OpenStack better, your team can meet at the PTG!</p>

                        <p>
                            <strong>Q: Is my workgroup forced to meet at the PTG?</strong>
                        </p>
                        <p>
                            A: Each team is free to decide if they want to leverage the PTG to meet in-person or not. Sometimes a team is small, or dominated by one organization, or coming from the same far-away geographic region and it does not make much sense for team members to travel to the PTG to meet. And sometimes it would benefit from getting together to build trust and get work done more effectively over the next 6 months. The decision to participate is theirs.
                        </p>
                        <p>
                            We still encourage leaders and cross-team liaisons from all workgroups to participate to the event to represent their team, whether their team actually meets at the PTG or not
                        </p>
                        <p>
                            <strong>Q: Why are you running PTGs separately from Summits?</strong>
                        </p>
                        <p>
                            A: In the past, the OpenStack community has published the latest release, hosted the OpenStack Conference, and held the Design Summit for contributors to plan the next version—all in the same feverish, two-week timespan. There have been a few problems with this approach. It has been hard on users because there was very little time get acquainted with the new release before the conference. It has been hard for strategic discussions, because they happened too late in the cycle to be taken into account. It has been hard on contributors, torn between the opportunity to reach out beyond their team, and the necessity to get a lot of things done with your team. And it has been hard on downstream companies who are working to build product based on the new releases.
                        </p>
                        <p>
                            As a result, we moved Summits further away from release dates, and focus on cross-community communication and strategic discussions there. We created a separate event at the start of the development part of the release cycle for team meetings in a productive, distraction-free setting.
                        </p>
                        <p>
                            <strong>Q: Why are you merging the Ops Meetup with the PTG?</strong>
                        </p>
                        <p>
                            A: The Ops meetup is a bi-annual event where operators get together to discuss common pain points and priorities, and meet within working groups to get work done. It happened a few weeks away from the PTG, in a different location. It made it quite costly to travel to both events, and therefore discouraged operators from participating in other work groups and SIGs that were meeting at the PTG (or the other way around). The format of the Ops meetup being compatible with the format of the PTG, the User Committee decided to try to run the Ops meetup at the PTG.
                        </p>
                        <p>
                            <strong>Q: What is the Forum?</strong>
                        </p>
                        <p>
                            A: Forum is the name for the part of the Design Summit (Ops+Devs) that still happens at the main Summit event. It is primarily be focused on strategic discussions and planning for the next release (the “what”), essentially the start of the next release cycle even though development will not begin for another 3 months. We should still take advantage of having all of our community (Devs, Ops, End users…) represented to hold cross-community discussions there. That means getting feedback from users and operators over specific projects in our last release, gathering pain points and priorities for upcoming development, proposing changes and see what the community thinks of them, and recruiting and onboarding new team members. We will do that in a neutral space (rather than have separate “Ops” and “Dev” days) so that the discussion is not influenced by who owns the session. This event will happen at least two months after the previous release, to give users time to test and bring valuable feedback.

                        </p>
                        <p>
                            <strong>Q: What is the difference between PTGs and Forums?</strong>
                        </p>
                        <p>
                            A: he Forum is a part of the OpenStack Summit event. It’s organized in 40-min slots to discuss specific topics. The Forum takes advantage of having a larger cross-section of our community present to gather wide feedback on strategic issues, helping to shape the future of OpenStack and set priorities. At the Forum, we discuss the “what”, we take the pulse of the community, we engage outside of our existing teams and established contributor base.
                        </p>
                        <p>
                            In contrast, the PTG is more to discuss within our teams, between people engaged to actively work on making OpenStack better. At the PTGs, we discuss the “how”, get agreement, build trust, assign work items and get work done. Of course there is a grey area and some key topics end up being discussed in both venues.

                        </p>
                        <p>
                            <strong>Q: Are ops and devs still needed at the main Summit?</strong>
                        </p>
                        <p>
                            A: Operators and developers are still very much needed at the main Summit. The Summit is where all of the OpenStack community gets together and where the feedback loop happens. All OpenStack teams need to be represented there, to engage in strategic discussions, collect the feedback on their project, communicate about their group work, discuss cross-community topics, reach out to new people and onboard new developers. We also very much want to have operators and developers give presentations at the conference portion of the Summit.
                        </p>
                        <p>
                            <strong>Q: The Project Teams Gathering sounds like a huge event. How am I expected to be productive there? Or to be able to build social bonds with my small team?</strong>
                        </p>
                        <p>
                            A: Project Teams Gatherings are much smaller events compared to Summits (think 400 people rather than 5000). Each workgroup is allocated a specific, separate meeting area, in which they will be able to organize their schedule and work however they see fit. Past attendees have described the PTG as one of the most productive events they ever attended. The only moment where everyone meets is around lunch. We organize a limited number of social events (happy hours, karaoke, game night) and encourage teams to organize team dinners and build strong social bonds.
                        </p>
                        <p>
                            <strong>Q: I heard that you were reconsidering Project Teams Gatherings for 2019. What is the future of this event?</strong>
                        </p>
                        <p>
                            A: Project Teams Gatherings are very popular with attendees, who praise the productivity of the event coming from a distraction-free environment. However, not everyone can attend the event, especially when they need to prioritize attendance to the Summit for business reasons. Those contributors feel excluded, and would prefer if the PTG would happen directly around (or concurrently to) Summits.
                        </p>
                        <p>
                            Holding Project Teams Gatherings is therefore a complex trade-off between productivity and inclusion. After having held 3 of those events (and committing to hold a 4th one in September 2018), the OpenStack Foundation is gathering wide feedback before deciding on the 2019 strategy.
                        </p>
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
