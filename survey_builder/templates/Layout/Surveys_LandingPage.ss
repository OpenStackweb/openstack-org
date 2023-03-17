<style>
    .hero-survey {
        background-color: transparent;
        background-image: url('{$Top.CloudUrl("/images/user-survey/report_cover_image.jpg")}');
        background-repeat: no-repeat;
        background-position: left center;
        background-attachment: scroll;
        background-size: 100%;
        height: 300px;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: none;
    }

    .hero-survey i.fa.fa-info-circle {
        display: none;
    }
</style>
<div class="container">
    <h1>$LoginPageTitle</h1>
    <!-- user survey report -->
    <div class="row">
        <div class="col-lg-12">
            <div class="condensed hero-survey">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h1 style="color: white">See the results from the latest User Survey</h1>
                        </div>
                    </div>
                    <div title="Photo by the Open Infrastructure Foundation" data-placement="left" data-toggle="tooltip"
                         class="hero-credit" data-original-title="Photo by the Open Infrastructure Foundation">
                        <a target="_blank" href="#"><i class="fa fa-info-circle"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h3>OpenStack users share their opinions and deployment choices to inform the roadmap</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p>
                Read more from the sixth survey of OpenStack users since April 2013, with a goal of better
                understanding attitudes, organizational profiles, use cases, and technology choices across the
                community’s various deployment stages and sizes.
            </p>
            <p>
                <a class="roundedButton" href="{$Top.CloudUrl('assets/survey/Public-User-Survey-Report.pdf')}" target="_blank">Download the report</a>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h3>See prior surveys</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <p>
                Learn more about past User Survey data to see how OpenStack is growing and maturing.
            </p>
            <ul class="list-unstyled">
                <li>
                    <a href="http://superuser.openstack.org/articles/user-survey-identifies-leading-industries-and-business-drivers-for-openstack-adoption"
                       target="_blank">May 2015 Demographics</a></li>
                <li>
                    <a href="http://superuser.openstack.org/articles/user-survey-identifies-leading-industries-and-business-drivers-for-openstack-adoption"
                       target="_blank">May 2015 Business drivers</a></li>
                <li>
                    <a href="http://superuser.openstack.org/articles/openstack-users-share-how-their-deployments-stack-up"
                       target="_blank">May 2015 Deployments</a></li>
                <li><a href="http://superuser.openstack.org/articles/openstack-user-survey-insights-november-2014"
                       target="_blank">November 2014 Full report</a></li>
            </ul>
        </div>
    </div>
    <!-- end - user survey report -->
    <hr/>
    <div class="row">

        <div class="col-lg-4">
            <div id="user">
                <p>$LoginPageSlide1Content</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div id="time">
                <p>$LoginPageSlide2Content</p>
            </div>
        </div>

        <div class="survey-box col-lg-4">
            <div id="private">
                <p>$LoginPageSlide3Content</p>
            </div>
        </div>

    </div>
    $LoginPageContent
    <hr/>
    <h1>Get Started</h1>
    <div class="row">
        <div class="col-lg-6">
            <h3>Already have an Open Infrastructure Foundation login?</h3>
            <div class="survey-login-wrapper">
                <form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={$BackURL}" method="post"
                      enctype="application/x-www-form-urlencoded">
                    <input type="hidden" name="fragment" id="fragment"/>
                    <div class="Actions">
                        <input class="action " id="MemberLoginForm_LoginForm_action_dologin" type="submit"
                               name="action_dologin" value="Log in" title="Log in"/>
                        <p id="ForgotPassword"><a href="Security/lostpassword">I've lost my password</a></p>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <h3>Don't have a login? Start here.</h3>

            <div class="survey-login-wrapper">
                <a class="btn btn-default" href="/join/register/signup?BackURL=$Top.Link" role="button">Sign up</a>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            var param = $('#fragment');
            param.val(window.location.hash);
        });
    </script>
</div>
