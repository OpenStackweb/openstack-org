<div class="product-box">
    <div class="span-8">
        <div class="logo-area">
            <span style="background-color: #{$Company.CompanyColor}" class="color-bar"></span>
            <a href="$ConsultantLink{$Company.URLSegment}/{$Slug}">
                $Company.SmallLogoPreview(150)
            </a>
        </div>
    </div>
    <div class="span-9 last">
        <div class="company-details-area">
            <h1>
                <a style="color: #{$Company.CompanyColor}" href="$ConsultantLink{$Company.URLSegment}/{$Slug}">
                    $Name
                </a>
            </h1>
            <p>$Overview</p>
            <a style="background-color: #{$Company.CompanyColor}" href="$ConsultantLink{$Company.URLSegment}/{$Slug}" class="details-button">Details</a>
        </div>
    </div>
</div>