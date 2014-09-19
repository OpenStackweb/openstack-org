jQuery(document).ready(function($){
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
});
