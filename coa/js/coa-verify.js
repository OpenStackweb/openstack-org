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
    $('#search_exam').click(function() {
       getVerification();
    });

    var hash = window.location.hash;
    if(hash == '#tos'){
        $('#tos').modal('show');
    }
});

function getVerification() {
    var cert_id = $('#cert_id').val();
    var last_name = $('#last_name').val();
    var terms = $('#terms').prop('checked');
    var username = $('#username').val();

    if (username != '') return false;

    if (terms && cert_id && last_name) {
        $.ajax({
            type: "GET",
            url: 'api/v1/coa/'+cert_id+'/'+last_name,
            contentType: "application/json; charset=utf-8",
            success: function (data) {
                $('#cert_empty').hide();
                $('#cert_verification').slideUp('slow');

                if (data.ID) {
                    $('#result_name').text(data.OwnerName);
                    $('#result_cert').text(data.Code);
                    $('#result_date').text(data.PassFailDate);
                    $('#result_status').text(data.CertificationStatus);

                    $('#cert_verification').slideDown('slow');
                } else {
                    $('#cert_empty').show();
                }

            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            var http_code = jqXHR.status;
            if(http_code === 401){
                // user lost its session
                alert('you are not logged in!');
                location.reload();
            }
        });
    } else {
        swal('Validation Error','Please fill in the required information and accept the Terms of Use.','warning');
    }

}
