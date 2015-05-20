<div class="container">
    <h1>Welcome to the OpenStack User Survey!</h1>

    <div class="row">

        <div class="col-lg-4">
            <div id="user">
                <p></p><p>This is the <strong>OpenStack User Survey</strong> for OpenStack cloud users and operators.</p><p></p>
            </div>
        </div>

        <div class="col-lg-4">
            <div id="time">
                <p></p><p>It should only take <strong>10 minutes</strong> to complete.</p><p></p>
            </div>
        </div>

        <div class="survey-box col-lg-4">
            <div id="private">
                <p></p><p>All of the information you provide is <strong>confidential</strong> to the Foundation (unless you specify otherwise).</p><p></p>
            </div>
        </div>

    </div>
    <p>This survey provides users an opportunity to influence the community and software
        direction. By sharing information about your configuration and requirements, the OpenStack
        Foundation User Committee will be able to advocate on your behalf.</p>
    <p><a class="roundedButton" href="/user-survey/faq">More Information About The Survey</a></p>
    <hr>
    <h1>Get Started</h1>
    <div class="row">
        <div class="col-lg-6">

            <h3>Already have an OpenStack Foundation login?</h3>

            <div class="survey-login-wrapper">

                <form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={$BackURL}" method="post"
                      enctype="application/x-www-form-urlencoded">
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
                $RegisterForm
            </div>
        </div>
    </div>
</div>