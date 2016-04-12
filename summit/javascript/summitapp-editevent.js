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

var form_validator = null;

$(document).ready(function(){

    $('#summit_type').chosen({width: "100%", placeholder_text_multiple: "Select Summit Types..."});

    $("#start_date").datetimepicker({
        format:'Y-m-d H:i:00',
        step:5,
        defaultDate:summit_begin_date,
        minDate:summit_begin_date,
        maxDate:summit_end_date,
        formatDate:'Y-m-d',
        defaultTime: summit_start_time,
        formatTime:'H:i:00',
    });

    $("#end_date").datetimepicker({
        format:'Y-m-d H:i:00',
        step:5,
        defaultDate:summit_begin_date,
        minDate:summit_begin_date,
        maxDate:summit_end_date,
        formatDate:'Y-m-d',
        defaultTime: summit_start_time,
        formatTime:'H:i:00',
    });

    var is_publishing = false;
    var summit_id     = $('#summit_id').val();


    $('#event_type').change(function(){
       var val = $(this).find("option:selected").text();
        if (val == 'Presentation' || val == 'Keynotes' ) {
           $('.speakers_container').show();
           $('.track_container').show();
           $('.level_container').show();
           $('#allow_feedback').attr("checked","checked");
           if(val == 'Keynotes')
               $('.moderator_container').show();
           else
               $('.moderator_container').hide();
           $('#expect_learn_container').show();
       }
       else {
           $('#expect_learn_container').hide();
           $('.speakers_container').hide();
           $('.moderator_container').hide();
           $('.track_container').hide();
           $('.level_container').hide();
           $('#allow_feedback').removeAttr("checked");
       }
    });
    // speakers autocomplete

    var speakers_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/speakers/%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#speakers').tagsinput({
        itemValue: 'unique_id',
        itemText: 'name',
        freeInput: false,
        allowDuplicates: false,
        trimValue: true,
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'speakers_source',
                displayKey: 'name',
                source: speakers_source
            }
        ]
    });

    var speakers_emails = [];
    $.each(speakers, function(index, value) {
        $('#speakers').tagsinput('add', value);
        speakers_emails.push(value.email);
    });

    var email_href = $('#email-speakers').attr('href')+speakers_emails.join();
    email_href += '?cc=speakersupport@openstack.org';
    $('#email-speakers').attr('href',email_href);

    $("#speakers").bind("paste", function(e){
        // access the clipboard using the api
        var pastedData = e.originalEvent.clipboardData.getData('text');
        alert(pastedData);
    } );

    // tags autocomplete

    var tags_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/tags?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#tags').tagsinput({
        itemValue: 'id',
        itemText: 'name',
        freeInput: false,
        allowDuplicates: false,
        trimValue: true,
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 3
            },
            {
                name: 'tags_source',
                displayKey: 'name',
                source: tags_source
            }
        ]
    });

    $.each(tags, function(index, value) {
        $('#tags').tagsinput('add', value);
    });

    // sponsors autocomplete

    var sponsors_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/sponsors?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#sponsors').tagsinput({
        itemValue: 'id',
        itemText: 'name',
        freeInput: false,
        allowDuplicates: false,
        trimValue: true,
        tagClass: 'label label-success',
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 2
            },
            {
                name: 'sponsors_source',
                displayKey: 'name',
                source: sponsors_source
            }
        ]
    });

    $.each(sponsors, function(index, value) {
        $('#sponsors').tagsinput('add', value);
    });
    // moderator autocomplete

    var moderators_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/speakers/%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#moderator').tagsinput({
        itemValue: 'unique_id',
        itemText: 'name',
        freeInput: false,
        maxTags: 1,
        trimValue: true,
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 3
            },
            {
                name: 'moderators_source',
                displayKey: 'name',
                source: moderators_source
            }
        ]
    });

    if (!$.isEmptyObject(moderator)) {
        $('#moderator').tagsinput('add', moderator);
    }

    tinymce.init({
        selector: "textarea",
        width:      '100%',
        height:     270,
        plugins:    [ "anchor link spellchecker" ],
        toolbar:    "formatselect, fontselect, fontsizeselect, bold, italic, underline, alignleft, aligncenter, alignright, alignjustify, bullist, numlist, outdent, indent, blockquote, undo, redo, removeformat, link, spellchecker",
        statusbar:  false,
        menubar:    false,
    });

    var form = $('#edit-event-form');

    jQuery.validator.addMethod("no_rel_urls", function(value, element) {
        return this.optional(element) || !(/(?=.*href\s*=\s*"(?!http))/.test(value));
    }, "We don't allow relative urls in the text.");

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            title: {required: true},
            short_description: {required: true, no_rel_urls: true},
            expect_learn: {no_rel_urls: true},
            rsvp_link: { url : true },
            headcount: { number: true },
            event_type: { required: true },
            summit_type: { required: true },
            level: { required: function(){
                var event_type = $('#event_type').find("option:selected").text();
                return event_type === 'Presentation' || event_type === 'Keynotes';
            }},
            track: { required: function(){
                var event_type = $('#event_type').find("option:selected").text();
                return event_type === 'Presentation' || event_type === 'Keynotes';
            }},
            speakers: { required: function(){
                var event_type = $('#event_type').find("option:selected").text();
                return event_type === 'Presentation' || event_type === 'Keynotes';
            }},
            moderator :{ required: function(){
                var event_type = $('#event_type').find("option:selected").text();
                return event_type === 'Keynotes';
            }},
            location: { required: function(){
                var published = $('#published').val();
                return is_publishing || published;
            }},
            start_date: { required: function(){
                var published = $('#published').val();
                var end_date  = $('#end_date').val();
                return is_publishing || published || end_date != '';
            }},
            end_date: { required: function(){
                var published = $('#published').val();
                var start_date  = $('#start_date').val();
                return is_publishing || published || start_date != '';
            }},
        },
    });

    $('#btn_save').click(function(evt){
        evt.preventDefault();
        form.find('textarea').each(function() {
            var text_area = $(this);
            var text_editor = tinyMCE.get(text_area.attr('id'));
            if (text_editor)
                text_area.val(text_editor.getContent());
        });
        is_publishing = false;
        if (!form.valid()) return false;
        form.find(':submit').attr('disabled','disabled');
        saveOrUpdate(is_publishing);
        return false;
    });

    $('#btn_publish').click(function(evt){
        evt.preventDefault();
        form.find('textarea').each(function() {
            var text_area = $(this);
            var text_editor = tinyMCE.get(text_area.attr('id'));
            if (text_editor)
                text_area.val(text_editor.getContent());
        });
        is_publishing = true;
        if (!form.valid()) return false;
        form.find(':submit').attr('disabled','disabled');
        saveOrUpdate(is_publishing);
        return false;
    });

    $('#btn_unpublish').click(function(evt)
    {
        evt.preventDefault();
        form.find(':submit').attr('disabled','disabled');
        var summit_id     = $('#summit_id').val();
        var event_id      = $('#event_id').val();
        var url           = 'api/v1/summits/'+summit_id+'/events/'+event_id+'/unpublish';

        swal({
                title: "Are you sure?",
                text: "You will be unpublishing this event from current schedule!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, UnPublish it!",
                closeOnConfirm: true,
                allowEscapeKey: false,
            },
            function(isConfirm){

                if (isConfirm) {
                    $.ajax({
                            type: 'DELETE',
                            url: url,
                            contentType: "application/json; charset=utf-8",
                            dataType: "json"
                        })
                        .done(function () {
                            swal("Unpublished!", "Your event was unpublished successfully.", "success");
                            location.reload();
                            form.find(':submit').removeAttr('disabled');
                        })
                        .fail(function (jqXHR) {
                            var responseCode = jqXHR.status;
                            if (responseCode == 412) {
                                var response = $.parseJSON(jqXHR.responseText);
                                swal('Validation error', response.messages[0].message, 'warning');
                            }
                            else {
                                swal('Error', 'There was a problem saving the event, please contact admin.', 'warning');
                            }
                            form.find(':submit').removeAttr('disabled');
                        });
                }
                else {
                    swal("Cancelled", "", "error");
                    form.find(':submit').removeAttr('disabled');
                }
            }
        );


        return false;
    });

    function saveOrUpdate(publish)
    {
        var summit_id     = $('#summit_id').val();
        var event_id      = ($('#event_id').val()) ? $('#event_id').val() : 0;
        var url           = 'api/v1/summits/'+summit_id+'/events';
        if(event_id) url += '/'+event_id

        var request = {
            title: $('#title').val(),
            rsvp_link: $('#rsvp_link').val(),
            headcount: $('#headcount').val(),
            short_description: tinyMCE.get('short_description').getContent(),
            expect_learn: tinyMCE.get('expect_learn').getContent(),
            location_id: $('#location').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            event_type: $('#event_type').val(),
            summit_type: $('#summit_type').val(),
            level: $('#level').val(),
            track: $('#track').val(),
            allow_feedback: ($('#allow_feedback').prop('checked')) ? 1 : 0,
            tags: $('#tags').val(),
            sponsors: $('#sponsors').val(),
            speakers: $('#speakers').tagsinput('items'),
            moderator: $('#moderator').tagsinput('items')[0],
            publish: publish
        };

        $.ajax({
            type: event_id ? 'PUT' : 'POST',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function(saved_event) {
            if (event_id) {
                swal("Updated!", "Your event was updated successfully.", "success");
                location.reload();
            } else {
                swal("Saved!", "Your event was created successfully.", "success");
                window.location = window.location+'/'+saved_event.ID;
                $('#event_id').val(saved_event.ID);
                $('.active','.breadcrumb').html(saved_event.Title);
            }
            form.find(':submit').removeAttr('disabled');
        }).fail(function(jqXHR) {
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0].message, 'warning');
            } else {
                swal('Error', 'There was a problem saving the event, please contact admin.', 'warning');
            }
            form.find(':submit').removeAttr('disabled');
        });

    }

});

