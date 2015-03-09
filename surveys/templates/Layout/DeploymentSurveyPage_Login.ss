<div class="container">
    <h1>$LoginPageTitle</h1>

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
    <div class="row">
        <div class="col-lg-6">

            <h3>Already have an OpenStack Foundation login?</h3>

            <div class="survey-login-wrapper">

                <form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={$Link}" method="post"
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