jQuery(document).ready(function($){
    //init map widget

    var places = [];

    if(typeof(dc_locations)!=='undefined' && dc_locations.length > 0){
        places = dc_locations;
    }

    $('#mini-map').google_map({
        places : places,
        minZoom: 1,
        minClusterZoom:2,
        gridSize: 20,
        minimumClusterSize:2,
        getInfo:function(place){
            return '<a href="'+place.product_url+'"><b>'+place.owner+'</b><br>'+
                place.product_name+'<br>'+
                place.city+', '+place.country+' DataCenter</a>'
        }
    });
});

