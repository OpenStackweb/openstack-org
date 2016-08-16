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

    var edit_dialog = $( "#edit_dialog" ).dialog({
        width: '100%',
        height: 900,
        modal: true,
        autoOpen: false,
        resizable: false,
        buttons: {
            "Save": function() {

                var form     = $('form',edit_dialog);

                form.find('textarea').each(function() {
                    var text_area = $(this);
                    var text_editor = tinyMCE.get(text_area.attr('id'));
                    if (text_editor)
                        text_area.val(text_editor.getContent());
                });

                form_validator.settings.ignore = ".physical_location";
                var is_valid = form.valid();
                //re add rules
                form_validator.settings.ignore = [];

                if(!is_valid) return false;
                var form_id = form.attr('id');

                ajaxIndicatorStart('saving data.. please wait..');
                var locations = [];
                var location_type = $('#'+form_id+'_location_type',form).val();

                if(location_type === 'Various')
                {
                    //serialize locations
                    var rows  =  $("tbody > tr",$('#locations_table',form));

                    for(var i=0; i < rows.length;i++){
                        var row = rows[i];
                        locations.push({
                            city    : $('input.location_city',row).val(),
                            state   : $('input.location_state',row).val(),
                            country : $('input.location_country',row).val()
                        });
                    }

                }

                $(this).geocoding({
                    requests:locations,
                    buildGeoRequest:function(location){
                        var restrictions = {
                            locality: location.city,
                            country:location.country
                        };
                        if(location.state!=''){
                            restrictions.administrativeArea = location.state;
                        }
                        var request = {componentRestrictions:restrictions};
                        return request;
                    },
                    postProcessRequest:function(location, lat, lng){
                        location.lat = lat;
                        location.lng = lng;
                    },
                    processFinished:function(){
                        ajaxIndicatorStop();
                        var row     = edit_dialog.data('row');
                        var id      = parseInt(edit_dialog.data('id'));
                        var url     = 'api/v1/job-registration-requests';

                        var request = {
                            id : id,
                            point_of_contact_name  :  $('#'+form_id+'_point_of_contact_name',form).val(),
                            point_of_contact_email :  $('#'+form_id+'_point_of_contact_email',form).val(),
                            title         : $('#'+form_id+'_title',form).val(),
                            job_type      : $('#'+form_id+'_job_type',form).val(),
                            is_coa_needed : $('#'+form_id+'_is_coa_needed',form).prop('checked'),
                            url           : $('#'+form_id+'_url',form).val(),
                            description   : $('#'+form_id+'_description',form).val(),
                            instructions  : $('#'+form_id+'_instructions',form).val(),
                            company_id     : $('.company-composite', form).company_field('getCompany').id,
                            company        : $('.company-composite', form).company_field('getCompany').name,
                            location_type : $('#'+form_id+'_location_type',form).val(),
                            locations     : locations
                        };

                        $.ajax({
                            type: 'PUT',
                            url: url,
                            data: JSON.stringify(request),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            success: function (data,textStatus,jqXHR) {
                                edit_dialog.dialog( "close" );
                                form.cleanForm();
                                //update row values...
                                $('.title',row).text(request.title);
                                $('.url',row).text(request.url);
                                $('.location_type',row).text(request.location_type);
                                $('.company-name',row).text(request.company_name);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                ajaxError(jqXHR, textStatus, errorThrown);
                            }
                        });
                    },
                    cancelProcess:function(){
                        ajaxIndicatorStop();
                    },
                    errorMessage:function(location){
                        return 'job location: address ( city:'+location.city+',state: '+location.state+', country:'+location.country+' )';
                    }
                });
            },
            "Cancel": function() {
                edit_dialog.dialog( "close" );
            }
        }
    });

    var edit_live_dialog = $( "#edit_live_dialog" ).dialog({
        width: '100%',
        height: 900,
        modal: true,
        autoOpen: false,
        resizable: false,
        buttons: {
            "Save": function() {

                var form     = $('form',edit_live_dialog);

                form.find('textarea').each(function() {
                    var text_area = $(this);
                    var text_editor = tinyMCE.get(text_area.attr('id'));
                    if (text_editor)
                        text_area.val(text_editor.getContent());
                });

                form_validator.settings.ignore = ".physical_location";
                var is_valid = form.valid();
                //re add rules
                form_validator.settings.ignore = [];

                if(!is_valid) return false;
                var form_id = form.attr('id');

                ajaxIndicatorStart('saving data.. please wait..');
                var locations = [];
                var location_type = $('#'+form_id+'_location_type',form).val();

                if(location_type === 'Various')
                {
                    //serialize locations
                    var rows  =  $("tbody > tr",$('#locations_table',form));

                    for(var i=0; i < rows.length;i++){
                        var row = rows[i];
                        locations.push({
                            city    : $('input.location_city',row).val(),
                            state   : $('input.location_state',row).val(),
                            country : $('input.location_country',row).val()
                        });
                    }
                }

                $(this).geocoding({
                    requests:locations,
                    buildGeoRequest:function(location){
                        var restrictions = {
                            locality: location.city,
                            country:location.country
                        };
                        if(location.state!=''){
                            restrictions.administrativeArea = location.state;
                        }
                        var request = {componentRestrictions:restrictions};
                        return request;
                    },
                    postProcessRequest:function(location, lat, lng){
                        location.lat = lat;
                        location.lng = lng;
                    },
                    processFinished:function(){

                        var row        = edit_live_dialog.data('row');
                        var id         = parseInt(edit_live_dialog.data('id'));
                        var url        = 'api/v1/jobs';
                        var ajax_type  = 'POST';
                        var is_new_job = (id) ? false : true ;

                        var job = {
                            title         : $('#'+form_id+'_title',form).val(),
                            url           : $('#'+form_id+'_url',form).val(),
                            job_type      : $('#'+form_id+'_job_type',form).val(),
                            is_coa_needed : $('#'+form_id+'_is_coa_needed',form).prop('checked'),
                            description   : $('#'+form_id+'_description',form).val(),
                            instructions  : $('#'+form_id+'_instructions',form).val(),
                            company_id    : $('.company-composite', form).company_field('getCompany').id,
                            company       : $('.company-composite', form).company_field('getCompany').name,
                            location_type : $('#'+form_id+'_location_type',form).val(),
                            locations     : locations
                        };

                        if (!is_new_job) {
                            job.id = id;
                            ajax_type = 'PUT';
                        } else {
                            job.posted_date = $.datepicker.formatDate('yy-mm-dd', new Date());
                        }

                        $.ajax({
                            type: ajax_type,
                            url: url,
                            data: JSON.stringify(job),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            success: function (data,textStatus,jqXHR) {
                                edit_live_dialog.dialog( "close" );
                                form.cleanForm();
                                //update row values...
                                ajaxIndicatorStop();
                                if (is_new_job) {
                                    var new_id = data;
                                    console.log('new id '+new_id);
                                    var new_row = '<tr><td class="title"><a id="job' + new_id + '" href="#"></a>' + job.title + '</td>';
                                    new_row += '<td class="post-date">' + job.posted_date + '</td>';
                                    new_row += '<td class="url"><a href="' + job.url + '">Link</a></td><td class="company-name">' + job.company + '</td>';
                                    new_row += '<td class="location_type">' + job.location_type + '</td><td class="is_foundation"><input class="foundation_check" job_id="' + new_id + '" type="checkbox" /></td>';
                                    new_row += '<td class="buttons"><a href="#" data-job-id="' + new_id + '" class="edit-live-job roundedButton addDeploymentBtn">Edit</a>';
                                    new_row += '&nbsp;<a href="#" data-job-id="' + new_id + '" class="delete-live-job roundedButton addDeploymentBtn">Delete</a></td>';
                                    new_row += '</tr>';
                                    $('tbody', '#posted-jobs-table').prepend(new_row);
                                    return;
                                }
                                $('.title',row).text(job.title);
                                $('.url',row).find('a').attr('href',job.url);
                                $('.location_type',row).text(job.location_type);
                                $('.company-name',row).text(job.company);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                ajaxIndicatorStop();
                                ajaxError(jqXHR, textStatus, errorThrown);
                            }
                        });
                    },
                    cancelProcess:function(){
                        ajaxIndicatorStop();
                    },
                    errorMessage:function(location){
                        return 'job location: address ( city:'+location.city+',state: '+location.state+', country:'+location.country+' )';
                    }
                });
            },
            "Cancel": function() {
                edit_live_dialog.dialog( "close" );
            }
        }
    });

    var confirm_reject_dialog = $('#dialog-reject-post').dialog({
        resizable: false,
        autoOpen: false,
        height:500,
        width: '45.5%',
        modal: true,
        buttons: {
            "Reject": function() {
                var form     = $('form', confirm_reject_dialog);
                //var is_valid = form.valid();
                //if(!is_valid) return false;
                form.find('textarea').each(function() {
                    var text_area = $(this);
                    var text_editor = tinyMCE.get(text_area.attr('id'));
                    if (text_editor)
                        text_area.val(text_editor.getContent());
                });
                var id  = parseInt(confirm_reject_dialog.data('id'));
                var row = confirm_reject_dialog.data('row');
                var reject_data = {
                    send_rejection_email : $('#send_rejection_email',form).is(':checked'),
                    custom_reject_message: $('#custom_reject_message',form).val()
                };

                var url = 'api/v1/job-registration-requests/'+id+'/rejected';
                $.ajax({
                    type: 'PUT',
                    url: url,
                    data: JSON.stringify(reject_data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        row.hide('slow', function(){ row.remove();});
                        confirm_reject_dialog.dialog( "close" );
                        form.cleanForm();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        ajaxError(jqXHR, textStatus, errorThrown);
                    }
                });
            },
            "Cancel": function() {
                confirm_reject_dialog.dialog( "close" );
            }
        }
    });

    var confirm_post_dialog = $('#dialog-confirm-post').dialog({
        resizable: false,
        autoOpen: false,
        height:200,
        modal: true,
        buttons: {
            "Post": function() {
                var btn = $(".ui-dialog-buttonset button:contains('Post')",$(this).parent());
                btn.attr("disabled", true);
                var id  = $(this).data('id');
                var row = $(this).data('row');
                var url = 'api/v1/job-registration-requests/'+id+'/posted';
                $.ajax({
                    type: 'PUT',
                    url: url,
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        row.hide('slow', function(){ row.remove();});
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        ajaxError(jqXHR, textStatus, errorThrown);
                        btn.attr("disabled", false);
                    }
                });
                $(this).dialog( "close" );
            },
            "Cancel": function() {
                $( this ).dialog( "close" );
            }
        }
    });

    var confirm_delete_dialog = $('#dialog-delete-post').dialog({
        resizable: false,
        autoOpen: false,
        height:200,
        width: 450,
        modal: true,
        buttons: {
            "Delete": function() {
                var form     = $('form',confirm_delete_dialog);
                var id  = parseInt(confirm_delete_dialog.data('id'));
                var row = confirm_delete_dialog.data('row');

                var url = 'api/v1/jobs/'+id+'/delete';
                $.ajax({
                    type: 'PUT',
                    url: url,
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        row.hide('slow', function(){ row.remove();});
                        confirm_delete_dialog.dialog( "close" );
                        form.cleanForm();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        ajaxError(jqXHR, textStatus, errorThrown);
                    }
                });
            },
            "Cancel": function() {
                confirm_delete_dialog.dialog( "close" );
            }
        }
    });

    $('.edit-job').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-request-id');
        var row = $(this).parent().parent();
        var url = 'api/v1/job-registration-requests/'+id;
        $.ajax({
            type: 'GET',
            url: url,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                var form    = $('form',edit_dialog);
                form.cleanForm();
                var form_id = form.attr('id');
                //populate edit form
                $('#'+form_id+'_point_of_contact_name',form).val(data.point_of_contact_name);
                $('#'+form_id+'_point_of_contact_email',form).val(data.point_of_contact_email);
                $('#'+form_id+'_title',form).val(data.title);
                $('#'+form_id+'_url',form).val(data.url);
                $('#'+form_id+'_is_coa_needed',form).prop('checked', data.is_coa_needed);
                $('#'+form_id+'_job_type',form).val(data.job_type);
                $('.company-composite', form).company_field('setCompany', data.company);
                $('#'+form_id+'_description',form).val(data.description);
                $('#'+form_id+'_instructions',form).val(data.instructions);
                $('#'+form_id+'_expiration_date',form).val(data.expiration_date);
                $('#'+form_id+'_location_type',form).val(data.location_type);
                $('#'+form_id+'_expiration_date',form).prop('disabled',true);
                form.find('textarea').each(function() {
                    var text_area = $(this);
                    var text_editor = tinyMCE.get(text_area.attr('id'));
                    if (text_editor)
                        text_editor.setContent(text_area.val());
                });

                //erase all rows...

                $("#locations_table > tbody",form).html("");
                if(data.location_type === 'Various'){
                    $('#locations_table',form).show();
                    for(var i=0;i<data.locations.length;i++){
                        addLocation(data.locations[i]);
                    }
                }
                else
                    $('#locations_table',form).hide();

                edit_dialog.data('id',id).data('row',row).dialog( "open");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });

        return false;
    });

    $('.reject-job').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-request-id');
        var row = $(this).parent().parent();
        confirm_reject_dialog.data('id',id).data('row',row).dialog( "open");
        tinymce.get(form_id+'_custom_reject_message').setContent('');
        return false;
    });

    $('.post-job').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-request-id');
        var row = $(this).parent().parent();
        confirm_post_dialog.data('id',id).data('row',row).dialog( "open");
        return false;
    });

    $('.foundation_check').click(function(ev){
        var job_id  = $(this).attr('job_id');
        var url = 'api/v1/jobs/'+job_id+'/toggle_foundation';
        $.ajax({
            type: 'PUT',
            url: url,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });
    });

    $(document).on('click', '.edit-live-job', function() {
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-job-id');
        var row = $(this).parent().parent();
        var url = 'api/v1/jobs/'+id;

        $.ajax({
            type: 'GET',
            url: url,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                var form    = $('form',edit_live_dialog);
                form.cleanForm();
                var form_id = form.attr('id');
                //populate edit form
                $('#'+form_id+'_title',form).val(data.title);
                $('#'+form_id+'_url',form).val(data.url);
                $('#'+form_id+'_is_coa_needed',form).prop('checked', data.is_coa_needed);
                $('#'+form_id+'_job_type',form).val(data.job_type);
                $('.company-composite', form).company_field('setCompany', data.company);
                $('#'+form_id+'_description',form).val(data.description);
                $('#'+form_id+'_instructions',form).val(data.instructions);
                $('#'+form_id+'_expiration_date',form).val(data.expiration_date);
                $('#'+form_id+'_location_type',form).val(data.location_type);
                $('#'+form_id+'_expiration_date',form).prop('disabled',true);
                form.find('textarea').each(function() {
                    var text_area = $(this);
                    var text_editor = tinyMCE.get(text_area.attr('id'));
                    if (text_editor)
                        text_editor.setContent(text_area.val());
                });

                //erase all rows...

                $("#locations_table > tbody",form).html("");
                if(data.location_type === 'Various'){
                    $('#locations_table',form).show();
                    for(var i=0;i<data.locations.length;i++){
                        addLocation(data.locations[i]);
                    }
                }
                else
                    $('#locations_table',form).hide();

                edit_live_dialog.data('id',id).data('row',row).dialog( "open");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });

        return false;
    });


    $('.add-live-job').click(function(event){

        event.preventDefault();
        event.stopPropagation();

        var form    = $('form', edit_live_dialog);
        form.cleanForm();
        var form_id = form.attr('id');
        edit_live_dialog.dialog( "open");

        tinymce.get(form_id+'_description').setContent('');
        tinymce.get(form_id+'_instructions').setContent('');

        return false;
    });

    $(document).on('click', '.delete-live-job', function() {
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-job-id');
        var row = $(this).parent().parent();
        confirm_delete_dialog.data('id',id).data('row',row).dialog( "open");
        return false;
    });

    $.urlParam = function(name){
        var results = new RegExp("[\\?&]" + name + "=([^&#]*)").exec(window.location.href);
        if (results==null){
            return null;
        }
        else{
            return results[1] || 0;
        }
    }

    if($.urlParam("job")){

        var anchor = $("#job" + $.urlParam("job"));

        $("html, body").animate({
            scrollTop: anchor.offset().top - 30
        }, 2000);

        anchor.parents("tr").css("background-color","lightyellow");
    }
});