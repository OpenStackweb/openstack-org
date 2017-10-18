</div> <!-- Killing the main site .container -->
$ModuleCSS('main')
<div class="user-stories-hero container">
    <div class="row">
        <div class="col-sm-10 col-sm-push-1">
            $HeaderText
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div id="user-stories-video-trigger" class="user-story-main-bkgd" style="background-image: url($HeroImage.getURL())">
                <span class="text-wrapper">
                    $HeroText
                    <span class="user-story-main-btn">Watch The Video <i class="fa fa-play-circle"></i></span>
                </span>
            </div>
            <div class="user-stories-video-wrapper">
                <iframe class="user-stories-hero-video" src="https://www.youtube.com/embed/{$YouTubeID}?rel=0&amp;showinfo=0&amp;autoplay=0" frameborder="0" allowfullscreen=""></iframe>
            </div>
        </div>
    </div>
</div>

<div id="user-stories-container"></div>

<div class="container">
  <div class="row user-stories-cta">
    <div class="col-sm-12">
      <p>
        Share your story, and take the OpenStack user survey
        <a href="https://www.openstack.org/user-survey/survey-2016/landing?BackURL=/user-survey/survey-2016/" class="user-story-add-btn">Take the User Survey</a>
      </p>
    </div>
  </div>
</div>

<% include EnterpriseEvents %>


<script type="text/javascript">
	window.UserStoriesConfig = $JSONConfig;
</script>
$ModuleJS('main')
$ModuleCSS('main')
