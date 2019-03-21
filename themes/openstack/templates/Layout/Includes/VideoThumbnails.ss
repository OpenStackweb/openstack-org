<div class="container video-gallery">
    <!-- Start Videos -->
        <% loop GroupedPresentations.GroupedBy(PresentationDay) %>
            <div class="row">
                <div class="col-lg-12">
                    <h2 id="day-{$Pos}">$PresentationDay</h2>
                </div>
            </div>

            <div class="row">

            <% loop Children %>

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
                <!-- Slides Block -->
                <% else_if SlidesLink %>
                  <div class="col-sm-3 video-block">
                    <a href="{$Top.Link}presentation/{$URLSegment}">
                      <div class="video-thumb">
                        <div class="thumb-play"></div>
                        <img class="video-thumb-img" src="{$Top.CloudUrl("images/no-video.jpg")}">
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
      <% end_loop %>

    </div>
  </div>
  <!-- End Videos -->
