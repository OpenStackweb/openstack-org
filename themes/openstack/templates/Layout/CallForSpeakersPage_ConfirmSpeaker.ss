<% require themedCSS(conference) %> 



<div class="container summit">

	<% with Parent %>
	$HeaderArea
	<% end_with %>
	
  <div class="row">

		<!-- News Feed -->

                        <h1>Confrim Yourself as a Speaker</h1>
                        
        <p><strong>Hello $ConfirmedSpeaker.FirstName $ConfirmedSpeaker.Surname!</strong> Let's confirm you as a speaker at the OpenStack Summit. In order to present at the Summit, you need to agree to be recorded during your presentation.<br/><br/></p>
        <p></p>

        <p><strong>Please read the terms below and complete the short form.</strong></p>

		<div class="termsBox">
		    $VideoTerms
		</div>

		$OnsitePhoneForm
                

    </div></div>

$GATrackingCode