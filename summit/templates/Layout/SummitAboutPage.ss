<div class="light secondary-nav" id="nav-bar">
    <div class="container">
        <ul class="secondary-nav-list">
            <% loop $getNavSections() %>
            <li>
                <a href="#{$Name}">
                    <i class="fa {$IconClass}"></i>
                    $Title
                </a>
            </li>
            <% end_loop %>
        </ul>
    </div>
</div>

<% loop $getPageSections() %>
    <% if $isClass('PageSectionLinks') %>
    <div class="white about-summit with-arrow" id="{$Name}">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-md-8 col-sm-8">
                    <h1> {$Title} </h1>
                    <% if $isClass('PageSectionText') %>
                        {$Text}
                    <% end_if %>
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
    <% else_if $isClass('PageSectionSpeakers') %>
    <div class="light summit-users-wrapper" id="{$Name}">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h2>{$Title}</h2>
                </div>
            </div>
            <div class="row">
                <% loop $Speakers.Sort('Order') %>
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
    <% else_if $isClass('PageSectionVideos') %>
    <div class="summit-more-wrapper" id="{$Name}">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <hr style="margin-top:60px;">
                    <h3 class="recap-title">{$Title}</h3>
                    <% loop $Videos().Sort('Order')  %>
                        <div class="about-video-wrapper">
                            <iframe src="//www.youtube.com/embed/{$YoutubeID}?rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
                            <p class='video-catpion'>{$Caption}</p>
                        </div>
                    <% end_loop %>
                </div>
            </div>
        </div>
    </div>
    <% else_if $isClass('PageSectionMovement') %>
    <div class="growth austin" id="{$Name}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="growth-text-top map">
                        <h2>{$Title}</h2>
                        {$TextTop}
                    </div>
                    <div class="growth-map-wrapper">
                        <img class="growth-map tokyo" src="{$Picture.getURL()}" alt="OpenStack Summit Growth Chart">
                        <img class="growth-chart-legend map" src="/themes/openstack/static/images/tokyo/map-legend.svg" alt="OpenStack Summit Map Legend">
                    </div>
                    <div class="growth-text-bottom map">
                        {$TextBottom}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <% else_if $isClass('PageSectionPicture') %>
    <div class="{$WrapperClass}" id="{$Name}">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h2>{$Title}</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <% if $Text %>
                        $Text
                    <% end_if %>
                    <% if $Picture %>
                        <div style="text-align: center">
                            $Picture.getTag()
                        </div>
                    <% end_if %>
                </div>
            </div>
        </div>
    </div>
    <% else_if $isClass('PageSectionText') %>
    <div class="{$WrapperClass}" id="{$Name}">
        {$Text}
    </div>
    <% end_if %>
<% end_loop %>
