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
    var form = $('#bulk-promocode-form');

    $('#code_qty,#code_prefix,#company_id').change(function(){
       getCodes();
    });

    $('#speakers').on('itemAdded', function(event) {
        $(this).siblings('label').html('Speakers ('+$(this).tagsinput('items').length+')');
    });

    $('#speakers').on('itemRemoved', function(event) {
        $(this).siblings('label').html('Speakers ('+$(this).tagsinput('items').length+')');
    });

    $('#members').on('itemAdded', function(event) {
        $(this).siblings('label').html('Members ('+$(this).tagsinput('items').length+')');
    });

    $('#members').on('itemRemoved', function(event) {
        $(this).siblings('label').html('Members ('+$(this).tagsinput('items').length+')');
    });

    $('#code_type').change(function(){
        getCodes();
        if ($(this).val() == 'ALTERNATE' || $(this).val() == 'ACCEPTED' ) {
            $('#members_input').hide();
            $('#speakers_input').show();
        } else {
            $('#members_input').show();
            $('#speakers_input').hide();
        }

        if ($(this).val() == 'SPONSOR') {
            $('#company_input').show();
        } else {
            $('#company_input').hide();
        }
    });

    // Members Tags Input
    var members_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/members?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#members').tagsinput({
        itemValue: 'id',
        itemText: 'name',
        freeInput: false,
        trimValue: true,
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 3
            },
            {
                name: 'members_source',
                displayKey: 'name',
                source: members_source
            }
        ]
    });

    //Speakers Tags Input
    var speakers_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/speakers/%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#speakers').tagsinput({
        itemValue: 'speaker_id',
        itemText: 'name',
        freeInput: false,
        trimValue: true,
        typeaheadjs: [
            {
                hint: true,
                highlight: true,
                minLength: 3
            },
            {
                name: 'speakers_source',
                displayKey: 'name',
                source: speakers_source
            }
        ]
    });

    //SPONSOR AUTOCOMPLETE  --------------------------------------------//
    var company_source = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'api/v1/summits/'+summit_id+'/companies?query=%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#company_id').tagsinput({
        itemValue: 'id',
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
                name: 'company_source',
                displayKey: 'name',
                source: company_source
            }
        ]
    });

    // FORM SUBMIT
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            code_qty: { required: true },
            company_id :{ required: function(){
                var code_type = $('#code_type').find("option:selected").text();
                return code_type === 'SPONSOR';
            }},
        },
    });

    form.submit(function (evt) {
        evt.preventDefault();

        if (!form.valid()) return false;

        form.find(':submit').attr('disabled','disabled');
        var request   = form.serializeForm();
        var summit_id = $('#summit_id').val();

        $.ajax({
            type: 'POST',
            url: 'api/v1/summits/'+summit_id+'/registration-codes/bulk',
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function(promocodes) {
            var codes_string = '';
            $.each(promocodes,function(idx,code){
                codes_string += code+', ';
            });
            codes_string = codes_string.slice(0,-2);

            swal({
                title: "Success!",
                text: "Promocodes were created/assigned successfully. Promocodes: "+codes_string,
                confirmButtonText: "Done!",
                type: "success"
            },
            function() {
                location.reload();
            });
        }).fail(function(jqXHR) {
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0].message, 'warning');
            } else {
                swal('Error', 'There was a problem creating/assigning the promo codes, please contact admin.', 'warning');
            }
            form.find(':submit').removeAttr('disabled');
        });

        return false;
    });


});

function getCodes() {
    var code_type = $('#code_type').val();
    var code_qty = $('#code_qty').val();
    var code_prefix = $('#code_prefix').val();
    var company_id = $('#company_id').val();
    var summit_id = $('#summit_id').val();
    var request   = {
        limit: code_qty,
        prefix: code_prefix,
        type: code_type,
        company_id: company_id,
    };

    if (!code_qty) return false;
    if(code_type == 'SPONSOR' && !company_id) return false;

    $.getJSON('api/v1/summits/'+summit_id+'/registration-codes/free', request, function(codes){
        var code_html = '';
        $('#set_qty').hide();

        if (codes.length) {
            $.each(codes,function(idx,code) {
                code_html += '<div class="col-md-2">'+code.Code+'</div>';
            });

            $('#matching_codes').html(code_html).show();
            $('#use_codes_1').prop('disabled',false);
            $('#use_codes_2').prop('checked',false);
        } else {
            $('#matching_codes').html('<div class="col-md-12">No matches found.</div>').show();
            $('#use_codes_1').prop('disabled',true);
            $('#use_codes_2').prop('checked',true);
        }
    });

    return false;
}