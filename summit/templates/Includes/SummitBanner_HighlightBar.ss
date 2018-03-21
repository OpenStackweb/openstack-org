
<% with $Banner %>
<div class="summit-banner highlight" style="background-color: #{$BackgroundColor}">
    <div class="container">
        <div class="row">
            <div class="col-md-2 logo-box">
                <img class="logo" src="{$Logo().getUrl()}" />
            </div>
            <div class="line"></div>
            <div class="col-md-6 main-text-box" style="color:#{$MainTextColor}">
                {$MainText}
            </div>
            <div class="col-md-3 button-box">
                <a class="btn btn-default" href="{$ButtonLink}?b={$Name}" style="background-color: #{$ButtonColor}; color: #{$ButtonTextColor}">
                    {$ButtonText} <i class="fa fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<% end_with %>
   
