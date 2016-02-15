<div class="container">
    <h1>Get Started</h1>
    <div class="row">
        <div class="col-lg-6">
            <h3>Already have an OpenStack Foundation login?</h3>
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
                $RegisterForm
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
