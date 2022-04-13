<div class="summit-hero-landing-short container-fluid about-summit-header">


    <nav class="navbar navbar-default" id="summit-main-nav">
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
                        <a href="/summit" class="drop">Other Summits<i class="fa fa-caret-down"></i></a><i class="mobile-expand"></i>
                        <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuEvents">
                            <li><a href="https://openinfra.dev/summit">OpenInfra Summit Berlin</a></li>
                            <li><a href="https://openinfra.dev/live/keynotes">OpenInfra Live: Keynotes</a></li>
                            <li><a href="/summit/2020/">Virtual 2020</a></li>
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

    <div class="container">

        <div class="row text-wrapper">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h4 class="summit-title script">The OpenStack Summit’s new name:</h4>
                <h2>The Open Infrastructure Summit</h2>
                <h5>Collaborate directly with the people building and running open source infrastructure using <strong>OpenStack, Kubernetes and 30+ other technologies</strong>.</h5>
            </div>
        </div></div>
</div>
