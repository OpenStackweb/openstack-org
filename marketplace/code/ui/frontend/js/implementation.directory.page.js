/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
jQuery(document).ready(function($){

    $('#name-term').autocomplete({
        source: 'implementations/names',
        minLength: 2,
        select: function (event, ui) {
            if(ui.item) $('#name-term').val(ui.item.value);
            $('.filter-label').trigger("click");
        }
    })
    .keydown(function (e) {
            if (e.keyCode === 13) {
                $('.filter-label').trigger("click");
            }
    });

    var selected = ($('#service-term').val()) ? '' : 'selected';
    $('#service-term').prepend("<option value='' "+selected+">-- Select a Service--</option>");
    $('#service-term').chosen({disable_search_threshold: 3, width:'auto'});
    $('#service-term').change(function () {
        $('.filter-label').trigger("click");
    });

    var last_filter_request = null;

    $(document).on('click', '.filter-label', function (event) {
        var params = {
            name_term     : $('#name-term').val(),
            service_term  : $('#service-term').val()
        }
        if(last_filter_request!=null)
            last_filter_request.abort();

        var name_term = (params.name_term == '') ? 'a' : params.name_term;
        var service = (params.service_term == '') ? 'a' : params.service_term;
        var state = '/marketplace/distros';
        if (params.name_term){
            state += '/f/'+service+'/'+name_term;
        } else if (params.service_term){
            state += '/f/'+service;
        }
        history.pushState(null, null, state);

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
                    if(errorThrown === 'abort') return;
                    $('#implementation-list').html('<div>There are no Distros/Appliances matching your criteria.</div>');
                    last_filter_request = null;
                }
            }
        );
    });

    //if preselected filters run ajax
    if ($('#name-term').val() || $('#service-term').val()) {
        $('.filter-label').trigger("click");
    }

});
