jQuery(document).ready(function($){

    //init map widget
    if(typeof(offices_instance)!=='undefined' && offices_instance.length > 0){
        $('#mini-map').google_map({
            places : offices_instance,
            minZoom: 1,
            minClusterZoom:2,
            getInfo:function(place){
                return '<b>'+place.owner+'</b><br>'+
                    '<b>'+place.name+'</b><br>'+
                    place.address;
            }
        });
    }

    $('.support-regions').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        $('.support-regions').removeClass('selected');
        $('.support-channels').hide();
        $(this).addClass('selected');
        var region_id =  $(this).attr('data-region');
        $('#region_channels_'+region_id).show();
        return false;
    });
});