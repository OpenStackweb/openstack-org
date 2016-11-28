<div class="top-bar">
    <p>
        <i class="fa fa-exclamation-circle"></i>
        We had an amazing time in {$Summit.Title}, you don't want to miss us in {$Top.NextSummit.Name}!
        <a href="<% if $Summit.Next.RegistrationLink %>$Summit.Next.RegistrationLink<% else %>#<% end_if %>">More on the Summit in {$Top.NextSummit.Name}.</a>
    </p>
</div>
<div id="wrap">
    <div class="summit-hero-wrapper condensed" <% if SummitImage %>style="background: rgba(0, 0, 0, 0) url('{$SummitImage.Image.Link}') no-repeat scroll center bottom / cover ;"<% end_if %>>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <a href="/">
                        <img alt="OpenStack Summit"
                             src="/summit/images/summit-logo.svg" onerror="this.onerror=null; this.src=/summit/images/summit-logo.png"
                             class="summit-hero-logo">
                    </a>
                    <h2>
                        $Summit.DateLabel
                    </h2>

                    <h1>
                        $Summit.Title
                    </h1>
                </div>
            </div>
            <a class="open-panel" href="#"><i class="fa fa-bars fa-2x collapse-nav"></i></a>
        </div>
        <div class="hero-tab-wrapper">
            <!-- Microsite Navigation -->
            <ul class="nav nav-tabs">
                <li class="current">
                    <a href="$Top.Link">Summit Highlights</a>
                </li>
                <li class="">
                    <a href="$Top.Link(videos)">Videos</a>
                </li>
                <li class="">
                    <a href="$Top.Link(sponsors)">Sponsors</a>
                </li>
            </ul>
            <!-- End Microsite Navigation -->
        </div>
        <% if SummitImage %>
        <a target="_blank" title="" data-placement="left" data-toggle="tooltip" class="photo-credit"
           href="$SummitImage.OriginalURL" data-original-title="{$SummitImage.Attribution}"><i
                class="fa fa-info-circle"></i></a>
        <% end_if %>
    </div>
    <!-- Begin Page Content -->
    <div class="white summit-highlights-intro">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Thank You {$Summit.Title}!</h1>
                    <p>
                        {$ThankYouText}
                    </p>
                </div>
                <div class="col-sm-6">
                    <div class="future-summit-promo tokyo" <% if NextSummitTinyBackgroundImage %>style="background: rgba(0, 0, 0, 0) url('{$NextSummitTinyBackgroundImage.Link}') no-repeat center center;"<% end_if %>>
                        <div class="future-summit-next">
                            Up Next
                        </div>
                        <div class="future-summit-promo-city">
                            $Summit.Next.Title
                        </div>
                        <p>
                           {$NextSummitText}
                        </p>
                        <p>
                            <a class="future-summit-btn" href="#">Find Out More</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="huge-success-video">
        <h1>It Was A <strong>HUGE</strong> Success</h1>
        <div class="summit-success-stats">
            <div class="stat">
                <div class="number attendance">
                    {$Top.AttendanceQty}
                </div>
                <div class="title">
                    In Attendance
                </div>
            </div>
            <div class="stat">
                <div class="number companies">
                    {$Top.CompaniesRepresentedQty}
                </div>
                <div class="title">
                    Companies Represented
                </div>
            </div>
            <div class="stat">
                <div class="number countries">
                    {$Top.CountriesRepresentedQty}
                </div>
                <div class="title">
                    Countries Represented
                </div>
            </div>
        </div>
        <video id="bgvid" poster="{$Top.StatisticsVideoPoster.Link}" loop="" autoplay="">
            <% if StatisticsVideoUrl %>
            <source type="video/mp4" src="{$Top.StatisticsVideoUrl}"></source>
            <% end_if %>
        </video>
    </div>
    <% if KeynotesImages %>
        <script>
            var keynotes = [];
            <% loop SummitKeynoteHighlightAvailableDays %>
                var {$Label} = { day:'{$Label}', items:[] };
                keynotes.push({$Label});
            <% end_loop %>
            <% loop KeynotesImages.Sort(Order, ASC) %>
                {$Day}.items.push(
                        {
                            title       : '{$Title}',
                            description : '{$Description.RAW}',
                            image_url   : '{$Image.Link}',
                            preview_url : '{$ThumbnailLink}',
                        }
                );
            <% end_loop %>
        </script>
        <highlights keynotes="{ keynotes }"></highlights>
    <% end_if %>
    <%if ReleaseAnnouncedTitle %>
    <div class="summit-highlight-release">
        <div class="container">
            <div class="row">
                <h1>{$ReleaseAnnouncedTitle}</h1>

                <div class="col-sm-5 center">
                    <img alt=""
                         src="{$ReleaseAnnouncedImage.Link}">
                </div>
                <div class="col-sm-7">
                    <p>
                        {$ReleaseAnnouncedDescription}
                    </p>
                    <p>
                        <a class="highlight-software-btn" href="{$ReleaseAnnouncedButtonLink}">{$ReleaseAnnouncedButtonTitle}&nbsp;<i class="fa fa-chevron-right"></i></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <% end_if %>
    <% if Pics %>
        <div class="summit-highlights-pics">
            <div class="container">
                <div class="row">
                    <h1>Oh, The Fun We Had!</h1>
                    <% loop Pics.sort(Order) %>
                        <div class="pic-container">
                            <div data-toggle="lightbox" class="thumbnails">
                                <a class="thumbnail" href="{$Image.Link}" title="{$Title}">
                                    <img alt="$Title"
                                         src="{$Image.Link}"
                                         class="img-responsive">
                                </a>
                            </div>
                        </div>
                    <% end_loop %>
                </div>
                <div class="row">
                    <div class="col-sm-12 center">
                        <a class="highlights-pic-link" href="<% if CurrentSummitFlickrUrl %>$CurrentSummitFlickrUrl<% else %>#<% end_if %>">See all of the pics from The Summit in {$Summit.Title} on Flickr</a>
                    </div>
                </div>
            </div>
        </div>
    <% end_if %>
    <div class="about-city-row" <% if NextSummitBackgroundImage %>style="background: rgba(0, 0, 0, 0) url('{$NextSummitBackgroundImage.Image.Link}') no-repeat scroll left top / cover;"<% end_if %> >
        <p>
            The next Summit will be here before you know it...
        </p>
        <h1>See You In {$Summit.Next.Title}</h1>
        <div class="summit-date">
            {$Summit.Next.Text}
        </div>
        <% if $Summit.Next.RegistrationLink %>
        <div>
            <br/><a class="btn register-btn-lrg" href="$Summit.Next.RegistrationLink">Join Us</a>
        </div>
        <% end_if %>
        <% if NextSummitBackgroundImage %>
        <a target="_blank" title="" data-placement="left" data-toggle="tooltip" class="photo-credit"
           href="{$NextSummitBackgroundImage.OriginalURL}" data-original-title="{$NextSummitBackgroundImage.Attribution}"><i class="fa fa-info-circle"></i></a>
        <% end_if %>
    </div>
    <!-- End Page Content -->
    <div id="push"></div>
</div>
<script src="summit/javascript/summit-highlights.bundle.js"></script>
