jQuery(document).ready(function($){
    var second_column = $('div.events-second-column');
    if(second_column.length>0){
        var upcoming_events_container = $('div.upcoming');
        if(upcoming_events_container.length>0){
            upcoming_events_container.css('max-height', ( second_column.height() - 48 ) +'px');
        }
    }

    setInterval(refresh_page,120000);

});

function refresh_page() {
    refresh_future_events();
    refresh_future_summits();
    refresh_past_summits();
}

function refresh_future_events() {
    jQuery.ajax({
        type: "POST",
        url: 'EventHolder_Controller/AjaxFutureEvents',
        success: function(result){
            var html  = result + '<div class="clear"></div>';
            jQuery('.future_events').html(html);
        }
    });
}

function refresh_future_summits() {
    jQuery.ajax({
        type: "POST",
        url: 'EventHolder_Controller/AjaxFutureSummits',
        success: function(result){
            var html  = result + '<div class="clear"></div>';
            jQuery('.future_summits').html(html);
        }
    });
}

function refresh_past_summits() {
    jQuery.ajax({
        type: "POST",
        url: 'EventHolder_Controller/AjaxPastSummits',
        success: function(result){
            var html  = result + '<div class="clear"></div>';
            jQuery('.past_summits').html(html);
        }
    });
}