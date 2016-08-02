</div>
<div class="new-design">
    <div class="container">
        <h1>$HeaderTitle</h1>
        <div class="text-center page-descr">$HeaderText</div>
    </div>

    <div class="software-tab-wrapper">
        <div class="container">
            <ul class="nav nav-tabs project-tabs">
                <li class="nav-item tab-get_involved">
                    <a href="#get_involved" class="nav-link" data-toggle="tab" role="tab">GET INVOLVED</a>
                </li>
                <li class="nav-item tab-events">
                    <a href="#events" class="nav-link" data-toggle="tab" role="tab">NAVIGATING EVENTS</a>
                </li>
                <li class="nav-item tab-collateral">
                    <a href="#collateral" class="nav-link" data-toggle="tab" role="tab">COLLATERAL</a>
                </li>
                <li class="nav-item tab-software">
                    <a href="#software" class="nav-link" data-toggle="tab" role="tab">SOFTWARE RELEASES</a>
                </li>
                <li class="nav-item tab-graphics">
                    <a href="#graphics" class="nav-link" data-toggle="tab" role="tab">GRAPHICS & VIDEO</a>
                </li>
                <li class="nav-item tab-promote_product active">
                    <a href="#promote_product" class="nav-link" data-toggle="tab" role="tab">PROMOTE YOUR PRODUCT</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="tab-content">
        <div id="get_involved" class="tab-pane fade in tab-page involved" role="tabpanel">
            <div class="container">
                <div class="inline-images">
                    <% loop InvolvedImages() %>
                        $getTag()
                    <% end_loop %>
                </div>

                $InvolvedText
            </div>
        </div>
        <div id="events" class="tab-pane fade in tab-page" role="tabpanel">
            <div class="container">
                <div class="tab-header">$EventsIntroText</div>
                <div class="row events-list">
                    <% loop SponsorEvents() %>
                    <div class="col-md-4 pd-8">
                        $Image.getTag()
                        <div class="br-block">
                            <h3 class="blue-title">$Title</h3>
                            <span class="small-descr">$Description</span>
                            <span class="hr"></span>
                            <a href="$ContactLink" class="red-button">CONTACT US</a>
                        </div>
                    </div>
                    <% end_loop %>
                </div>
            </div>
        </div>
        <div id="collateral" class="tab-pane fade in tab-page" role="tabpanel">
            <div class="container">
                <div class="tab-header">$CollateralIntroText</div>
                <div class="row colateral">
                    <% loop Collaterals() %>
                    <div class="col-md-4 pd-8">
                        $Image.getTag()
                        <div class="br-block">
                            <h3 class="blue-title">
                                $Title
                                <% if $ShowGlobe %>
                                <i class="fa fa-globe" aria-hidden="true"></i>
                                <% end_if %>
                            </h3>
                            <span class="small-descr">$Description</span>
                            <span class="hr"></span>
                            <% loop $CollateralFiles %>
                            <p>
                                <span class="left-info">$Title</span>
                                <a href="$Link()" class="download">DOWNLOAD</a>
                            </p>
                            <% end_loop %>
                            <% loop $CollateralLinks %>
                            <p>
                                <span class="left-info">$Title</span>
                                <a href="$Link" class="download">DOWNLOAD</a>
                            </p>
                            <% end_loop %>
                        </div>
                    </div>
                    <% end_loop %>
                </div>
            </div>
        </div>
        <div id="software" class="tab-pane fade in tab-page" role="tabpanel">
            <div class="container software">
                <div class="tab-header">$SoftwareIntroText</div>
                <div class="row">
                    <% loop $Software() %>
                    <div class="brt1">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="image-logo col-md-2 col-xs-4">
                                    $Logo.getTag()
                                </div>
                                <div class="col-md-10 col-xs-8">
                                    <h3 class="blue-title">$Name</h3>
                                    <span class="small-descr">$Description</span>
                                    <a href="" class="view-more">View Release Details ></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 brl1">
                            <p>
                                <span class="left-info">Video</span>
                                <% if $YoutubeID %>
                                <a href="https://www.youtube.com/watch?v={$YoutubeID}" target="_blank" class="download">WATCH</a>
                                <% else %>
                                <span class="soon">COMING SOON</span>
                                <% end_if %>
                            </p>
                            <p>
                                <span class="left-info">Logo</span>
                                <% if $Logo.Exists %>
                                <a href="" class="download">DOWNLOAD</a>
                                <% else %>
                                <span class="soon">COMING SOON</span>
                                <% end_if %>
                            </p>
                            <p>
                                <span class="left-info">Presentation</span>
                                <% if $Presentation.Exists %>
                                <a href="" class="download">DOWNLOAD</a>
                                <% else %>
                                <span class="soon">COMING SOON</span>
                                <% end_if %>
                            </p>
                        </div>
                    </div>
                    <% end_loop %>
                </div>
            </div>
        </div>
        <div id="graphics" class="tab-pane fade in tab-page" role="tabpanel">
            <div class="container">
                <div class="tab-header">$GraphicsIntroText</div>
                <h3 class="blue-title">Sticker Files</h3>
                <ul class="content-list">
                    <% loop $Stickers() %>
                    <li>
                        $getTag();
                        <p>$Name</p>
                        <a class="download" href="">Download</a>
                    </li>
                    <% end_loop %>
                </ul>
                <span class="hr"></span>

                <h3 class="blue-title">T-Shirt Files</h3>
                <ul class="content-list">
                    <% loop $TShirts() %>
                    <li>
                        $getTag()
                        <p>$Name</p>
                        <a class="download" href="">Download</a>
                    </li>
                    <% end_loop %>
                </ul>
                <span class="hr"></span>

                <h3 class="blue-title">Banner & Poster Files</h3>
                <ul class="content-list">
                    <% loop $Banners() %>
                    <li>
                        $getTag()
                        <p>$Name</p>
                        <a class="download" href="">Download</a>
                    </li>
                    <% end_loop %>
                </ul>
                <span class="hr"></span>

                <h3 class="blue-title">Videos</h3>
                <ul class="content-list">
                    <% loop $Videos() %>
                    <li>
                        $Thumbnail.getTag()
                        <p>$Caption</p>
                        <a class="download" href="">Watch</a>
                    </li>
                    <% end_loop %>
                </ul>
            </div>
        </div>
        <div id="promote_product" class="tab-pane fade in active tab-page" role="tabpanel">
            <div class="container">
                <div class="tab-header">$PromoteProductIntroText</div>
                <div class="row ">
                    <% loop $PromoteEvents() %>
                    <div class="col-md-4 pd-8">
                        $Image.getTag()
                        <div class="br-block">
                            <h3 class="blue-title">$Title</h3>
                            <span class="small-descr">$Description</span>
                            <a href="$ButtonLink" class="red-button">$ButtonLabel</a>
                        </div>
                    </div>
                    <% end_loop %>
                </div>
            </div>
        </div>

    </div>