<% if $Top.ActiveSummit %>
    <% if $Top.ActiveSummit.isCallForSpeakersOpen %>
        <div class="presentation-app-header">
            <div class="container">
                <p class="status"><i class="fa fa-calendar"></i>&nbsp;
                Currently accepting presentation submissions until <strong>$ActiveSummit.SubmissionEndDate.Format('F jS, Y')</strong>.</p>
            </div>
        </div>
    <% end_if %>
<% end_if %>
<div class="presentation-app-body">
    <div class="container">
        <h1>Would you like to speak at the OpenStack Summit?</h1>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12" style="float: none; margin: 0 auto;">
                <div class="presentation-app-login-panel">
                    <h3>Complete your Speaker Registration</h3>
                    $RegistrationForm
                </div>
            </div>

        </div>
    </div>
