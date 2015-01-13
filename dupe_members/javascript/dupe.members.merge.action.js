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
jQuery(document).ready(function($) {

    var fields = ['gerrit_id', 'first_name', 'surname', 'email', 'second_email', 'third_email', 'shirt_size',
                  'statement_interest', 'bio', 'food_preference', 'other_food', 'irc_handle', 'twitter_name',
                  'linkedin_profile', 'projects', 'other_project', 'address', 'suburb', 'state', 'postcode',
                  'country', 'city', 'gender', 'photo'];

    for(var i = 0; i < fields.length ; i++) {
        var field = fields[i];
        $('.'+field).click(function (e) {
            var radio     = $(this);
            var field     = radio.attr('name');
            var member_id = radio.attr('data-member-id');
            var span      = $('#'+field+'_' + member_id);
            $('.'+field+'_div').addClass('hidden');
            span.removeClass('hidden');
        });
    }
});

