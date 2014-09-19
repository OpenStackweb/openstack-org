jQuery(document).ready(function($){

    $('#name-term').autocomplete({
        source: 'implementations/names',
        minLength: 2,
        select: function (event, ui) {
            $('.filter-label').trigger("click");
        }
    })
    .keydown(function (e) {
            if (e.keyCode === 13) {
                $('.filter-label').trigger("click");
            }
    });

    $('#service-term').prepend("<option value='' selected='selected'>-- Select a Service--</option>");
    $('#service-term').chosen({disable_search_threshold: 3});
    $('#service-term').change(function () {
        $('.filter-label').trigger("click");
    });

    var last_filter_request = null;

    $('.filter-label').live('click', function (event) {
        var params = {
            name_term     : $('#name-term').val(),
            service_term  : $('#service-term').val()
        }
        if(last_filter_request!=null)
            last_filter_request.abort();

        last_filter_request = $.ajax(
            {
                type:        "POST",
                url:         'implementations/search',
                contentType: "application/json; charset=utf-8",
                dataType:    "html",
                data:        JSON.stringify(params),
                success: function (data,textStatus,jqXHR) {
                    $('#implementation-list').html(data);
                    last_filter_request = null;
                },
                error: function (jqXHR,  textStatus,  errorThrown) {
                    $('#implementation-list').html('<div>There are no Distros/Appliances matching your criteria.</div>');
                    last_filter_request = null;
                }
            }
        );
    });

});
