</div>
<!-- <div class="intro-header featured" style="background-image: url({$HeroImageUrl})"> Removed for OpenDev promo-->
    <div class="intro-header featured" style="background: #000;overflow-y:hidden;">
        <img src="/themes/openstack/images/opendev-earth.png" alt="" class="opendev-earth">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-3 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12" style="text-align:left;">
                <% if $PromoIntroMessage %>
                <div class="intro-message">
                    <h1 style="text-align:left;">$PromoIntroMessage</h1>
                </div>
                <% end_if %>
                <% if PromoDatesText %>
                <p class="promo-dates">$PromoDatesText</p>
                <% end_if %>
                <% if $PromoButtonUrl %>
                <div class="promo-btn-wrapper">
                    <a href="{$PromoButtonUrl}" class="promo-btn" style="background:#43B7D9;">$PromoButtonText<i class="fa fa-chevron-right"></i></a>
                    <div style="font-style:italic;margin-top:5px;">
                        September 7-8 | San Francisco, CA
                    </div>
                </div>
                <% end_if %>
            </div>
            <% if PromoHeroCredit && PromoHeroCreditUrl %>
            <div class="hero-credit" data-toggle="tooltip" data-placement="left" title="{$PromoHeroCredit}">
                <a href="{$PromoHeroCreditUrl}" target="_blank"><i class="fa fa-info-circle"></i></a>
            </div>
            <% else_if PromoHeroCredit %>
            <div class="hero-credit" data-toggle="tooltip" data-placement="left" title="{$PromoHeroCredit}"><i class="fa fa-info-circle"></i></div>
            <% end_if %>
        </div>
    </div>
</div>
<!-- /.intro-header -->

<% include HomePageBottom %>

