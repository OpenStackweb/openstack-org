/**
 * Copyright 2016 OpenStack Foundation
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
    var summit_id = $('#summit_id').val();

    //SPEAKER AUTOCOMPLETE  --------------------------------------------//
    var speakers_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/speakers/only/%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#speaker-search-1').tagsinput({
        itemValue: 'speaker_id',
        itemText: 'name',
        freeInput: false,
        maxTags: 1,
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
                source: speakers_source,
                limit: 20
            }
        ]
    });

    $('#speaker-search-2').tagsinput({
        itemValue: 'speaker_id',
        itemText: 'name',
        freeInput: false,
        maxTags: 1,
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
                source: speakers_source,
                limit: 20
            }
        ]
    });

    $('#speaker-search-1').on('itemAdded', function(event) {
        populateSpeaker(event.item.speaker_id, '1');
    });

    $('#speaker-search-2').on('itemAdded', function(event) {
        populateSpeaker(event.item.speaker_id, '2');
    });

    $('.selectable').click(function(){
        if (!$('#speaker-search-1').val() || !$('#speaker-search-2').val()) return false;

        var id = $(this).children().not('label').first().attr('id');
        var col = id.slice(-1);
        var other_id = (col == '1') ? id.slice(0, -1)+'2' : id.slice(0, -1)+'1';

        $('#'+id).toggleClass('selected');
        $('#'+id).removeClass('not_selected');

        $('#'+other_id).toggleClass('not_selected');
        $('#'+other_id).removeClass('selected');

    });

    $('#merge_button').click(function(){
        if ($('.selectable').children().not('.selected').not('.not_selected').length) {
           swal('','Please choose a speaker for all fields.','warning');
           return false;
        }
        swal(
            {
                title: "Attention!",
                text: "There is no going back. One of these speakers will be erased and the other one will have all the green fields",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, merge and delete.",
                closeOnConfirm: true
            },
            function(){
                mergeSpeakers();
            }
        );
    });


});

function populateSpeaker(speaker_id, col) {

    var summit_id  = $('#summit_id').val();
    var url        = 'api/v1/summits/'+summit_id+'/speakers/byID/'+speaker_id;

    $.ajax({
        type: 'GET',
        url: url,
        contentType: "application/json; charset=utf-8",
        dataType: "json"
    }).done(function(speaker) {
        $('#title-'+col).val(speaker.Title);
        $('#first_name-'+col).val(speaker.FirstName);
        $('#last_name-'+col).val(speaker.LastName);
        $('#email-'+col).val(speaker.Email);
        var pres_html = '';
        var last_summit = '';
        $.each(speaker.Presentations,function(idx,val){
            if (last_summit != val.SummitID) {
                pres_html += '<strong>Summit '+val.SummitID+':</strong><br>';
            }
            pres_html += '<a href="" target="_blank">'+val.Title+'</a><br>';
            last_summit = val.SummitID;
        });
        $('#presentations-'+col).html(pres_html);
        $('#twitter-'+col).val(speaker.Twitter);
        $('#irc-'+col).val(speaker.IRC);
        $('#bio-'+col).val(speaker.Bio);
        $('#picture-'+col).html(speaker.Pic);
        var exp_html = '';
        $.each(speaker.Expertise,function(idx,val){
            exp_html += val.Expertise+' - ';
        });
        $('#expertise-'+col).html(exp_html);
        var other_html = '';
        $.each(speaker.OtherPresentations,function(idx,val){
            other_html += val.Title+'<br>';
        });
        $('#other-'+col).html(other_html);
        var lang_html = '';
        $.each(speaker.Languages,function(idx,val){
            lang_html += val.Language+', ';
        });
        $('#languages-'+col).html(lang_html);
        var travel_html = '';
        $.each(speaker.TravelPreferences,function(idx,val){
            travel_html += val.Country+', ';
        });
        $('#travel-'+col).html(travel_html);
        var assists_html = '';
        $.each(speaker.Assistances,function(idx,val){
            assists_html += '<strong>Summit '+val.SummitID+':</strong><br>';
            assists_html += 'Ph: '+val.OnSitePhoneNumber;
            assists_html += ' - Registered: '+(val.RegisteredForSummit ? 'yes' : 'no');
            assists_html += ' - Confirmed: '+(val.IsConfirmed ? 'yes' : 'no')+'<br>';
        });
        $('#assistance-'+col).html(assists_html);

        var codes_html = '';
        $.each(speaker.Promocodes,function(idx,val){
            codes_html += '<strong>Summit '+val.SummitID+': </strong>'+val.Code+'<br>';
        });
        $('#promocode-'+col).html(codes_html);

        var roles_html = '';
        $.each(speaker.OrganizationalRoles,function(idx,val){
            roles_html += val.Role+', ';
        });
        $('#roles-'+col).html(roles_html);

        var involve_html = '';
        $.each(speaker.ActiveInvolvements,function(idx,val){
            involve_html += val.Involvement+', ';
        });
        $('#involvements-'+col).html(involve_html);

        if ($('#speaker-search-1').val() && $('#speaker-search-2').val()){
            $('*[data-speaker="1"]').addClass('selected');
            $('*[data-speaker="2"]').addClass('not_selected');
        }
    });
}

function mergeSpeakers() {
    var summit_id  = $('#summit_id').val();
    var speakers   = [];
    speakers['1']  = $('#speaker-search-1').val();
    speakers['2']  = $('#speaker-search-2').val();
    var url        = 'api/v1/summits/'+summit_id+'/speakers/merge/'+speakers['1']+'/'+speakers['2'];
    var request    = {};

    $('.selected').each(function(){
        request[$(this).data('field')] = speakers[$(this).data('speaker')];
    });



    $.ajax({
        type: 'POST',
        url: url,
        data: JSON.stringify(request),
        contentType: "application/json; charset=utf-8",
        dataType: "json"
    }).done(function(speaker) {
        swal({
                title: "Success!",
                text: 'Speakers Merged.',
                confirmButtonText: "Done!",
                type: "success"
            },
            function() {
                location.reload();
            });
    });
}