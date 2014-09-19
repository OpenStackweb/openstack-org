jQuery(document).ready(function($){
    var second_column = $('div.events-second-column');
    if(second_column.length>0){
        var upcoming_events_container = $('div.upcoming');
        if(upcoming_events_container.length>0){
            upcoming_events_container.css('max-height', ( second_column.height() - 48 ) +'px');
        }
    }
});
