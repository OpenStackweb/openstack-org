<div class="summit-hero-wrapper small-header <% if HeroCSSClass %>$HeroCSSClass<% end_if %>" <% if $SummitImage %>style="background: url('{$SummitImage.Image.link}') -360px -140px ;"<% end_if %> >
    <div class="container">
        <div class="row text-wrapper">
            <% with $Summit %>
            <div class="col-md-2 col-sm-3 logo-box">
                <a href="/summit">
                    <img class="summit-hero-logo" src="/themes/openstack/static/images/summit-logo-small-white.svg" alt="OpenStack Summit">
                </a>
            </div>
            <div class="col-md-6 col-sm-5 title-box">
                <div class="summit-title">{$Title}</div>
                <div class="summit-date">{$DateLabel}</div>
            </div>
            <div class="col-md-3 col-sm-3 button-box">
                <% if IsUpComing %>
                    <div class="button-wrapper">
                        <a href="{$RegistrationLink}" class="btn register-btn-lrg">Register Now</a>
                    </div>
                <% else_if IsCurrent %>
                    <div class="inner-countdown-wrapper">
                        <div class="countdown">
                            <span>N</span>
                            <span>O</span>
                            <span>W</span>
                        </div>
                        <div class="countdown-text">
                            The Summit is Happening Now!
                        </div>
                    </div>
                <% end_if %>
            </div>
            <% end_with %>
        </div>
    </div>
    <div class="hero-tab-wrapper">
        <!-- Microsite Navigation -->
        <% include SummitNav %>
        <!-- End Microsite Navigation -->
    </div>
    <% if $SummitImage && $SummitImage.Attribution %>
        <a href="#" class="photo-credit" title="{$SummitImage.Attribution}">
            <i class="fa fa-info-circle"></i>
        </a>
    <% end_if %>
</div>
