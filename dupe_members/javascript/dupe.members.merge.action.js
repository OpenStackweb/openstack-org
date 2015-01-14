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
jQuery(document).ready(function ($) {

    var fields = ['gerrit_id', 'first_name', 'surname', 'email', 'second_email', 'third_email', 'shirt_size',
        'statement_interest', 'bio', 'food_preference', 'other_food', 'irc_handle', 'twitter_name',
        'linkedin_profile', 'projects', 'other_project', 'address', 'suburb', 'state', 'postcode',
        'country', 'city', 'gender', 'photo'];

    for (var i = 0; i < fields.length; i++) {
        var field = fields[i];
        $("input:radio." + field).click(function (e) {
            var radio = $(this);
            var field = radio.attr('name');
            var member_id = radio.attr('data-member-id');
            var span = $('#' + field + '_' + member_id);
            $('.' + field + '_div').addClass('hidden');
            span.removeClass('hidden');
        });
    }


    $('.merge').click(function (e) {
        e.preventDefault();
        var btn = $(this);
        if (window.confirm('Are you sure?')) {

            var merge_result = {};

            var confirmation_token = $('#merge_table').attr('data-confirmation-token');

            for (var i = 0; i < fields.length; i++) {
                var field = fields[i];
                var tr = $('.'+field + '_row', '#merge_table');
                var radio = $('input:radio:checked', tr);
                var val = null;
                if (radio.length > 0) {
                    val = radio.val();
                }
                merge_result[field] = val;
            }

            $.ajax({
                async: true,
                type: 'POST',
                url: 'api/v1/dupes-members/'+confirmation_token+'/merge',
                dataType: "json",
                data: JSON.stringify(merge_result),
                contentType: "application/json; charset=utf-8",
                success: function (data, textStatus, jqXHR) {
                    alert('You have been merged your duplicate account successfully. Thank you.');
                    window.location = '/';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError(jqXHR, textStatus, errorThrown);
                }
            });
        }
        return false;
    });
});

