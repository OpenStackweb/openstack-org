</div>
<div class="intro-header featured" style="background-image: url({$HeroImageUrl})">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-sm-12">
                <div class="intro-message">
                    <h1>$PromoIntroMessage</h1>
                </div>
                <div class="promo-btn-wrapper">
                    <a href="{$PromoButtonUrl}" class="promo-btn">$PromoButtonText<i class="fa fa-chevron-right"></i></a>
                </div>
                <% if PromoDatesText %>
                <p class="promo-dates">$PromoDatesText</p>
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
