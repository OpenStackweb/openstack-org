/**
 * Copyright 2015 Openstack Foundation
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

    $(".sendgrid-subscription-widget").on("success", function () {
        var form = $('form','.sendgrid-subscription-widget');
        var info = { first_name : $('#a',form).val(), last_name: $('#b',form).val(), email: $('input[type=email]', form).val()};
        var security_id = $('#SecurityID', form);
        $.ajax({
            type: "POST",
            url: 'NewsPage_Controller/sendSignupConfirmation?SecurityID='+security_id,
            data: JSON.stringify(info),
            success: function(){

            }
        });
    });
});