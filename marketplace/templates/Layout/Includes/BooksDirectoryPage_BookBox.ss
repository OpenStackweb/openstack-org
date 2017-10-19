<div class="product-box row" style="border-left-color: #{$Company.CompanyColor}">
    <div class="col-lg-3 col-md-5 col-sm-6">
        <div class="logo-area">
            <span style="background-color: #{$Company.CompanyColor}" class="color-bar"></span>
            <% if $Image.Exists %>
                <img src='{$Image.getURL()}' class='small-logo-company company-logo'/>
            <% else %>
                <img alt='{$Company.Name}_small_logo' src='{$Company.Logo().getURL()}' class='small-logo-company company-logo'/>
            <% end_if %>
        </div>
    </div>
    <div class="col-lg-8 col-md-7 col-sm-6">
        <div class="company-details-area">
            <h4>
                $Title
            </h4>
            <p class="company-name">by <a href="{$Company.URL}">$Company.Name</a></p>
            <p>$Description</p>
            <a style="background-color: #{$Company.CompanyColor}; padding-left:25px;" href="{$Link}" class="details-button">Get</a>
        </div>
    </div>
</div>