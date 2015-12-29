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

var last_filter_request = null;

jQuery(document).ready(function($){

    var location_filter = $('#location-term');
    if(location_filter.length > 0 ){
        location_filter.prepend("<option value='' selected='selected'>-- Select a Location--</option>");
        location_filter.chosen({disable_search_threshold: 3, width:400});
        location_filter.change(function () {
            filter_list(true);
        });
    }

    var level_filter = $('#level-term');
    if(level_filter.length > 0){
        level_filter.prepend("<option value='' selected='selected'>-- Select a Level--</option>");
        level_filter.chosen({disable_search_threshold: 3, width:200});
        level_filter.change(function () {
            filter_list(true);
        });
    }

    var level_filter = $('#company-term');
    if(level_filter.length > 0){
        level_filter.prepend("<option value='' selected='selected'>-- Select a Company--</option>");
        level_filter.chosen({disable_search_threshold: 3, width:200});
        level_filter.change(function () {
            filter_list(true);
        });
    }

    var date_picker_start = $('#from_date_filter');
    date_picker_start.datepicker({
        dateFormat: 'yy-mm-dd'
    });
    date_picker_start.change(function () {
        filter_list(true);
    });

    var date_picker_end = $('#to_date_filter');
    date_picker_end.datepicker({
        dateFormat: 'yy-mm-dd'
    });
    date_picker_end.change(function () {
        filter_list(true);
    });

    //pagination
    set_pagination();

    $('.page').live('click',function(e){
        e.stopPropagation();
        change_page(this);
        return false;
    });

});

function filter_list(reset_page) {
    current_page = (reset_page) ? 1 : current_page;
    var params = {
        topic_term:    $('#topic-term').val(),
        location_term: $('#location-term').val(),
        level_term:    $('#level-term').val(),
        company_term:  $('#company-term').val(),
        start_date:    $('#from_date_filter').val(),
        end_date:      $('#to_date_filter').val(),
        page_no:       current_page
    }

    if(last_filter_request != null)
        last_filter_request.abort();

    last_filter_request = $.ajax({
        type:        "POST",
        url:         'trainings/search_classes',
        contentType: "application/json; charset=utf-8",
        dataType:    "json",
        data:        JSON.stringify(params),
        success: function (data,textStatus,jqXHR) {
            $('#training-list').html(data.class_html);
            class_count = data.class_count;
            page_count = Math.ceil(class_count/40);
            last_filter_request = null;
            set_pagination();
        },
        error: function (jqXHR,  textStatus,  errorThrown) {
            $('#training-list').html('<div>There are no classes matching your criteria.</div>');
            last_filter_request = null;
        }
    });
}

function set_pagination() {
    var custom_class = (current_page == 1) ? 'disabled' : '';
    var output = '<li class="'+custom_class+'"><a href="#" class="prev page" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';

    if (page_count == 1) {
        $('.pagination').hide();
    } else {
        $('.pagination').show();

        for (var i=1; i <= page_count; i++) {
            custom_class = (current_page == i) ? 'active' : '';
            output += '<li class="'+custom_class+'"><a href="#" class="page">'+i+'</a></li>';
        }
    }

    custom_class = (current_page == page_count) ? 'disabled' : '';
    output += '<li class="'+custom_class+'"><a href="#" class="next page" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';

    $('.pagination').html(output);
}

function change_page(element) {
    var change_to = 1;

    if($(element).hasClass('prev') && current_page > 1) {
        change_to = current_page - 1;
    } else if ($(element).hasClass('next') && page_count > current_page) {
        change_to = current_page + 1;
    } else {
        change_to = $(element).html();
    }

    current_page = change_to;

    filter_list(false);
}