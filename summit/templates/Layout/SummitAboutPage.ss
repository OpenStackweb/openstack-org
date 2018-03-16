<div class="light secondary-nav" id="nav-bar">
    <div class="container">
        <ul class="secondary-nav-list">
            <li>
                <a href="#who-should-attend">
                    <i class="fa fa-check-circle"></i>
                    Who Should Attend
                </a>
            </li>
            <li>
                <a href="#featured-speakers">
                    <i class="fa fa-microphone"></i>
                    Featured Speakers
                </a>
            </li>
            <li>
                <a href="#openstackacademy">
                    <i class="fa fa-graduation-cap"></i>
                    OpenStack Academy
                </a>
            </li>
            <li>
                <a href="#forum">
                    <i class="fa fa-flask"></i>
                    The Forum
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="white about-summit with-arrow" id="who-should-attend">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-8">
                <h1>
                  Who Should Attend?
                </h1>
                $WhoShouldAttendText
            </div>
            <div class="col-lg-3 col-md-4 col-sm-4" style="margin-top:30px;">
              <ul class="help-me-menu">
                <% loop $Links().Sort('Order') %>
                  <li>
                      <a href="{$URL}">
                          <% if $IconClass %> <i class="fa {$IconClass}"></i> <% end_if %>
                          $Label
                      </a>
                  </li>
                <% end_loop %>
              </ul>
            </div>
        </div>
    </div>
</div>

<div class="light summit-users-wrapper" id="featured-speakers">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2>{$FeaturedSpeakersTitle}</h2>
      </div>
    </div>
    <div class="row">
        <% loop $FeaturedSpeakers().Sort('Order') %>
            <div class="col-sm-3 featured">
                <div class="summit-user-section">
                    <div class="summit-user-image-box">
                      <img src="{$ProfilePhoto(400)}" alt="{$getName()}" class="summit-user-image">
                    </div>
                    <div class="name">{$getName()}</div>
                    <div class="title">{$getTitleNice()}</div>
                </div>
            </div>
        <% end_loop %>
    </div>
  </div>
</div>
<div class="summit-gallery-section">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="gallery-content">
          <a href="{$RegisterLink}" target="_blank" class="btn register-btn-lrg" target="_blank">Register Now</a>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="academy-wrapper" id="openstackacademy">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2>Get Hands On With OpenStack Academy</h2>
      </div>
    </div>
    {$AcademyText}
    <div class="row">
      <div class="col-sm-12">
        <a href="{$AcademyLink}" class="btn academy-cta">Read more about OpenStack Academy</a>
      </div>
    </div>
    <div class="row" id="forum">
      <div class="col-sm-12">
        <hr style="margin-top:60px;">
        <h1 style="margin-bottom: 20px;">The Forum</h1>
        <i class="fa fa-flask" style="color: #3E9B85;font-size: 5.5em;margin-bottom: 20px;"></i>
        <div class="row">
          <div class="col-sm-8 col-sm-push-2">
            {$ForumText}
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <a href="{$ForumLink}" class="btn academy-cta" target="_blank">Read more about the Forum</a>
      </div>
    </div>
  </div>
</div>

<div class="summit-more-wrapper">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <hr style="margin-top:60px;">
          <h3 class="recap-title">{$HighLightsTitle}</h3>
          <% loop $Highlights().Sort('Order')  %>
              <div class="about-video-wrapper">
                  <iframe src="//www.youtube.com/embed/{$YoutubeID}?rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
                  <p class='video-catpion'>{$Caption}</p>
              </div>
          <% end_loop %>
      </div>
    </div>
  </div>
</div>
<div class="growth austin">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="growth-text-top map">
                    <h2>Join The Movement</h2>
                    <p>
                        {$JoinMovementText1}
                    </p>
                </div>
                <div class="growth-map-wrapper">
                    <img class="growth-map tokyo" src="{$JoinMovementImage.getURL()}" alt="OpenStack Summit Growth Chart">
                    <img class="growth-chart-legend map" src="/themes/openstack/static/images/tokyo/map-legend.svg" alt="OpenStack Summit Map Legend"> 
                </div>
                <div class="growth-text-bottom map">
                    <p>
                        {$JoinMovementText2}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="register-promo-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-push-2">
          <h2>{$JoinUsTitle}</h2>
          {$JoinUsText}
          <p>
            <a href="{$RegisterLink}" target="_blank" class="btn" target="_blank">Register Now</a>
          </p>
            </div>
        </div>
    </div>
</div>
