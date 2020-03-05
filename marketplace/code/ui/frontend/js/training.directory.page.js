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

        var company_filter = $('#company-term');
        if(company_filter.length > 0){
            if ($('#company-term option:contains("-- Select a Company--")').length == 0) {
                company_filter.prepend("<option value='' selected='selected'>-- Select a Company--</option>");
            }
            company_filter.chosen({disable_search_threshold: 3, width:200});
            company_filter.change(function () {
                $('.filter-label').trigger("click");
            });
        }

        var location_filter = $('#location-term');
        if(location_filter.length > 0 ){
            var selected = (location_filter.val()) ? '' : 'selected';
            location_filter.prepend("<option value='' "+selected+" >-- Select a City--</option>");
            location_filter.chosen({disable_search_threshold: 3, width:'auto'});
            location_filter.change(function () {
                $('.filter-label').trigger("click");
            });
        }

        var level_filter = $('#level-term');
        if(level_filter.length > 0){
            var selected = (location_filter.val()) ? '' : 'selected';
            level_filter.prepend("<option value='' "+selected+" >-- Select a Level--</option>");
            level_filter.chosen({disable_search_threshold: 3, width:'auto'});
            level_filter.change(function () {
                $('.filter-label').trigger("click");
            });
        }

        var last_filter_request = null;

        $(document).on('click', '.filter-label', function (event) {
            var params = {
                company_term:    $('#company-term').val(),
                location_term: $('#location-term').val(),
                level_term:    $('#level-term').val()
            }

            if(last_filter_request!=null)
                last_filter_request.abort();

            var company = (params.company_term == '') ? 'a' : params.company_term;
            var location = (params.location_term == '') ? 'a' : params.location_term;
            var level = (params.level_term == '') ? 'a' : params.level_term;
            var state = '/marketplace/training';
            if (params.company){
                state += '/f/'+location+'/'+level+'/'+company;
            } else if (params.level_term){
                state += '/f/'+location+'/'+level;
            } else if (params.location_term){
                state += '/f/'+location;
            }
            history.pushState(null, null, state);

            last_filter_request = $.ajax({
                    type:        "POST",
                    url:         'trainings/search_courses',
                    contentType: "application/json; charset=utf-8",
                    dataType:    "html",
                    data:        JSON.stringify(params),
                    success: function (data,textStatus,jqXHR) {
                        $('#training-list').html(data);
                        last_filter_request = null;
                    },
                    error: function (jqXHR,  textStatus,  errorThrown) {
                        $('#training-list').html('<div>There are no courses matching your criteria.</div>');
                        last_filter_request = null;
                    }
            });
        });
});