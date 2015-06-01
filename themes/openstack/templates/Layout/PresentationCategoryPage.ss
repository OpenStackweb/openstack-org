<% require themedCSS(videos) %>
</div>
		<% if $StillUploading %>
			<div class="still-uploading-row">
				<div class="container">
				<i class="fa fa-refresh fa-spin"></i>
				 Check back again soon! We're still uploading videos from The OpenStack Summit in Vancouver...
				</div>
			</div>	
		<% end_if %>
       <div class="intro-header featured videos">
					<% loop LatestPresentation %>
						<a href="{$Top.Link}presentation/{$URLSegment}" class="main-video">
							<div class="video-description-wrapper">
								<p class="latest-video">Latest Video Added</p>
								<h3>$Name</h3>
							</div>
							<div class="latest-video-img">
								<img src="//img.youtube.com/vi/{$YouTubeID}/0.jpg">
							</div>
						</a>
					<% end_loop %>
	        <div class="container">
	        <a href="$Top.Parent.Link" class="videos-back-summit"><i class="fa fa-chevron-left"></i> Back to the Summit Site</a>
	            <div class="row">
	                <div class="col-lg-8 col-lg-offset-2 col-sm-12">
	                    <div class="intro-message">
	                        <h1>$Title</h1>
	                    </div>
	                </div>
	            </div>
	        </div>
    	</div>

<div class="featured-row">
	<div class="container">
		<h2>
			Daily Recaps
			<span>Highlights from the OpenStack Summit in Vancouver</span>
		</h2>
	</div>
</div>
<div class="container daily-recap-wrapper">
	<div class="row">
		<% loop FeaturedVideos %>

		<!-- If there is a YouTube ID -->

		<% if YouTubeID %>

			<div class="col-sm-3 video-block">
				<a href="{$Top.Link}featured/{$URLSegment}">
					<div class="video-thumb">
						<div class="thumb-play"></div>
						<img class="video-thumb-img" src="//img.youtube.com/vi/{$YouTubeID}/0.jpg">
					</div>
					<p class="video-thumb-title">
						$Name
					</p>
				</a>
			</div>

		<% else %>


			<div class="col-sm-3">
				<div class="video-thumb">
					<img class="video-thumb-img" src="/themes/openstack/images/no-video.jpg">
				</div>
				<p class="video-thumb-title">
					Day {$Pos} - Coming Soon
				</p>
			</div>

		<% end_if %>


		<% end_loop %>
	</div>
</div>

<div class="sort-row">
	<div class="container">
		<div class="sort-left">
			<i class="fa fa-th active"></i>
			<i class="fa fa-th-list"></i>
		</div>
		<div class="sort-right">
			<div class="dropdown video-dropdown">
				<a data-toggle="dropdown" href="#">Select A Day <i class="fa fa-caret-down"></i></a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">

				<li role="presentation"><a role="menuitem" tabindex="-1" href="{$Top.Link}#keynotes">Keynote Presentations</a></li>


				<% loop  GroupedPresentations.GroupedBy(PresentationDay) %>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="{$Top.Link}#day-{$Pos}">$PresentationDay</a></li>
				<% end_loop %>

				</ul>
			</div>
		</div>
	</div>
</div>

<div class="container video-gallery">

<% if Keynotes %>
<div class="row">
	<div class="col-lg-12" id="keynotes">
		<h2 id="keynotes">Keynotes</h2>
	</div>
</div>

<div class="row">

<% loop Keynotes %>

    <!-- Video Block -->
    <% if YouTubeID %>
        <div class="col-sm-3 video-block">
            <a href="{$Top.Link}presentation/{$URLSegment}">
                <div class="video-thumb">
                    <div class="thumb-play"></div>
                    <img class="video-thumb-img" src="//img.youtube.com/vi/{$YouTubeID}/0.jpg">
                </div>
                <div class="video-details">
                    <p class="video-thumb-title">
                        $Name
                    </p>
                    <p class="video-thumb-speaker">
                        $Speakers
                    </p>
                </div>
            </a>
        </div>
    <!-- Slide Block -->
    <% else_if SlidesLink %>
        <div class="col-sm-3 video-block">
            <a href="{$Top.Link}presentation/{$URLSegment}">
                <div class="video-thumb">
                    <div class="thumb-play"></div>
                    <img class="video-thumb-img" src="/themes/openstack/images/no-video.jpg">
                </div>
                <div class="video-details">
                    <p class="video-thumb-title">
                        $Name
                    </p>
                    <p class="video-thumb-speaker">
                        $Speakers
                    </p>
                </div>
            </a>
        </div>
    <% end_if %>


  <% if MultipleOf(4) %>
      </div>
      <div class="row">
  <% end_if %>

  <% if Last %>
      </div>
  <% end_if %>

<% end_loop %>

</div>
<% end_if %>

<% include VideoThumbnails %>
