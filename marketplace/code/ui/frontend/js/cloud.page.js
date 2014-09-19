jQuery(document).ready(function($){
    //init map widget
    if(typeof(dc_locations_per_cloud_instance)!=='undefined' && dc_locations_per_cloud_instance.length > 0){
        $('#mini-map').google_map({
            places :dc_locations_per_cloud_instance,
            minZoom: 1,
            minClusterZoom:2,
            getInfo:function(location){
                var data_center_info = '<strong>'+location.city+', '+location.country+' Data Center</strong><br>';
                data_center_info+='<b>'+location.zone+'</b><br>';
                if(location.endpoint!=null)
                    data_center_info+= location.endpoint+'<br>';
                if(location.availability_zones.length>0){
                    data_center_info+='<ul>';
                    for(var j in location.availability_zones){
                        var az = location.availability_zones[j];
                        data_center_info+='<li>';
                        data_center_info+=az.name;
                        data_center_info+='</li>';
                    }
                    data_center_info+='</ul>';
                }
                return data_center_info;
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

    $('.api-coverage').capabilities_meter({coverages:coverages});

    for(var i in enabled_schemas){
        var td = $('#enabled_'+enabled_schemas[i]);
        td.addClass('check');
        td.append($('<span>X</span>'));
    }
});