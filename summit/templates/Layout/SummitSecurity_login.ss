
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
                <li><span>1</span> <a href="summit/boston-2017/categories/" target="_blank">Review the list of Summit Categories &amp; Tracks.</a></li>
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
            <p>On average, the OpenStack Foundation receives more than 1,500 submissions for the Summit. Of those, we are only able to select 25-35% for participation, depending on the event. To decide which talks are accepted, we rely on Track Chairs as well a community voting process that will open in February 2017.</p>
            <p>The Foundation selects Track Chairs who are subject matter experts to review submissions in their track, for example "storage" or "cloud app development." There are typically 3-4 chairs per track who collaboratively decide which presentations to accept. The Foundation strives to recruit Track Chairs from a diverse set of companies, regions, roles in the community (i.e., contributing developers, users and business leaders) and areas of expertise.</p>
            <h2>New to the Summit Submission Process for 2017</h2>
            <ul>
            <li>To continue to improving the quality of the content at the Summit, the OpenStack Foundation will be extending invitations directly to a small number of highly regarded speakers from past events for each track. This was added in response to community requests for specialized sessions, presenter diversity, advanced technical talks and other content that adds to the overall Summit content quality, but was not being addressed in the existing CFP process. This approach will allow the Foundation to lock down key speakers earlier in the process to better serve our community, while making up the less than 10% of the total sessions.</li>
            </ul>
            <p>Once the call for speakers has concluded in mid-January, all submissions will be made available for community vote and input. After community voting closes, Track Chairs will receive their presentations to review and they will determine the final selections for their respective tracks. Community votes are meant to help inform the decision, but are not the only guide. Track chairs are expected to exercise judgment in their area of expertise and help ensure diversity. Real-world user stories and in-the-trenches experiences are favored over sales pitches.</p>
            <p>After track chairs make their decisions, speakers will be informed by mid March 2017. If you are selected as a speaker (or alternate speaker), you will receive a free code to register for the Boston Summit, as well as a set of deadlines and deliverables leading up to the event.</p>
            <p>Track chair nominations for the Boston Summit are now open <a href="https://openstackfoundation.formstack.com/forms/openstack_summit_boston2017_track_chair_nominations" target="_blank">here</a> and will close January 18, 2017. Notifications will go out to selected track chairs by January 25, 2017.</p>
            <p>Please note that this process covers the speaking sessions during the Summit, NOT the Forum sessions. You can more about that process on the <a href="https://wiki.openstack.org/wiki/Forum" target="_blank">OpenStack Wiki</a>.</p>
            <p>Want to provide feedback on this process? Join the discussion on the <a href="http://lists.openstack.org/cgi-bin/mailman/listinfo/community" target="_blank">openstack-community mailing list</a>, and/or contact the Foundation Summit Team directly <a href="mailto:summit@openstack.org">summit@openstack.org</a>.</p>
            <p>&nbsp;</p>

            <p class="submit-button-area"><a href="#submit" class="btn btn-default">Submit Your Presentation Proposal</a></p>

            <!-- <hr />

            <a id="categories"></a>
            <h2>Summit Tracks</h2>

            <p><a href="summit/boston-2016/categories/" target="_blank">Plan your week! See how the session tracks are grouped into audience categories, by day.</a></p>

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