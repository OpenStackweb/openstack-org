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
                                Thanks to Our Sponsors!
                            </h5>
                        </div>
                        <div class="col-lg-12">
                            <div class="row"> 
                                $SponsorLogos
                            </div>
                        </div>
                        <div class="col-lg-12">
                          <p>&nbsp;</p>
                          <hr>
                          <p>&nbsp;</p>

                        </div>
                    </div>
                    <% end if %>
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
                            <h2>Hotel & Travel</h2>
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
                            <strong>Q: Why change?</strong>
                        </p>
                        <p>
                            A: In the past, the OpenStack community has published the latest release, hosted the OpenStack Conference for users, and held the Design Summit for contributors to plan the next version—all in the same feverish, two-week timespan. There have been a few problems with this approach. It has been hard on users because there was very little time get acquainted with the new release before the conference. It has been hard for strategic discussions, because they happened too late in the cycle to be taken into account. It has been hard on upstream contributors, torn between the opportunity to reach out beyond their team, and the necessity to get a lot of things down with your team. And it has been hard on downstream companies who are working to build product based on the new releases.
                        </p>
                        <p>So we’re making adjustments to create time between the OpenStack Conference and the release of the software. Going forward, the releases will happen months before each summit. And a new event will be also held between summits called the Project Teams Gathering (PTG). PTGs will be the new venue for planning, organizing, and kickstarting the work on a new development cycle.</p>

                        <p>
                            <strong>Q: When are those events happening?</strong>
                        </p>
                        <p>
                            A: Project Teams Gatherings happen in Q1 and Q3 of each year. OpenStack Summits happen in Q2 and Q4 of each year. You can see how this plays out in the timeline below.</p>
                        <p>
                            <a href="/assets/Uploads/summit-ptg-timeline-revised.png" target="_blank">
                                <img class="ptg-release-timeline" src="/assets/Uploads/summit-ptg-timeline-revised.png" alt="Project Teams Gathering vs Design Summit">
                            </a></p>

                        <p>
                            <strong>Q: Attendance to Design Summits used to be free for ATCs. Why do I have to pay to attend the Project Teams Gathering?</strong>
                        </p>
                        <p>
                            A: In the past, the OpenStack Foundation gave discounted Summit passes to a subset of upstream contributors (not all ATCs) who contributed in the last six months, so that they could more easily attend the Summit. Most ATCs retrieved their Summit pass just in case, and an unpredictable portion of them would actually use them to attend. The PTG space is limited, so we charge a base fee in order to increase the chances that people registering will actually show up, optimizing our usage of the space. The funds will also help keep sponsorship presence at the event to a minimum. Anyone who physically attends the Project Teams Gathering will receive a discount code to attend the next Summit, in order to keep costs low for people who will attend both events. At the same time, we are beefing up our Travel Support Program in order to ensure we can get all the needed people at the right events. If the base fee for the PTG registration is an issue, you should consider applying to that program.
                        </p>
                        <p>
                            <strong>Q: Is the PTG a capped event? How did you set expected attendance?</strong>
                        </p>
                        <p>
                            A: This is the first event of this kind. We had to make an educated guess on how many people would show up, to plan space accordingly and keep costs under control. The PTG is targeted to existing project team members, like the team mid-cycle meetups and the Fridays contributors meetups at the Summit. We looked at attendance to those events and similar dev-oriented events in the industry, and settled for 500 people. 500 people represent about 80% of the commits to OpenStack in any given release, and way more people than ever showed up at team mid-cycles.
                        </p>
                        <p>
                            <strong>Q: How is the change helping upstream developers?</strong>
                        </p>
                        <p>
                            A: When everything was held in a single Summit week, upstream developers had a lot of different goals for that week. We leveraged the Summit to communicate new things (give presentations), learn new things (attend presentations), get feedback from users and operators over our last release, gather pain points and priorities for upcoming development, propose changes and see what the community thinks of them, recruit and onboard new team members, have essential cross-project discussions, meet with our existing project team members, kickstart the work on the new cycle, and get things done. There was just not enough time in 4 or 5 days to do all of that, so we usually dropped half of those goals. Most skipped attending presentations. Some abandoned the idea of presenting. Some dropped cross-project discussions, resulting in them not having the critical mass of representation to actually serve their purpose. Some dropped out of their project team meeting to run somewhere else. The time conflicts made us jump between sessions, resulting in us being generally unavailable for listening to feedback, pain points, or newcomers. By the end of the week we were so tired we could not get anything done. We needed to free up time during the week. There are goals that can only be reached in the Summit setting, where all of our community is represented — we keep those goals in the Summit week. There are goals that are better reached in a distraction-free setting — we organize a separate event for them.
                        </p>
                        <p>
                            <strong>Q: What is the Forum?</strong>
                        </p>
                        <p>
                            A: Forum is the name for the part of the Design Summit (Ops+Devs) that still happens at the main Summit event. It is primarily be focused on strategic discussions and planning for the next release (the “what”), essentially the start of the next release cycle even though development will not begin for another 3 months. We should still take advantage of having all of our community (Devs, Ops, End users…) represented to hold cross-community discussions there. That means getting feedback from users and operators over specific projects in our last release, gathering pain points and priorities for upcoming development, proposing changes and see what the community thinks of them, and recruiting and onboarding new team members. We will do that in a neutral space (rather than have separate “Ops” and “Dev” days) so that the discussion is not influenced by who owns the session. This event will happen at least two months after the previous release, to give users time to test and bring valuable feedback.

                        </p>
                        <p>
                            <strong>Q: What is the Project Teams Gathering (PTG)?</strong>
                        </p>
                        <p>
                            A: Project Teams Gathering is the name for the part of the Design Summit that happens as a separate event. It provides space for project teams to meet, make implementation decisions and start development work (the “how”). This is where we’ll have essential cross-project discussions, meet with our existing project team members, generate shared understanding, kickstart the development work on the new cycle, and generally get things done. At this event, OpenStack project teams are given separate rooms to meet for one or more days, in a loose format (no 40-min slots). If you self-identify as a member of a specific OpenStack project team, you should definitely join. If you are not part of a specific project team (or can’t pick one team), you could still come but your experience of the event would likely not be optimal, since the goal of the attendees at this event is to get things done rather than listen to feedback or engage with newcomers. This event happens at (or just around) the previous release time, when developers are ready to fully switch development work to the new cycle.
                        </p>
                        <p>
                            <strong>Q: How is the change helping OpenStack as a whole?</strong>
                        </p>
                        <p>
                            A: Putting the larger Summit event further away from last release dramatically improves the feedback loop. In the past, calling for feedback at the Summit was not working: users hadn’t had time to use the last release at all, so most of the feedback we collected was based on the 7-month old previous release. It was also the wrong timing to push for new features: we were already well into the new cycle and it was too late to add new priorities to the mix. The new position of the “Forum” event with respect to the development cycle makes it late enough to get feedback from the previous release and early enough to influence what gets done on the next cycle. By freeing up developers time during the Summit week, we also improve the Summit experience for all attendees: developers will be more available to engage and listen. The technical content at the conference also benefits from having more upstream developers available to give talks and participate in panels. Finally, placing the Summit further away from the release date helps vendors prepare and announce products based on the latest release, making the Summit marketplace more attractive and relevant.
                        </p>
                        <p>
                            <strong>Q: What about mid-cycles?</strong>
                        </p>
                        <p>
                            A: In the past, mid-cycle sprints were organized in separate events by project teams as a way to gather team members and get things done. They grew in popularity as the distractions at the main Summit increased and it became hard for project teams to get together, build social bonds and generally be productive at the Design Summit. We hope that the Project Teams Gathering event will fulfill those productivity and social bonding needs, eliminating the need for separate team-specific sprints.
                        </p>
                        <p>
                            <strong>Q: This Project Teams Gathering thing is likely to be a huge event too. How am I expected to be productive there? Or to be able to build social bonds with my small team?</strong>
                        </p>
                        <p>
                            A: Project Teams Gatherings are much smaller events compared to Summits (think 500 people rather than 7000). Each project teams will be allocated a specific, separate room, in which they will be able to organize their work however they see fit. The only moment where everyone will meet should be around lunch. There will be no evening parties: project teams will be encouraged to organize separate team dinners and build strong social bonds.

                        </p>
                        <p>
                            <strong>Q: Does that new format actually help with cross-project work?</strong>
                        </p>
                        <p>
                            A: Cross-project work was unfortunately one of the things a lot of attendees dropped as they struggled with all the things they had to do during the Summit week. Cross-project workshops ended up being less and less productive, especially in getting to decisions or work produced. Mid-cycle sprints ended up being where the work can be done, but them being organized separately meant it is very costly for a member of a cross-project team (infrastructure, docs, QA, release management…) to attend them all. We basically set up our events in a way that made cross-project work prohibitively expensive, and then wondered why we had so much trouble recruiting people to do it. The new format ensures that we have a place to actually do cross-project work, without anything running against it, at the Project Teams Gathering. It dramatically reduces the number of places a Documentation person (for example) needs to travel to get some work done in-person with project team members. It gives project team members in vertical teams an option to break out of their silo and join such a cross-project team. It allows us to dedicate separate rooms to specific cross-project initiatives, beyond existing horizontal teams, to get specific cross-project work done. 
                        </p>
                        <p>
                            <strong>Q: Are devs still needed at the main Summit?</strong>
                        </p>
                        <p>
                            A: Upstream developers are still very much needed at the main Summit. The Summit is where all of the OpenStack community gets together and where the feedback loop happens. All project teams need to be represented there, to engage in strategic discussions, collect the feedback on their project, discuss cross-community topics, reach out to new people and onboard new developers. We also very much want to have developers give presentations at the conference portion of the Summit (we actually expect that more of them will have free time to present at the conference, and that the technical content at the Summit will therefore improve). So yes, developers are still very much needed at the main Summit.
                        </p>
                        <p>
                            <strong>Q: My project team falls apart if the whole team doesn’t meet in person every 3 months (at the Design Summit and our separate mid-cycle project team meeting). With this change aren’t we losing our ability to all get together every 3 months ?</strong>
                        </p>
                        <p>
                            A: As mentioned earlier, we hope the Project Teams Gathering to be a lot more productive than the current Design Summit, reducing the need for mid-cycle sprints. That said, if you really still need to organize a separate mid-cycle sprint, you should definitely feel free to do so. We plan to provide space at the main Summit event so that you can hold mid-cycle sprints there and take advantage of the critical mass of people already around. If you decide to host a mid-cycle sprint, you should communicate that your team mid-cycle will be co-located with the Summit and that team member attendance is strongly encouraged.
                        </p>
                        <p>
                            <strong>Q: We are a small team. We don’t do mid-cycles currently. Now we’ll have to travel to four events per year instead of two?</strong>
                        </p>
                        <p>
                            A: Each team needs to decide if it needs to get together to build trust and get work done over the next 6 months. If it does, it should participate (as a team) to the Project Teams Gathering. If it doesn’t, the team can skip it. The PTL and individual team members interested in engaging with other teams (or participating in cross-project efforts) should still definitely come to the Project Teams Gathering, but you don’t need to get every single team member there if you don’t have a dedicated team room. Note that in all cases, your project wants to have some developers present at the Summit to engage with the rest of the community.

                        </p>
                        <p>
                            <strong>Q: The project I’m involved with is mostly driven by a single vendor, most of us work from the same office. How does it make sense for all of us to travel to a remote location to get some work done?</strong>
                        </p>
                        <p>
                            A: It doesn’t make a lot of sense, and as a result we expect single-vendor teams in that situation to not request a team room at the PTG. The PTL (and whoever else is interested) should probably still come to the Project Teams Gathering to participate in cross-project work. And you should also definitely come to the Summit to engage with other organizations and contributors and increase your affiliation diversity to the point where you can take advantage of the Project Teams Gathering.
                        </p>
                        <p>
                            <strong>Q: I’m a translator, should I come to the Project Teams Gathering?</strong>
                        </p>
                        <p>
                            A: The I18n team is of course free to meet at the Project Teams Gathering. However, given the nature of the team (large number of members, geographically-dispersed, coming from all over our community, ops, devs, users), it probably makes sense to leverage the Summit to get translators together instead. The Summit constantly reaches out to new communities and countries, while the Project Teams Gathering is likely to focus on major developer areas. We’ll likely get better outreach results by holding I18n sessions or workshops at the “Forum” instead.
                        </p>
                        <p>
                            <strong>Q: A lot of people attend some sessions at the current Design Summit to get a peek at how the sausage is made. How do we preserve that education aspect in the new model?</strong>
                        </p>
                        <p>
                            A: It is true that the Design Summit was an essential piece in showing how open design worked to the rest of the world. However this created a lot of tension, as it is difficult to educate and reach out at the same time you try to get some work done. We tried to separate fishbowls and workrooms at the Design Summit, to separate discussion/feedback sessions from team-members work sessions. That worked for a time, but the context switch can be hard, and people started working around it, making some work rooms look like overcrowded fishbowl rooms. In the end that resulted in a miserable experience for everyone involved. In the new format, the “Forum” sessions will still allow people to witness open design at work, and since those are specifically set up as listening/outreach sessions (rather than team-introspective “get things done” sessions), we can take the time to properly engage and listen. It will also free up time for upstream contributors to present in the general conference and explain how the model works, rather than solely rely on the potentially-frustrating direct experience
                        </p>
                        <p>
                            <strong>Q: Having the Design Summit at the same time as the Summit allowed us to transform users and other community members into potential contributors. Doesn’t the new format jeopardize that seamless onboarding?</strong>
                        </p>
                        <p>
                            A: It is fair to say that by separating project teams gatherings from the general summit event, we make it more difficult for new potential contributors to informally join an existing team during the summit week. On the other hand, the week was so intense that team members could not really dedicate time (or space) specifically to reach out to and recruit new potential contributors: those new members had to hit the ground running. We could not spend half the time in a 40-min session to introduce or summarize the history of the topic to newcomers. By not running the work sessions during the summit week, we free up time for dedicated onboarding and education activities. Project teams will be able to request specific sessions to introduce a given project to newcomers in a classroom setting, where a few team members will spend the necessary time to explain things out. If the new contributor is interested and decides to get involved, their next step would be to start contributing and attend the project team gathering event, only 3 months away (rather than 6 months away). Beyond that, fewer conflicts during the week means we won’t be always running to our next sessions and will likely be more available to reach out to others in the hallway track, extending our recruitment sphere to people who might not have attended the Design Summit at all in the previous setup.
                        </p>
                        <p>
                            <strong>Q: What about the Ops mid-cycle meetup?</strong>
                        </p>
                        <p>
                            A: The Ops meetups are still happening, and for the next year or two probably won’t change much at all. An “Ops Meetups Team” was started to answer the questions about the future of the meetups, and also actively organize the upcoming ones. Part of that team’s definition: “Keeping the spirit of the ops meetup alive” – the meetups are run by ops, for ops and will continue to be. If you have interest, join the team and talk about the number and regional location of the meetups, as well as their content.
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
