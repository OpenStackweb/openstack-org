</div>
      
<div class="intro-header featured livestream">

	<% if $VideoCurrentlyPlaying == 'Yes' %>
		<div class="container livestream-container">
            <div class="row">
                <div class="col-sm-10 col-sm-push-1">
                    <div class="intro-message">
                        <h1>Live Stream of the Summit Keynotes in Vancouver is Happening NOW</h1>
                    </div>
                    <div class="livestream-watch">
                        <a href="/home/video/" class="promo-btn">Watch It Now <i class="fa fa-play-circle"></i></a>
                    </div>
                    <div class="livestream-sponsor">
                        <a href="#">
                            Brought to you by
                            <img src="/themes/openstack/images/homepage/intel.png" alt="">
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
                        <h1>Live Video of the Summit Keynotes in Vancouver is Starting Soon</h1>
                    </div>
                    <div class="livestream-date">
                        <p>Live Stream Starting Right Here At</p>
                        <h4>$NextPresentationStartTime</h4>
                        <h5>$NextPresentationStartDate</h5>
                    </div>
                    <div class="livestream-sponsor">
                        <a href="#">
                            Brought to you by
                            <img src="/themes/openstack/images/homepage/intel.png" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>

     <% end_if %>

    </div>


    <div class="livestream-links">
            <ul>
                <li><a href="#">Get the app</a></li>
                <li><a href="/summit/vancouver-2015/schedule/">Full Schedule</a></li>
                <li><a href="#">All Videos</a></li>
                <li><a href="/summit/vancouver-2015/sponsors/">Sponsors</a></li>
            </ul>
        </div>

    <% include HomePageBottom %>