</div>
      
<div class="intro-header featured livestream">

	<% if $VideoCurrentlyPlaying == 'Yes' %>
		<div class="container livestream-container">
            <div class="row">
                <div class="col-sm-10 col-sm-push-1">
                    <div class="intro-message">
                        <h1>Live Stream of the Summit Keynotes in Austin is Happening NOW</h1>
                    </div>
                    <div class="livestream-watch">
                        <a href="/home/video/" class="promo-btn">Watch It Now <i class="fa fa-play-circle"></i></a>
                    </div>
                    <div class="livestream-sponsor">
                        <a href="/summit">
                            <img src="/themes/openstack/images/summit-event-logo.png" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>

     <% else %>

        <div class="container livestream-container">
            <div class="row">
                <div class="col-sm-10 col-sm-push-1">
                    <div class="intro-message">
                        <h1>Live Video of the Summit Keynotes in Austin is Starting Soon</h1>
                    </div>
                    <div class="livestream-date">
                        <p>Live Stream Starting Right Here At</p>
                        <h4>$NextPresentationStartTime</h4>
                        <h5>$NextPresentationStartDate</h5>
                    </div>
                    <div class="livestream-sponsor">
                        <a href="/summit">
                            <img src="/themes/openstack/images/summit-event-logo.png" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>

     <% end_if %>

    </div>


    <% include LiveStreamLinks %>

    <% include HomePageBottom %>