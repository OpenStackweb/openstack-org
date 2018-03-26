<div  class="summit-hero-wrapper<% if $top_section != 'full' %> condensed<% end_if %><% if HeroCSSClass %> $HeroCSSClass<% end_if %>" <% if $SummitImage %>style="background: rgba(0, 0, 0, 0) url('{$SummitImage.Image.link}') no-repeat scroll center center / cover ;"<% end_if %> >
    <div class="container">
        <div class="row">
            <% with $Summit %>
            <div class="col-sm-12">
                <a href="/summit">
                    <img class="summit-hero-logo" src="/themes/openstack/static/images/summit-logo-small-white.svg" alt="OpenStack Summit">
                </a>

                <% if IsUpComing %>
                    <div class="inner-countdown-wrapper">
                        <a href="{$RegistrationLink}" class="btn register-btn-lrg">Register Now</a>
                    </div>
                <% else_if IsCurrent %>
                    <div class="inner-countdown-wrapper">
                        <div class="countdown">
                            <span>N</span>
                            <span>O</span>
                            <span>W</span>
                        </div>
                        <div class="countdown-text">
                            The Summit is Happening Now!
                        </div>
                    </div>
                <% end_if %>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10 col-sm-push-1">
                <h2>
                    The Must-Attend
                </h2>
                <h1>
                    Open Infrastructure Event
                </h1>
                <div class="summit-hero-postcard">
                    <p>
                        The open infrastructure landscape is changing, and so is the Summit. Now that users are integrating dozens of open source tools into a modern stack that reaches well beyond the scope of OpenStack, we’re re-organizing the event to focus on specific problem domains like container infrastructure, edge computing and CI/CD and we are focusing on the hard work of integrating all of these tools developed in disparate communities. This is the essential work of 2018 and beyond, to ensure that open infrastructure is truly a viable path for operators.
                    </p>
                    <div class="landing-action">
                        <% if $RegistrationLink %>
                            <a href="{$RegistrationLink}" class="btn register-btn-lrg">Register Now</a>
                        <% end_if %>
                        <% if $ComingSoonBtnText %>
                            <button class="btn register-btn-lrg soon" href="#">{$ComingSoonBtnText}</button>
                        <% end_if %>
                        <% if ]$SecondaryRegistrationLink %>
                        &nbsp;&nbsp;
                        <a href="{$SecondaryRegistrationLink}"  class="btn register-btn-lrg">{$SecondaryRegistrationBtnText}</a>
                        <% end_if %>
                    </div>
                    <div class="landing-date">
                        <div class="left">
                            $Title
                        </div>
                        <div class="right">
                            $DateLabel
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <% end_with %>
        <a href="#" class="open-panel"><i class="fa fa-bars fa-2x collapse-nav"></i></a>
    </div>
    <div class="hero-tab-wrapper">
        <!-- Microsite Navigation -->
        <% include SummitNav %>
        <!-- End Microsite Navigation -->
    </div>
    <% if $SummitImage %><a href="#" class="photo-credit" data-toggle="tooltip" data-placement="left" title="{$SummitImage.Attribution}"><i class="fa fa-info-circle"></i></a><% end_if %>
</div>