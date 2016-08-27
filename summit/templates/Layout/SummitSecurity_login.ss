<div class="presentation-app-header">
    <div class="container">
        <p class="status">
            <i class="fa fa-calendar"></i>&nbsp;{$Top.PresentationDeadlineText}</p>
        </div>
    </div>


    <div class="presentation-app-body">
        <div class="container">
            <h1>Would you like to speak at the OpenStack Summit?</h1>

            <p>Here are three steps you should take as a potential speaker:</p>

            <ul class="submit-steps">
                <li><span>1</span> <a href="summit/barcelona-2016/categories/" target="_blank">Review the list of Summit Categories &amp; Tracks.</a></li>
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


            <!-- <a id="categories"></a>
            <h2>Summit Tracks</h2>

            <p><a href="summit/austin-2016/categories/" target="_blank">Plan your week! See how the session tracks are grouped into audience categories, by day.</a></p>

            <% loop ActiveSummit.getCategories %>
            <p>
                <strong>$Title</strong><br/>
                $Description
            </p>
            <% end_loop %>

            <p class="submit-button-area"><a href="#submit" class="btn btn-default">Submit Your Presentation Proposal</a></p>


            <hr/> -->

            <a id="process"></a>
            <h2>About The Summit Submission and Selection Process</h2>
            
            <p>On average, we at the OpenStack Foundation receive more than 1500+ submissions for the Main Conference. Of those, we are only able to select 25-35% for participation, depending on the event. To decide which talks are accepted, we rely on Track Chairs, as well as community input through an open voting process that will be open to the community July 26 - August 8th, 2016.</p>
            <p>As Foundation Staff, our goal is to select Track Chairs who are subject matter experts who review submissions to their particular track, for example "storage" or "cloud app development." There are typically 3-4 chairs per track who review and collaboratively decide which presentations are accepted. The Foundation strives to recruit Track Chairs from a diverse set of companies, regions, roles in the community (i.e., contributing developers, users and business leaders) and areas of expertise.</p>
            <p>New to the Summit Submission Process for 2016:</p>
            <ul>
            <li>Speakers are limited to a maximum of three submissions, including panel participation.</li>
            <li>The submission forms include additional questions about how the proposed session meets the objectives of the OpenStack community.</li>
            <li>Speaker biographies are mandatory and should include any relevant OpenStack experience.</li>
            <li>Speakers are asked to provide information about past Summit presentations or other speaking examples, with links to previous presentations if possible.</li>
            <li>Speakers will be able to tag their proposals with keywords to help with the sorting process and ensure sessions are included in the correct track.</li>
            </ul>
            <p>Once the call for speakers has concluded in mid-July, all submissions will be made available for community vote and input. After community voting wraps up, Track Chairs will receive a slate of presentations to review and they will determine the final schedule. Community votes are meant to help inform the decision, but are not the only guide. Track chairs are expected to exercise judgment in their area of expertise and help ensure diversity. Real-world user stories and in-the-trenches experiences are favored over sales pitches.</p>
            <p>After track chairs make their decisions, speakers will be informed by early September 2016. If you are selected as a speaker (or alternate speaker), you will receive a free code to register for the Barcelona Summit, as well as a set of deadlines and deliverables leading up to the event.</p>
            <p>Track chair nomination for the Barcelona Summit closed June 20, 2016. The list of selected track chairs <a href="https://etherpad.openstack.org/p/r.764d79fb8fcd663f862735f621542f2c" target="_blank">is listed here</a>.</p>
            <p>Please note that this process covers the speaking sessions during the Summit, NOT the design summit working sessions. You can more about that process on the <a href="https://wiki.openstack.org/wiki/Design_Summit">OpenStack Wiki</a>.</p>
            <p>Want to provide feedback on this process? Join the discussion on the <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/community">openstack-community mailing list</a>, and/or contact the Foundation Summit Team directly <a href="mailto:summit@openstack.org">summit@openstack.org</a>.</p>
            <p>&nbsp;</p>

            <p class="submit-button-area"><a href="#submit" class="btn btn-default">Submit Your Presentation Proposal</a></p>



        </div>

    </div>
