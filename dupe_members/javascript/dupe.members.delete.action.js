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
    //keep account
    $('#btn-keep').click(function(e){
        e.preventDefault();
        var btn = $(this);

        swal({
            title: 'Are you sure?',
            text: 'You will keep this member account!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, keep it!',
            cancelButtonText: 'No',
        }).then(function() {
            var token = btn.attr('data-token');
            $.ajax({
                async:true,
                type: 'PUT',
                url: 'api/v1/dupes-members/'+token+'/account',
                dataType: "json",
                success: function (data,textStatus,jqXHR) {
                    swal(
                        'About your request',
                        'You decided to keep  your duplicate account. Thank you.',
                        'success'
                    );
                    window.location = '/';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
        });

        return false;
    });

    //merge account
    $('#btn-merge').click(function(e){
        e.preventDefault();
        var btn = $(this);

        swal({
            title: 'Are you sure?',
            text: 'You will merge this member account!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, merge it!',
            cancelButtonText: 'No, keep it',
        }).then(function() {
            var token = btn.attr('data-token');
            $.ajax({
                async:true,
                type: 'PATCH',
                url: 'api/v1/dupes-members/'+token+'/account',
                dataType: "json",
                success: function (data,textStatus,jqXHR) {
                    swal(
                        'About your request',
                        'An email will be sent to your duplicate account email with the merge instructions, please follow them. Thank you.',
                        'success'
                    );
                    window.location = '/';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
        });

        return false;
    });

    //delete account
    $('#btn-delete').click(function(e){
        e.preventDefault();
        var btn = $(this);
        swal({
            title: 'Are you sure?',
            text: 'This will delete your duplicate account and its not a reversible action. Are you sure?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it',
        }).then(function() {
            var token = btn.attr('data-token');
            $.ajax({
                async:true,
                type: 'DELETE',
                url: 'api/v1/dupes-members/'+token+'/account',
                dataType: "json",
                success: function (data,textStatus,jqXHR) {
                    alert();
                    swal(
                        'About your request',
                        'You have been deleted your duplicate account successfully. Thank you.',
                        'success'
                    );
                    window.location = '/';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
        });
        return false;
    });
});