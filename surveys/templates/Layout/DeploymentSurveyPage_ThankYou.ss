<h1>OpenStack User Survey</h1>

<div class="container">

    <% include DeploymentSurveyPageNavigation %>
	
	<div class="row">
       
        <form>
            <fieldset class="fake">
            
                $ThankYouContent

                <% if CurrentMember.Password %>
                <% else %>
                    <h2>Create A Password For Return Visits</h2>
                    <p>If you'd like to be able to return and update this information in the future, you
                    may choose a password below.</p>
                    $SavePasswordForm
                <% end_if %>
            </fieldset>
        </form>
    </div>
</div>

