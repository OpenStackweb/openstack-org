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

    buildAutocompleteCategory = function (isFistTime) {
        var $eventCategoryAutocomplete = $( ".event-category-autocomplete" );

        if ($eventCategoryAutocomplete.length > 0) {
            $.getJSON('api/v1/events-types')
                .done(function(data) {
                    if (isFistTime) {
                        $eventCategoryAutocomplete.autocomplete({ source: data, minLength: 0 });
                    }
                    else {
                        $eventCategoryAutocomplete.autocomplete("option", { source: data });
                    }

                });

            $eventCategoryAutocomplete.on('focus', function() {
                $(this).autocomplete("search","");
            })
        }
    }

    buildAutocompleteCategory(true);

    var edit_dialog = $( "#edit_dialog" ).dialog({
            width: 450,
            height: 900,
            modal: true,
            autoOpen: false,
            resizable: false,
            buttons: {
                "Save": function() {
                    var form     = $('form',edit_dialog);
                    var is_valid = form.valid();
                    if(!is_valid) return false;
                    var row     = edit_dialog.data('row');
                    var id      = parseInt(edit_dialog.data('id'));
                    var form_id = form.attr('id');
                    var url     = 'api/v1/event-registration-requests';

                    var request = {
                        id : id,
                        point_of_contact_name  :  $('#'+form_id+'_point_of_contact_name',form).val(),
                        point_of_contact_email :  $('#'+form_id+'_point_of_contact_email',form).val(),
                        title      : $('#'+form_id+'_title',form).val(),
                        url        : $('#'+form_id+'_url',form).val(),
                        category   : $('#'+form_id+'_category',form).val(),
                        city       : $('#'+form_id+'_city',form).val(),
                        state      : $('#'+form_id+'_state',form).val(),
                        country    : $('#'+form_id+'_country',form).val(),
                        start_date : $('#'+form_id+'_start_date',form).val(),
                        end_date   : $('#'+form_id+'_end_date',form).val()
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
                            $('.event-category-autocomplete text',row).text(request.category);
                            $('.city',row).text(request.city);
                            $('.state',row).text(request.state);
                            $('.country',row).text(request.country);
                            $('.start-date',row).text(request.start_date);
                            $('.end-date',row).text(request.end_date);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            ajaxError(jqXHR, textStatus, errorThrown);
                        }
                    });
                },
                "Cancel": function() {
                    edit_dialog.dialog( "close" );
                }
            }
    });

    var edit_live_dialog = $( "#edit_live_dialog" ).dialog({
        width: 450,
        height: 550,
        modal: true,
        autoOpen: false,
        resizable: false,
        open: function() {
            var $validator = $("#EventForm_EventForm").data("validator");
            $validator.resetForm();
        },
        buttons: {
            "Save": function() {

                var form     = $('form',edit_live_dialog);
                var is_valid = form.valid();
                if(!is_valid) return false;
                var form_id = form.attr('id');
                var url     = 'api/v1/events';
                var ajax_type = 'POST';
                var row     = edit_live_dialog.data('row');
                var id      = parseInt(edit_live_dialog.data('id'));
                var is_new_event = (id) ? false : true ;

                var event = {
                    title      : $('#'+form_id+'_title',form).val(),
                    url        : $('#'+form_id+'_url',form).val(),
                    category   : $('#'+form_id+'_event_category',form).val(),
                    location   : $('#'+form_id+'_location',form).val(),
                    start_date : $('#'+form_id+'_start_date',form).val(),
                    end_date   : $('#'+form_id+'_end_date',form).val()
                };

                if (!is_new_event) {
                    event.id = id;
                    ajax_type = 'PUT';
                }



                $.ajax({
                    type: ajax_type,
                    url: url,
                    data: JSON.stringify(event),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        edit_live_dialog.dialog( "close" );
                        form.cleanForm();
                        //update row values...
                        if (is_new_event) {
                            var new_id = data;
                            var new_row = '<tr><td class="title"><a id="evt'+new_id+'" href="#"></a>'+event.title+'</td>';
                            new_row += '<td class="start-date">'+event.start_date+'</td><td class="end-date">'+event.end_date+'</td>';
                            new_row += '<td class="url"><a href="'+event.url+'">Link</a></td><td class="category">'+event.category+'</td><td class="location">'+event.location+'</td>';
                            new_row += '<td class="sponsor"></td><td class="summit"><input class="summit_check" event_id="'+new_id+'" type="checkbox" /></td>';
                            new_row += '<td width="17%"><a href="#" data-event-id="'+new_id+'" class="edit-live-event roundedButton addDeploymentBtn">Edit</a>';
                            new_row += '&nbsp;<a href="#" data-event-id="'+new_id+'" class="delete-live-event roundedButton addDeploymentBtn">Delete</a></td>';
                            new_row += '</tr>';

                            $('tbody','#event-registration-requests-table').prepend(new_row);
                        } else {
                            $('.title',row).text(event.title);
                            $('.url',row).find('a').attr('href',event.url);
                            $('.category',row).text(event.category);
                            $('.location',row).text(event.location);
                            $('.start-date',row).text(event.start_date);
                            $('.end-date',row).text(event.end_date);
                        }

                        buildAutocompleteCategory(false);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        ajaxError(jqXHR, textStatus, errorThrown);
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
        height:380,
        width: 450,
        modal: true,
        buttons: {
            "Reject": function() {
                var form     = $('form',confirm_reject_dialog);
                //var is_valid = form.valid();
                //if(!is_valid) return false;
                var id  = parseInt(confirm_reject_dialog.data('id'));
                var row = confirm_reject_dialog.data('row');
                var reject_data = {
                  send_rejection_email : $('#send_rejection_email',form).is(':checked'),
                  custom_reject_message: $('#custom_reject_message',form).val()
                };

                var url = 'api/v1/event-registration-requests/'+id+'/rejected';
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
        height:160,
        modal: true,
        buttons: {
            "Post": function() {
                var btn = $(".ui-dialog-buttonset button:contains('Post')",$(this).parent());
                btn.attr("disabled", true);
                var id  = $(this).data('id');
                var row = $(this).data('row');
                var url = 'api/v1/event-registration-requests/'+id+'/posted';
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

                var url = 'api/v1/events/'+id+'/delete';
                $.ajax({
                    type: 'PUT',
                    url: url,
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        row.hide('slow', function(){ row.remove();});
                        confirm_delete_dialog.dialog( "close" );
                        form.cleanForm();
                        buildAutocompleteCategory(false);
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

    $('.edit-event').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-request-id');
        var row = $(this).parent().parent();
        var url = 'api/v1/event-registration-requests/'+id;
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
                $('#'+form_id+'_event_category',form).val(data.category);
                $('#'+form_id+'_city',form).val(data.city);
                $('#'+form_id+'_state',form).val(data.state);
                $('#'+form_id+'_country',form).val(data.country);
                $('#'+form_id+'_country',form).trigger("chosen:updated");
                $('#'+form_id+'_start_date',form).val(data.start_date);
                $('#'+form_id+'_end_date',form).val(data.end_date);
                edit_dialog.data('id',id).data('row',row).dialog( "open");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });

        return false;
    });

    $('.reject-event').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-request-id');
        var row = $(this).parent().parent();
        confirm_reject_dialog.data('id',id).data('row',row).dialog( "open");
        return false;
    });

    $('.post-event').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-request-id');
        var row = $(this).parent().parent();
        confirm_post_dialog.data('id',id).data('row',row).dialog( "open");
        return false;
    });

    $('.add-live-event').click(function(event){
        event.preventDefault();
        event.stopPropagation();

        var form    = $('form',edit_live_dialog);
        form.cleanForm();
        var form_id = form.attr('id');
        edit_live_dialog.data('id',0).data('row',0).dialog( "open");

        var date_picker_start = $('#'+form_id+'_start_date',form);
        date_picker_start.datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0
        });

        var date_picker_end = $('#'+form_id+'_end_date',form);

        date_picker_end.datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0
        });

        return false;
    });

    $('.edit-live-event').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-event-id');
        var row = $(this).parent().parent();
        var url = 'api/v1/events/'+id;
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
                $('#'+form_id+'_event_category',form).val(data.category);
                $('#'+form_id+'_location',form).val(data.location);
                $('#'+form_id+'_start_date',form).val(data.start_date);
                $('#'+form_id+'_end_date',form).val(data.end_date);
                edit_live_dialog.data('id',id).data('row',row).dialog( "open");

                var date_picker_start = $('#'+form_id+'_start_date',form);
                date_picker_start.datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: 0
                });

                var date_picker_end = $('#'+form_id+'_end_date',form);
                date_picker_end.datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: 0
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });

        return false;
    });

    $('.delete-live-event').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-event-id');
        var row = $(this).parent().parent();
        confirm_delete_dialog.data('id',id).data('row',row).dialog( "open");
        return false;
    });

    $('.summit_check').click(function(ev){
        var event_id  = $(this).attr('event_id');
        var url = 'api/v1/events/'+event_id+'/toggle_summit';
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

    $.urlParam = function(name){
        var results = new RegExp("[\\?&]" + name + "=([^&#]*)").exec(window.location.href);
        if (results==null){
            return null;
        }
        else{
            return results[1] || 0;
        }
    }

    if($.urlParam("evt")){

        var anchor = $("#evt" + $.urlParam("evt"));

        $("html, body").animate({
            scrollTop: anchor.offset().top - 30
        }, 2000);

        anchor.parents("tr").css("background-color","lightyellow");
    }
});
