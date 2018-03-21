
<% with $Banner %>
<div class="summit-banner editorial" style="background-color: #{$BackgroundColor}">
    <div class="container">
        <div class="row">
            <div class="col-md-3 picture-box">
                <img class="picture" src="{$Picture.getUrl()}" />
            </div>
            <div class="col-md-6 main-text-box" style="color:#{$MainTextColor}">
                {$MainText}
            </div>
            <div class="col-md-3">
                <div class="logo-box">
                    <img class="logo" src="{$Logo.getUrl()}" />
                </div>
                <a class="btn btn-default" href="{$ButtonLink}?b={$Name}" style="background-color: #{$ButtonColor}; color: #{$ButtonTextColor}">
                    {$ButtonText} <i class="fa fa-chevron-right"></i>
                </a>
                <div class="small-text-box" style="color:#{$SmallTextColor}">
                    {$SmallText}
                </div>
            </div>
        </div>
    </div>
</div>
<% end_with %>
   
