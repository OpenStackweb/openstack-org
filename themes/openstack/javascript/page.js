jQuery(document).ready(function($) {

    var d = new Date();
    var user_date = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate();
    $.cookie('user_date',user_date , { expires: 360, path: '/' });

    $('body').filetracking();

    $('.outbound-link').live('click',function(event){
        var href = $(this).attr('href');
        recordOutboundLink(this,'Outbound Links',href);
        event.preventDefault();
        event.stopPropagation()
        return false;
    });
    var use_shadow_box = $UseShadowBox;
    if(use_shadow_box)
        Shadowbox.init();
});