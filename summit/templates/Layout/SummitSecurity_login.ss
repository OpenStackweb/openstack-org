
<div class="presentation-app-header">
    <% if $Top.ActiveSummit %>
        <% if $Top.ActiveSummit.isCallForSpeakersOpen %>
            <div class="container">
                <p class="status">
                    <i class="fa fa-calendar"></i>&nbsp;{$Top.PresentationDeadlineText}</p>
                </div>
            </div>
        <% end_if %>
    <% end_if %>

    <div class="presentation-app-body">
        <div class="container">
            <h1>Would you like to present at the OpenStack Summit?</h1>

            <p>Here are three steps you should take as a potential speaker:</p>

            <ul class="submit-steps">
                <li><span>1</span> <a href="summit/berlin-2018/summit-categories" target="_blank">Review the list of Summit Categories &amp; Tracks.</a></li>
                <li><span>2</span> <a href="#process">Learn about the selection process.</a></li>
                <li><span>3</span> <a href="#submit">Submit your session proposal below. (Limit of 3 per speaker)</a></li>
            </ul>

            <hr/>
            <a id="submit"></a>
            <h2>Submit Your Speaking Proposal</h2>

            <div class="row">

                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="presentation-app-login-panel">
                        <h3>Already a member? Log in here</h3>
                        $Form
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="presentation-app-login-panel">
                        <h3>New to OpenStack? Register now.</h3>
                        $RegistrationForm
                    </div>
                </div>

            </div>

        </div>

        <div class="container">

            <hr/>

            <a id="process"></a>
            <h2>About The Summit Submission and Selection Process</h2>
            <p>On average, the OpenStack Foundation receives more than 1,000 submissions for the Summit. Of those, we are only able to select 25-30% for participation. To decide which talks are accepted, we rely on a Programming Committee as well as a community voting process that will open in July 2018.</p>
            <p>The Summit is organized around Tracks, which are focused on specific problem domains in Open Infrastructure, such as "Edge Computing", "CI/CD", or "Private &amp; Hybrid Cloud". Presentations for each Track are determined by a Program Committee for each track, which is made up of of Members and a Chair (or co-chairs). The Foundation selects the Program Committee members and chair(s), from a list of people nominated by the community. The Foundation strives to recruit Programming Committee members from a diverse set of companies, regions and roles across communities (i.e., contributing developers, users and business leaders). To nominate someone for the Summit Programming Committee for a specific Track, please fill out the nomination form. Nominations will close June, 2018.</p>
            <p>The Foundation selects Programming Committee members who are subject matter experts to review submissions within their assigned Track, for example "Edge Infrastructure" or "CI/CD." The Foundation strives to recruit Programming Committee members from a diverse set of companies, regions and roles across communities (i.e., contributing developers, users and business leaders). To nominate someone for the Summit Programming Committee for a specific Track, please fill out the nomination form. Nominations will close on June, 2018.</p>
            <p>The Foundation will also extend invitations directly to a small number of highly regarded presenters from past events for each Track, and we expect this content to make up less than 15% of total Summit presentations.</p>
            <p>Once the Call for Presentations has concluded, all submissions will be made available for community vote and input. After community voting closes, Programming Committee members will receive their presentations to review and will determine the final selections for their respective Tracks. Community votes are meant to help inform the decision, but are not the only guide. Programming Committee members are expected to exercise judgment in their area of expertise and help ensure diversity of sessions and presenters. Real-world user stories and technical, in-the-trenches experiences are favored over sales pitches.</p>
            <p>After the Programming Committee makes their decisions, presenters will be informed by mid July 2018. If you are selected as a presenter (or alternate presenter), you will receive a free code to register for the Vancouver Summit, as well as a set of deadlines and deliverables leading up to the event.</p>
            <p>If a presenter is selected as an Alternate, we will also ask them to prepare a Lightning Talk. This is in an effort to ensure that Alternates are onsite in the event they are needed, as well as program high quality Lightning Talks, which are very popular at the Summits.</p>
            <p>Please note that this process covers the speaking sessions during the Summit, NOT the Forum sessions. You can more about that process on the <a href="https://wiki.openstack.org/wiki/Forum" target="_blank">OpenStack Wiki</a>.</p>
            <h2>What to expect in Berlin 2018?</h2>
            <p>The open infrastructure landscape is changing, and so is the Summit. Now that users are integrating dozens of open source tools into a modern stack that reaches well beyond the scope of OpenStack, we&rsquo;re re-organizing the event to focus on specific problem domains and the hard work of integrating all of these tools developed in disparate communities.</p>
            <p>Part of this change means a reduction in the number of individual Tracks. To accommodate larger Tracks with more submissions to evaluate in each, we&rsquo;ve changed the terms and roles a bit from past Summits. <strong>Where previously we had Track Chairs, we now have Programming Committees for each Track, made up of both Members and a Chair (or co-chair)</strong>. We&rsquo;re also recruiting members and chairs from many different open source communities working in open infrastructure, in addition to the many familiar faces in the openstack community who will lead the effort.</p>
            <hr />
            <p>Please note that this process covers the speaking sessions during the Summit, NOT the Forum sessions. You can more about that process on the <a href="https://wiki.openstack.org/wiki/Forum" target="_blank">OpenStack Wiki</a>.</p>
            <p>Want to provide feedback on this process? Join the discussion on the <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/community" target="_blank">openstack-community mailing list</a>, and/or contact the Foundation Summit Team directly <a href="mailto:summit@openstack.org">summit@openstack.org</a>.</p>
            <p>&nbsp;</p>

            <p class="submit-button-area"><a href="#submit" class="btn btn-default">Submit Your Presentation Proposal</a></p>

            <!-- <hr />

            <a id="categories"></a>
            <h2>Summit Tracks</h2>

            <p><a href="summit/berlin-2018/categories/" target="_blank">Plan your week! See how the session tracks are grouped into audience categories, by day.</a></p>

            <% loop ActiveSummit.getCategories %>
            <p>
                <strong>$Title</strong><br/>
                $Description
            </p>
            <% end_loop %>

            <p class="submit-button-area"><a href="#submit" class="btn btn-default">Submit Your Presentation Proposal</a></p> -->


            <hr/>



        </div>

    </div>