<div class="summit-hero-landing" style="background-image: url('{$SummitImage.Image().getURL()}')">

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
                <ul class="nav navbar-nav">
                    <li class="{$getAboutPageNavClass}">
                        <a href="{$getSummitAboutPageLink}">About</a>
                    </li>
                    <% loop $Menu(3) %>
                    <li class="{$LinkingMode}">
                        <a href="{$Link}">$MenuTitle</a>
                    </li>
                    <% end_loop %>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row text-wrapper">
        <div class="col-md-12  title-box">
            <div class="summit-banner">
                $Top.getSummitPageText("HeaderText")
                <!-- <span class="arrow left"></span><span class="arrow right"></span> -->
            </div>
        </div>
    </div>

    <a href="{$SummitImage.OriginalURL}" target="_blank" class="photo-credit" title="{$SummitImage.Attribution}">
        <i class="fa fa-info-circle"></i>
    </a>

    <div class="row text-wrapper blue">
        <div class="highlight">
            <span class="megaphone"></span>
            <h5>
                <strong>Register for $999 USD</strong> before prices increase on October 24 at 11:59pm PT (October 25 at 6:59am UTC)
            </h5>
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