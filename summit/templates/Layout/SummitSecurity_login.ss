</div>
    <div class="presentation-app-header">
        <% if $Top.ActiveSummit %>
            <% if $Top.ActiveSummit.isCallForSpeakersOpen %>
                <div class="container">
                    <p class="status">
                        <i class="fa fa-calendar"></i>&nbsp;{$Top.PresentationDeadlineText}</p>
                </div>
            <% end_if %>
        <% end_if %>
    </div>
    <div class="presentation-app-body">
        <div class="container">
            <h1>Would you like to submit a session to the Forum at the OpenStack Summit?</h1>

            <h2>Submit Your Forum Proposal</h2>

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

            <hr/>

            <h2>How does the Forum work?</h2>
            <p>The Forum is a part of the OpenStack Summit. It is not a classic conference track with speakers and presentations. OpenStack community members (participants in development teams or working groups, and other interested individuals) discuss the topics they want to cover and get alignment on.</p>

            <p>Those scheduled sessions can include the presentation of a few slides but are generally a 40-min long, open brainstorming discussion on a given subject or feature. If you care about a particular subject, please join. Due to the nature of the event, the schedule is a bit dynamic, so check out the summit schedule pages often.</p>

            <p>If you suggest a session, you should be ready to moderate that session and make sure the discussion stays on track. Experienced attendees will generally help in that endeavour, but you should plan to attend that session yourself.</p>

            <p>The Forum is not the right place to get started or learn the basics of OpenStack. For that it's better to check the various OpenStack meetups organized by user groups around the world, attend talks in the 'Conference' part of the OpenStack Summit, or participate in classes in the 'Academy' part of the OpenStack Summit.</p>

            <p>For additional information, <a href="https://wiki.openstack.org/wiki/Forum" target="_blank">please take a look at the Forum Wiki</a>.</p>
        </div>
    </div>