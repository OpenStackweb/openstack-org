<div class="summit-hero-landing-short container-fluid" style="background-image: url('https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/OD-Header-2160x640.png')">

    <nav class="navbar navbar-default navbar-fixed-top" id="summit-main-nav">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"
                        aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand summit-hero-logo" href="/summit"></a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-main show">
                    <% loop $getMenuItems() %>
                        <li class="link">
                            <a href="{$Link}">$MenuTitle</a>
                        </li>
                    <% end_loop %>
                    <li class="link other-summits">
                        <a href="/events/opendev-2020/opendev-schedule" class="drop">OpenDev Schedules<i class="fa fa-caret-down"></i></a><i class="mobile-expand"></i>
                        <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuEvents">
                            <li><a href="/events/opendev-2020/opendev-schedule">Virtual Event 1</a></li>
                        </ul>
                    </li>
                    <li class="link">
                        <a href="/code-of-conduct/">Code fo Conduct</a>
                    </li>
                    <li class="link">
                        <a href="/ptg">June 2020 PTG</a>
                    </li>
                    <li class="link other-summits">
                        <a href="/events" class="drop">Other OSF Events<i class="fa fa-caret-down"></i></a><i class="mobile-expand"></i>
                        <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuEvents">
                            <li><a href="/summit/shanghai-2019/">Shanghai 2019</a></li>
                            <li><a href="/summit/denver-2019/">Denver 2019</a></li>
                            <li><a href="/summit/berlin-2018/">Berlin 2018</a></li>
                            <li><a href="/summit/vancouver-2018/">Vancouver 2018</a></li>
                        </ul>
                    </li>
                    <li class="link button-box">
                        <% include EventbriteRegisterLink Summit=$CurrentSummit(),Position='h' %>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="row text-wrapper">
        <div class="col-md-12  title-box">
            <div class="summit-banner">
                <p><img title="OpenDev 2020" alt="" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/opendev-logo-red-white1.svg" class="opendevlogo"></p>
                <h4 class="summit-title script">Three part virtual event series:</h4>
                <h2>Discuss challenges, collaborate, create open source.</h2>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        var scrollTop = 0;
        $(window).scroll(function () {
            scrollTop = $(window).scrollTop();
            $('.counter').html(scrollTop);

            if (scrollTop >= 50) {
                $('#summit-main-nav').addClass('scrolled-nav');
            } else if (scrollTop < 100) {
                $('#summit-main-nav').removeClass('scrolled-nav');
            }

        });

    });
</script>