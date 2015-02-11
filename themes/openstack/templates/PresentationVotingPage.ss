<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Vote For Vancouver Summit Presentations | OpenStack Open Source Cloud Computing Software</title>

    <!-- Bootstrap Core CSS -->
    <link href="/themes/openstack/css/bootstrap3.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/themes/openstack/css/voting-app.css" rel="stylesheet">


    <!-- Fonts -->
    <link href="/themes/openstack/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Page-specific CSS -->
    

</head>

<body class="voting-body">
    <div class="outer-wrap">


    <!-- For FB sharing -->
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=600413776716536&version=v2.0";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <!-- For Google+ Sharing -->
    <script src="https://apis.google.com/js/platform.js" async defer></script>


        <div class="main-body">
            <div id="wrap">

                <!-- Begin Page Content -->
                <a href="#" class="voting-open-panel"><i class="fa fa-bars fa-2x collapse-voting-nav"></i></a>
<div class="container">
	<div class="row">
		<div class="voting-header">
			<div class="col-lg-3 col-md-3 col-sm-3">
				<div class="voting-app-logo">
					<img class="summit-hero-logo" src="/themes/openstack/images/voting/voting-logo.svg" onerror="this.onerror=null; this.src=/themes/openstack/images/voting/voting-logo.png" alt="OpenStack Summit" />
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<div class="voting-app-title">
					<h1>
						Vote For Presentations
						<span>
							Help us pick the presentations for The Vancouver Summit
						</span>
					</h1>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3">
				<div class="voting-app-details">
					<a href="/summit/" class="btn">Summit Details</a>
				</div>
			</div>
		</div>
	</div>
	
	<!-- TOP BAR AREA ----------------------------------->
	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-3 voting-sidebar">
		    <div class="voting-app-details-link">
                <a href="#">More About The Vancouver Summit</a>
            </div>
            $SearchForm

<!-------------------------------------------------->	
<!-- NORMAL MODE ----------------------------------->
<!-------------------------------------------------->	

<% if SearchMode != TRUE %>	
			
<!-- DROP DOWN FOR CATEGORIES ----------------------------------->
<div class="btn-group voting-dropdown">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
       <% if $CategoryName %>
           $CategoryName
        <% else %>
            All Presentations
        <% end_if %>
       <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
        <li>
          <a href='{$Top.Link}Category/All'>All Categories</a>
        </li>
        <li class='divider'></li>

        <% loop CategoryLinks %>
        <li>
          <a href='{$Top.Link}Category/{$URLSegment}'>$Name</a>
        </li>
        <% end_loop %>
    </ul>
</div>



<!-- PRESENTATION LIST / BROWSER ----------------------------------->
<% if $CategoryName %>
    <h5>Presentations In This Category</h5>
<% else %>
    <h5>Showing All Presentations</h5>
<% end_if %>
<ul class="presentation-list">
  <% if $CurrentMember %>
  <% loop $CurrentMember.getRandomisedPresentations($CategoryID) %>
         
      <li 
          <% if $Top.Presentation.ID == $ID %>
              class="active"
          <% else_if $MemberVoteValue %>
              class="completed"
          <% end_if %>
      
      ><a href="{$Top.Link}presentation/{$URLSegment}">$PresentationTitle</a></li>
  <% end_loop %>
  <% else %>
  
  <% loop $LoggedOutPresentationList($CategoryID) %>
         
      <li 
          <% if $Top.Presentation.ID == $ID %>
              class="active"
          <% end_if %>
      
      ><a href="{$Top.Link}presentation/{$URLSegment}">$PresentationTitle</a></li>
  <% end_loop %>
  
  
  <% end_if %>
</ul>
		</div>
		
		<!-- PRESENTATION DISPLAY ----------------------------------->
		<div class="col-lg-9 col-md-9 col-sm-9 voting-content-body-wrapper">
			<a href="#" class="voting-open-panel text"><i class="fa fa-chevron-left"></i>All Submissions</a>
			<% loop Presentation %>
			
           	<div class="voting-content-body">

           <% if not $CurrentMember %>
            <!-- LOG IN TO VOTE  ----------------------------------->
				<h5>Login to vote</h5>
                <div class="login-to-vote">
                    <h3>Help this presentation get to the OpenStack Summit!</h3>
                    <p>OpenStack community members are voting on presentations to be presented at the OpenStack Summit, November 3-7, in Paris, France. We received hundreds of high-quality submissions, and your votes can help us determine which ones to include in the schedule.</p>
                    $Top.SpeakerVotingLoginForm
                </div>
			
			<% else %>
			<!-- CAST YOUR VOTE  ----------------------------------->
				<h5>Cast Your Vote</h5>
				<ul class="voting-rate-wrapper">
					<li class="voting-rate-single <% if $Top.VoteValue = 3 %>current-vote<% end_if %>">
					   <a href="{$Top.Link}SaveRating/?id={$ID}&rating=3" id='vote-3'>
                            Would Love To See!
                            <div class="voting-shortcut">3</div>
                        </a>
					</li>
					<li class="voting-rate-single <% if $Top.VoteValue = 2 %>current-vote<% end_if %>">
					   <a href="{$Top.Link}SaveRating/?id={$ID}&rating=2" id='vote-2'>
                            Would Try To See
                            <div class="voting-shortcut">2</div>
                        </a>
                    </li>
					<li class="voting-rate-single <% if $Top.VoteValue = 1 %>current-vote<% end_if %>">
					   <a href="{$Top.Link}SaveRating/?id={$ID}&rating=1" id='vote-1'>
                            Might See
                            <div class="voting-shortcut">1</div>
                        </a>
                    </li>
					<li class="voting-rate-single <% if $Top.VoteValue = -1 %>current-vote<% end_if %>">
					   <a href="{$Top.Link}SaveRating/?id={$ID}&rating=-1" id='vote-0'>
                            Would Not See
                            <div class="voting-shortcut">0</div>
                        </a>
                    </li>                                        
				</ul>
				<% end_if %>
				
				
				
				<div class="voting-presentation-title">
					<h5>Title</h5>
					<h3>$PresentationTitle</h3>
				</div>
				<div class="voting-presentation-body">
					<h5>Speakers</h5>
                    <% if Speakers %>
					<div class="voting-speaker-row">
						<ul>
                              <% loop Speakers %>
                                <li>
                                    <img class="voting-speaker-pic" src="{$Photo.SetRatioSize(80,80).URL}" />
                                    <div class="voting-speaker-name">
                                        $FirstName $Surname
                                        <span>$Title</span>
                                    </div>
                                </li>                              
                              <% end_loop %>
						</ul>
					</div>
                    <% end_if %>						

					<p>
						<h5>Abstract</h5>
					</p>
					
					<div>$Abstract</div>
					
					<div class="main-speaker-wrapper">
					<% loop Speakers %>					
						<div class="main-speaker-row">
							<div class="voting-speaker-name">
								$FirstName $Surname
								<span>$Title</span>
							</div>
							<img class="voting-speaker-pic" src="{$Photo.SetRatioSize(80,80).URL}" />
						</div>
						<div class="main-speaker-description">
							$Bio
						</div>
					<% end_loop %>
                    </div>
				</div>
				<h5>Cast Your Vote</h5>
				<ul class="voting-rate-wrapper">
					<li class="voting-rate-single <% if $Top.VoteValue = 3 %>current-vote<% end_if %>">
					   <a href="{$Top.Link}SaveRating/?id={$ID}&rating=3" id='vote-3'>
                            Would Love To See!
                            <div class="voting-shortcut">3</div>
                        </a>
					</li>
					<li class="voting-rate-single <% if $Top.VoteValue = 2 %>current-vote<% end_if %>">
					   <a href="{$Top.Link}SaveRating/?id={$ID}&rating=2" id='vote-2'>
                            Would Try To See
                            <div class="voting-shortcut">2</div>
                        </a>
                    </li>
					<li class="voting-rate-single <% if $Top.VoteValue = 1 %>current-vote<% end_if %>">
					   <a href="{$Top.Link}SaveRating/?id={$ID}&rating=1" id='vote-1'>
                            Might See
                            <div class="voting-shortcut">1</div>
                        </a>
                    </li>
					<li class="voting-rate-single <% if $Top.VoteValue = -1 %>current-vote<% end_if %>">
					   <a href="{$Top.Link}SaveRating/?id={$ID}&rating=-1" id='vote-0'>
                            Would Not See
                            <div class="voting-shortcut">0</div>
                        </a>
                    </li>                                        
				</ul>
					<div class="voting-tip">
						<strong>TIP:</strong> You can vote quickly with your keyboard using the numbers below each option.
					</div>
				<div class="voting-share-wrapper">
						<h5>Share This Presentation</h5>
						<div class="sharing-section">
							<div class="single-voting-share">
								<a href="https://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
							</div>
							<div class="single-voting-share">
								<div class="g-plus" data-action="share" data-annotation="none"></div>
							</div>
							<div class="single-voting-share fb-share">
								<div class="fb-share-button" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button"></div>
							</div>

                    		
						</div>
				</div>
			</div>
			<% end_loop %>
		</div>
		
<!-------------------------------------------------->	
<!-- SEARCH MODE ----------------------------------->
<!-------------------------------------------------->	

<% else %>

<!-- PRESENTATION LIST / BROWSER ----------------------------------->
<h5>Search Results</h5>
 <ul class="presentation-list">
  <% loop $SearchResults %>
         
      <li><a href="{$Top.Link}presentation/{$URLSegment}">$PresentationTitle</a></li>
  <% end_loop %>
</ul></div>

		<div class="col-lg-9 col-md-9 col-sm-9 voting-content-body-wrapper">
			<a href="#" class="voting-open-panel text"><i class="fa fa-chevron-left"></i>Search Results</a>
            <p>Select an entry on the left to see the details here.</p>
        </div>

<% end_if %>

		
		
	</div>
</div>

                <!-- End Page Content -->
            </div>
             

            <script>
            function goBack() {
                window.history.back()
            }
            </script>

        </div>
    </div>
</body>

</html>