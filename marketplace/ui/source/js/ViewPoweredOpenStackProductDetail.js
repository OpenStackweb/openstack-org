/**
 * Copyright 2017 OpenStack Foundation
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

    $('#expiry_date').datetimepicker({
        format: 'Y-m-d H:i:00',
        step: 1,
        formatDate: 'Y-m-d',
        formatTime: 'H:i:00',
        defaultTime: '23:59:00',
    });

    $('#form-product-main-info').submit(function(e){
        e.preventDefault();
        var payload = {
            required_for_compute : $('#required_for_compute').is(':checked'),
            required_for_storage : $('#required_for_storage').is(':checked'),
            expiry_date          : $('#expiry_date').val(),
            program_version_id   : $('#program_version_id').val(),
            reported_release_id  : $('#reported_release_id').val(),
            passed_release_id    : $('#passed_release_id').val(),
            notes                : $('#notes').val(),
        };

        var service_id = $('#service_id').val();

        $.ajax({
            async:true,
            type: 'PUT',
            url: 'api/v1/sangria/marketplace/openstack-powered-implementations/'+service_id,
            data: JSON.stringify(payload),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                swal(
                    'Success!',
                    'Info was updated!',
                    'success'
                )
                window.location = window.location;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError( jqXHR, textStatus, errorThrown);
            }
        });

        return false;
    });

    $('#add-refstack-form').validate({
            rules: {
                new_refstack_link: {
                    required: true,
                    complete_url: true
                }
            }
        }
    );

    $('#add-zendesk-form').validate({
            rules: {
                new_zendesk_link: {
                    required: true,
                    complete_url: true
                }
            }
        }
    );

    $('.add-service-link').click(function (e) {

        var type       = $(this).data('type-link');
        var service_id = $(this).data('service-id');
        var is_valid   = $('#add-'+type+'-form').valid();

        if(!is_valid) return;

        var link       = $('#new_'+type+'_link').val();

        console.log("link "+ link);
        var payload  = {'link' : link};
        $.ajax({
            async:true,
            type: 'POST',
            url: 'api/v1/sangria/marketplace/openstack-powered-implementations/'+service_id+'/'+type,
            data: JSON.stringify(payload),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                window.location = window.location;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError( jqXHR, textStatus, errorThrown);
            }
        });

        return false;
    });

    $('.delete-service-link').click(function () {
        var type       = $(this).data('type-link');
        var service_id = $(this).data('service-id');
        var link_id    = $(this).data('id');

        $.ajax({
            async:true,
            type: 'DELETE',
            url: 'api/v1/sangria/marketplace/openstack-powered-implementations/'+service_id+'/'+type+'/'+link_id,
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                window.location = window.location;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError( jqXHR, textStatus, errorThrown);
            }
        });

        return false
    });
});