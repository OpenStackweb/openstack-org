
<div class="presentation-app-header">
    <% if $Top.ActiveSummit %>
        <% if $Top.ActiveSummit.isCallForSpeakersOpen %>
            <div class="container">
                <p class="status">
                    <i class="fa fa-calendar"></i>&nbsp;{$Top.PresentationDeadlineText}
                </p>
            </div>
        <% end_if %>
    <% end_if %>
</div>


<div class="presentation-app-body" style="margin-bottom:300px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Would you like to present at the Open Infrastructure Summit?</h1>

                <p>Here are three steps you should take as a potential speaker:</p>

                <ul class="submit-steps">
                    <li><span>1</span> <a href="summit/denver-2019/summit-categories" target="_blank">Review the list of Summit Categories &amp; Tracks.</a></li>
                    <li><span>2</span> <a href="#process">Learn about the selection process.</a></li>
                    <li><span>3</span> <a href="#submit">Submit your session proposal below. (Limit of 3 per speaker)</a></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
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
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <a id="process"></a>
                <h2>About the Summit Submission and Selection Process</h2>
                <p>On average, the OpenStack Foundation receives more than 1,000 submissions for the Summit. Of those, we are only able to select 25-30% for participation. To decide which talks are accepted, we rely on a Programming Committee as well as a community voting process that will open in January 2019.</p>
                <p>The Open Infrastructure Summit is organized around Tracks, which are focused on specific problem domains in Open Infrastructure, such as "Edge Computing", "CI/CD", or "Private &amp; Hybrid Cloud". Presentations for each Track are determined by a&nbsp;<span>Programming</span> Committee for each track, which is made up of of 3-5 members. The Foundation selects the Programming Committee members from a list of people nominated by the community. The Foundation strives to recruit Programming Committee members from a diverse set of companies, regions and roles across communities (i.e., contributing developers, users and business leaders). To nominate someone for the Summit Programming Committee for a specific Track, please fill out the <a href="https://openstackfoundation.formstack.com/forms/openstackdenver2019_programmingcommitteenom">nomination form</a>. Nominations will close January 4, 2019.</p>
                <p>The Foundation will also extend invitations directly to a small number of highly regarded speakers from past events for each Track, and we expect this content to make up less than 15% of total Summit presentations.</p>
                <p>Once the Call for Presentations has concluded, all submissions will be made available for community vote and input. After community voting closes, Programming Committee members will receive their presentations to review and will determine the final selections for their respective Tracks. Community votes are meant to help inform the decision, but are not the only guide. Programming Committee members are expected to exercise judgment in their area of expertise and help ensure diversity of sessions and speakers. Real-world user stories and technical, in-the-trenches experiences are favored over sales pitches.</p>
                <p>After the Programming Committee makes their decisions, speakers will be informed by mid February 2019. If you are selected as a speaker (or alternate speaker), you will receive a free code to register for the Denver Summit, as well as a set of deadlines and deliverables leading up to the event.</p>
                <p>If a speaker is selected as an Alternate, we will also ask them to prepare a Lightning Talk. This is in an effort to ensure that Alternates are onsite in the event they are needed, as well as program high quality Lightning Talks, which are very popular at the Summits.</p>
                <h2>What&rsquo;s new in Denver 2019?</h2>
                <p dir="ltr"><span>Programming Committee selections will occur early in the CFP process. We're asking each Programming Committee to help define the description of the Track, the types of presentations they are looking for, and assist with promotion via social media channels.</span></p>
                <p dir="ltr"><span>Programming Committees for each Track will help build the Summit schedule, and are made up of individuals working in open infrastructure. Responsibilities include:</span></p>
                <ul>
                    <li>Helping the Summit team put together the best possible content based on your subject matter expertise</li>
                    <li>Promoting the individual Tracks within your networks</li>
                    <li>Reviewing the submissions and Community voting results in your particular Track</li>
                    <li>Determining if there are any major content gaps in your Track, and if so, potentially soliciting additional speakers directly</li>
                    <li>Ensuring diversity of speakers and companies represented in your Track</li>
                    <li>Avoiding vendor sales pitches, focusing more on real-world user stories and technical, in-the-trenches experiences</li>
                </ul>
                <hr />
                <p>Please note that this process covers the speaking sessions during the Summit, NOT the Forum sessions. You can more about that process on the <a href="https://wiki.openstack.org/wiki/Forum" target="_blank">OpenStack Wiki</a>.</p>
                <p>Want to provide feedback on this process? Join the discussion on the <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/community" target="_blank">openstack-community mailing list</a>, and/or contact the Foundation Summit Team directly <a href="mailto:summit@openstack.org">summit@openstack.org</a>.</p>
                <p>&nbsp;</p>

                <p class="submit-button-area"><a href="#submit" class="btn btn-default">Submit Your Presentation Proposal</a></p>

            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <a id="categories"></a>
                <h2>Summit Categories</h2>

                <p><a href="summit/denver-2019/categories/" target="_blank">Plan your week! See how the session tracks are grouped into audience categories, by day.</a></p>

                <% loop ActiveSummit.getCategories %>
                    <p>
                        <strong>$Title</strong><br/>
                        $Description
                    </p>
                <% end_loop %>

                <hr>
                <p class="submit-button-area"><a href="#submit" class="btn btn-default">Submit Your Presentation Proposal</a></p>

            </div>
        </div>
        <hr>

    </div>

</div>