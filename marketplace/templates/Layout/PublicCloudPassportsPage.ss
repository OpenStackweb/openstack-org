</div> <!-- Killing the main site .container -->

<div class="container-fluid hero">
    <div class="col-xs-12 col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
        <div class="col-xs-4">
            <img src="/marketplace/code/ui/frontend/images/logo.svg" alt="">
        </div>
        <div class="col-xs-8 text">
            <h3>The OpenStack Public Cloud Passport</h3>
            <h5>Your wings to roam any cloud.</h5>
            <a id="view-providers" class="btn">
                View Public providers <i class="fa fa-arrow-circle-down"></i>
            </a>
        </div>
    </div>
</div>

<div class="container columns">
    <div class="row">
        <div class="col-xs-12 col-md-4">
            <h4><i class="fa fa-search"></i>Explore Public Clouds</h4>
            <p>The OpenStack Global Passport Program is a collaborative effort between OpenStack public cloud providers to let you experience the freedom, performance and interoperability of open source infrastructure. You can quickly and easily gain access to OpenStack infrastructure via trial programs from participating OpenStack public cloud providers around the world.</p>
        </div>
        <div class="col-xs-12 col-md-4">
            <h4><i class="fa fa-map-marker"></i>Locations Everywhere</h4>
            <p>With more than 60 availability zones across XX cities, OpenStack providers collectively represent the broadest public cloud footprint, giving you freedom to deploy your applications across more geographies using the same open source infrastructure.</p>
        </div>
        <div class="col-xs-12 col-md-4">
            <h4><i class="fa fa-cloud"></i>Secure Trial Programs</h4>
            <p>Please note the process and requirements for trial programs may vary. For example, some providers may require a nominal charge to your credit card for fraud prevention purposes, while others may verify you via email and provide vouchers to sign up.</p>
        </div>
    </div>
</div>

<div class="container-fluid light">
    <div class="row light">
        <div class="col-xs-12">
            <h5>Pick your cloud below, check out the developer resources and get started!</h5>
        </div>
    </div>
</div>

<div id="app-container"></div>

<div class="container-fluid resources">
    <div class="row">
        <div class="col-xs-12 col-sm-offset-2 col-sm-8 col-lg-offset-4 col-lg-4 center">
            <img class="icon" src="/marketplace/code/ui/frontend/images/training.svg" alt="">
            <h4>Resources</h4>
            <p>Dig deep into the the world of OpenStack through the eyes of those who operate and develop OpenStack. Ask, find and answer OpenStack specific questions here.</p>
            <button class="btn btn-secondary">Explore</button>
        </div>
    </div>
</div>



<script type="text/javascript">
	window.AppConfig = $JSONConfig;

	$("#view-providers").click(function(e) {
	    e.preventDefault();
        $('html, body').animate({
            scrollTop: $("#app-container").offset().top
        }, 2000);
        return false;
    });
</script>

$ModuleJS('marketplace-passport-page')
$ModuleCSS('marketplace-passport-page')
