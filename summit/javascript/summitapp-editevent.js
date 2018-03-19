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

var form_validator        = null;
var TaxonomyEvent         = 1;
var TaxonomyPresentation  = 2;
var TaxonomyGroupEvent    = 3;
var TaxonomyTeamEvent     = 4;
var TaxonomyEventWithFile = 5;

$(document).ready(function(){

    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        $('#attachment-filename').val(label);
    });

    $("#location").chosen();

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

        var item              = $(this).find(':selected');

        if(item.length == 0) return;

        var useSponsors       = item.data('use-sponsors');
        var sponsorsMandatory = item.data('sponsors-mandatory');
        var type              = item.data('type-taxonomy');

        if(useSponsors) $('.sponsors-container').show();
        else $('.sponsors-container').hide();

        if(sponsorsMandatory){
            $('#sponsors').rules('add',{ required : true});
        }
        else{
            $('#sponsors').rules("remove");
        }

        if(type == TaxonomyPresentation){
            var useSpeakers       = item.data('use-speakers');
            var speakersMandatory = item.data('speakers-mandatory');

            if(useSpeakers) $('.speakers-container').show();
            else $('.speakers-container').hide();

            if(speakersMandatory){
                $('#speakers').rules('add',{ required : true});
            }
            else{
                $('#speakers').rules("remove");
            }

            var useModerator       = item.data('use-moderator');
            var moderatorMandatory = item.data('moderator-mandatory');
            var moderatorLabel     = item.data('moderator-label');
            if(useModerator) $('.moderator-container').show();
            else $('.moderator-container').hide();

            if(moderatorMandatory){
                $('#moderator').rules('add',{ required : true});
            }
            else{
                $('#moderator').rules("remove");
            }
            $('.moderator-label').text(moderatorLabel);
            $('.level_container').show();
            $('#expect_learn_container').show();
            $('.to_record_container').show();

            // only prepopulate on new
            if (!$('#event_id').val()) {
                $('#allow_feedback').attr("checked","checked");
            }
        }
        else{
            $('#expect_learn_container').hide();
            $('.level_container').hide();
            $('.moderator-container').hide();
            $('.speakers-container').hide();
            $('.to_record_container').hide();
            $('#moderator').rules("remove");
            $('#speakers').rules("remove");

            // only prepopulate on new
            if (!$('#event_id').val()) {
                $('#allow_feedback').removeAttr("checked");
            }
        }

        if(type == TaxonomyGroupEvent ){
            $('.groups_container').show();
        }
        else{
            $('.groups_container').hide();
        }

        if(type == TaxonomyEventWithFile ){
            $('.attachment_container').show();
        }
        else{
            $('.attachment_container').hide();
            $('#attachment-filename').val('');
        }
    });
    // speakers autocomplete

    var speakers_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/speakers/search?term=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#speakers').tagsinput({
        itemValue: 'unique_id',
        itemText: 'name',
        freeInput: false,
        allowDuplicates: false,
        trimValue: true,
        tagClass: function(item) {
            return 'label label-info speaker_' + item.speaker_id ;
        },
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'speakers_source',
                displayKey: 'name',
                source: speakers_source,
                limit: 20
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
                source: tags_source,
                limit: 20
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
                source: sponsors_source,
                limit: 20
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
            url: 'api/v1/summits/'+summit_id+'/speakers/search?term=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#moderator').tagsinput({
        itemValue: 'unique_id',
        itemText: 'name',
        freeInput: false,
        maxTags: 1,
        trimValue: true,
        tagClass: function(item) {
            return 'label label-info speaker_' + item.speaker_id ;
        },
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 3
            },
            {
                name: 'moderators_source',
                displayKey: 'name',
                source: moderators_source,
                limit: 20
            }
        ]
    });

    if (!$.isEmptyObject(moderator)) {
        $('#moderator').tagsinput('add', moderator);
    }

    // groups autocomplete

    var groups_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/groups?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#groups').tagsinput({
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
                name: 'groups_source',
                displayKey: 'name',
                source: groups_source,
                limit: 20
            }
        ]
    });

    $.each(groups, function(index, value) {
        $('#groups').tagsinput('add', value);
    });

    tinymce.init({
        selector: "textarea.html_text",
        width:      '99%',
        height:     150,
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
            abstract: {required: true, no_rel_urls: true},
            social_summary: {required: true , maxlength: 100},
            expect_learn: {no_rel_urls: true},
            rsvp_link: { url : true },
            headcount: { number: true },
            event_type: { required: true },
            level: { required: function(){
                var type = $('#event_type').find("option:selected").data('type-taxonomy');
                return type == TaxonomyPresentation;
            }},
            track: { required: function(){
                return true;
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
                var published   = $('#published').val();
                var start_date  = $('#start_date').val();
                return is_publishing || published || start_date != '';
            }},
            groups: { required: function(){
                var type = $('#event_type').find("option:selected").data('type-taxonomy');
                return type === TaxonomyGroupEvent;
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
                allowEscapeKey: false
            }).then(function(isConfirm){
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
                    return;
                }
                swal("Cancelled", "", "error");
                form.find(':submit').removeAttr('disabled');
        }).catch(swal.noop);

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
            abstract: tinyMCE.get('abstract').getContent(),
            social_summary:$('#social_summary').val(),
            expect_learn: tinyMCE.get('expect_learn').getContent(),
            location_id: $('#location').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            event_type: $('#event_type').val(),
            level: $('#level').val(),
            track: $('#track').val(),
            allow_feedback: ($('#allow_feedback').prop('checked')) ? 1 : 0,
            feature_cloud: $('input[name=feature_cloud]:checked').val(),
            tags: $('#tags').val(),
            sponsors: $('#sponsors').val(),
            speakers: $('#speakers').tagsinput('items'),
            moderator: $('#moderator').tagsinput('items')[0],
            groups: $('#groups').tagsinput('items'),
            publish: publish,
            to_record: ($('#to_record').prop('checked')) ? 1 : 0,
        };

        $.ajax({
            type: event_id ? 'PUT' : 'POST',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function(saved_event) {

            if($('#attachment-filename').val()){
                // upload file
                uploadAttachment(event_id == 0, saved_event);
                return;
            }
            finishEventSaveOrUpdate(event_id == 0, saved_event);
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

    function finishEventSaveOrUpdate(newEvent, event){
        if (newEvent) {
            swal("Saved!", "Your event was created successfully.", "success");
            window.location = window.location+'/'+event.ID;
            $('#event_id').val(event.ID);
            $('.active','.breadcrumb').html(event.Title);
        } else {
            swal("Updated!", "Your event was updated successfully.", "success");
            location.reload();
        }
        form.find(':submit').removeAttr('disabled');
    }

    function uploadAttachment(newEvent, event)
    {
        var summit_id  = $('#summit_id').val();
        var url        = 'api/v1/summits/'+summit_id+'/events/'+event.ID+'/attach';
        var file_data  = $("#event-attachment").prop("files")[0];
        var form_data  = new FormData();

        form_data.append("file", file_data);

        if ($('#attachment-filename').val()) {
            $.ajax({
                url: url,
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'POST',
                success: function(attachment_id){
                    finishEventSaveOrUpdate(newEvent, event);
                },
                error: function(response,status,error) {
                    swal('Validation error', response.responseJSON.messages[0].message, 'warning');
                }
            });
        }
    }

    $('.speakers-container').on('click', '.tag', function(){
        var speaker_class = $.grep(this.className.split(" "), function(v, i){
            return v.indexOf('speaker_') === 0;
        }).join();
        var speaker_id = speaker_class.split('speaker_')[1];

        var url = 'summit-admin/' + summit_id + '/speakers/' + speaker_id;
        console.log(url);
        window.open(url, '_blank');
    });

    $('.moderator-container').on('click', '.tag', function(){
        var speaker_class = $.grep(this.className.split(" "), function(v, i){
            return v.indexOf('speaker_') === 0;
        }).join();
        var speaker_id = speaker_class.split('speaker_')[1];

        var url = 'summit-admin/' + summit_id + '/speakers/' + speaker_id;
        console.log(url);
        window.open(url, '_blank');
    });

});

