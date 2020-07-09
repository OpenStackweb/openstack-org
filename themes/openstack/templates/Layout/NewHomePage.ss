</div>
<!-- Page Content -->
<div class="container">

</div>
<div class="intro-header featured">
<div class="container">
<div class="row">
<div class="col-lg-8 col-sm-12" style="padding-left: 0px; padding-right: 0px">

<div class="intro-message">
<h1>The Most Widely Deployed Open Source Cloud Software in the World</h1>
</div>

<p>
Deployed by thousands. Proven production at scale. OpenStack is a set of software components that provide
common services for cloud infrastructure.
</p>


<div class="promo-btn-wrapper">
<a href="/software" class="promo-btn">browse openstack components</a>
</div>
<p>
OpenStack is developed by the community. For the community. <a class="intro-header-link" href="/community/">Learn how
to contribute</a> <i class="fa fa-arrow-right"></i>
</p>
</div>
<div class="col-lg-3 col-lg-offset-1 col-sm-12" style="padding-left: 0px; padding-right: 0px">
<img src="/themes/openstack/home_images/Hero/OpenStack_SFAs.svg">
</div>

</div>
</div>
</div>
<!-- /.intro-header -->

<!-- Page Content -->

<div class="news-anniversary">

<div class="anniversary-robot">
<img src="/themes/openstack/home_images/Birthday/OS10_Robot_final.svg">
</div>
<div class="anniversary-text">
<h2>10 Years of OpenStack</h2>
<p>
Born from open collaboration, it’s grown to power the most critical infrastructure in the world.
</p>
</div>

<div class="anniversary-btn-wrapper">
<a href="#" class="anniversary-btn">celebrate with us</a>
</div>

</div>

<!-- /.news-anniversary -->

<!-- Page Content -->

<div class="diagram-section">
<div class="container">
<div class="row">
<div class="col-lg-6 col-sm-12 col-xs-12">
<img width="100%" src="/themes/openstack/home_images/Diagram/overview-diagram-new.svg">
</div>
<div class="col-lg-6 col-sm-12">
<h2>$CloudInfraTitle</h2>
<p>
$CloudInfraContent
</p>
<div class="diagram-btn-wrapper">
<a href="{$CloudInfraLink}" class="diagram-btn">read more</a>
</div>
</div>
</div>
<div class="row diagram-icons">
<div class="col-lg-4 col-xs-12 col-md-4 col-sm-4">
<img src="/themes/openstack/home_images/Icons/SVG/On-Premises-Icon.svg">
<h2>On-Premises</h2>
<p>Host your cloud infrastructure internally or find an OpenStack partner in the Marketplace</p>
</div>
<div class="col-lg-4 col-xs-12 col-md-4 col-sm-4">
<img src="/themes/openstack/home_images/Icons/SVG/Public-Cloud-Icon.svg">
<h2>Public Cloud</h2>
<p>Leverage one of the 70+ OpenStack powered public cloud data centers</p>
</div>
<div class="col-lg-4 col-xs-12 col-md-4 col-sm-4">
<img src="/themes/openstack/home_images/Icons/SVG/At-theEdge-Icon.svg">
<h2>At the Edge</h2>
<p>Telecoms and retailers rely on OpenStack for their distributed systems</p>
</div>
</div>
</div>
</div>

<!-- /.diagram-section -->

<!-- Latest Release Block -->

<div class="section-ussuri">
  <div class="container">
    <h2>Latest Release: {$LatestReleaseName}</h2>
    <div class="video-container">
      <img class="ussuri-video" src="{$LatestReleaseVideoPosterUrl}"/>
      <a href="{$LatestReleaseVideoLink}"><i class="fa btn-play fa-play-circle" aria-hidden="true" title="Play it"></i></a>
    </div>
    <p>{$LatestReleaseVideoDescription}</p>
    <div class="ussuri-btn-wrapper">
    <a href="{$LatestReleaseCurrentButtonLink}" class="ussuri-btn">{$LatestReleaseCurrentButtonText}</a>
    <a href="{$LatestReleaseUpNextButtonLink}" class="ussuri-btn">{$LatestReleaseUpNextButtonText}</a>
    </div>
  </div>
</div>

<!-- /.Latest Release Block -->

<!-- SPOTLIGHT-->

<div class="section-spotlight">
<div class="container">
<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <h2>Marketplace Spotlight</h2>
        <p>The OpenStack Marketplace is filled with experts working across industries, use cases, and regions to help
        your organization achieve your goals.</p>
        <% if $RandomCompanyService %>
        <% with $RandomCompanyService %>
        <img src="{$Company.Logo.Link}" alt="company_logo" class="spotlight-marketplace-logo"/>
        <p>{$Overview}</p>
        <div class="spotlight-btn-wrapper">
            <a href="{$Call2ActionUri}" class="spotlight-btn">learn more</a>
        </div>
        <% end_with %>
        <% end_if %>
    </div>
    <div class="col-lg-6 col-sm-12 col-xs-12">
      <h2>OSF Member Spotlight</h2>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec orci enim, scelerisque in nisi sit amet,
      rhoncus consectetur adipiscing. </p>
      <img src="/themes/openstack/home_images/Logos/RH-summit.svg" alt="" width="100%">
      <p>Join OpenStack at the Red Hat Summit, where you can “Immerse yourself in our free virtual event and find
      your inspiration at the intersection of choice and Potential.”</p>
      <div class="spotlight-btn-wrapper">
          <a href="#" class="spotlight-btn">learn more</a>
      </div>
    </div>
</div>
</div>
</div>

<!-- /.SPOTLIGHT -->

<!-- User Stories -->

<div class="twros-section">
<div class="container">
<h2>The World Runs on OpenStack</h2>
<p>OpenStack is trusted to manage 20 Million+ cores around the world, across dozens of industries.</p>
<div class="twros-example">
  <% loop UserStories %>
    <div class="twros-row">
      <% if $HomePageImage %>
      <img src="{$HomePageImage.Link}" alt="" class="twros-img"/>
      <% else %>
      <img src="{$Image.Link}" alt="" class="twros-img"/>
      <% end_if %>
      <div class="twros-text">
      <h2>$Name</h2>
      <p>
      $Description
      </p>
      <div class="twros-btn-wrapper">
      <a href="{$Link}" class="twros-btn"><% if $ButtonText %>$ButtonText<% else %>read more<% end_if %></a>
      </div>
      </div>
    </div>
  <% end_loop %>
</div>

</div>
<p><a href="/user-stories">SEE MORE CASE STUDIES &nbsp; <i class="fa fa-arrow-right"></i></a></p>
</div>
</div>

<!-- /.twros-section -->

<!-- Page Content -->

<div class="osf-section">
<div class="container">
<div class="row">
<div class="col-lg-12 col-sm-12" style="text-align: center;">
<img src="/themes/openstack/home_images/Logos/OSF_Logo_RGB_Horiz_Badge.svg" alt="" />
<p>OpenStack is a top-level open infrastructure project supported by the OSF</p>
</div>
</div>
</div>
</div>

<!-- /.osf-section -->