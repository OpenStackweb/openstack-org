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

    $('#code_type,#code_qty,#code_prefix').change(function(){
       getCodes();
    });

    // FORM SUBMIT
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            code_qty:    { required: true},
            code_prefix: { required: true},
            bulk_action: { required: true},
            members:     { required: true},
        },
    });

    form.submit(function (evt) {
        evt.preventDefault();

        if (!form.valid()) return false;

        form.find(':submit').attr('disabled','disabled');
        var disabled = form.find(':input:disabled').removeAttr('disabled');
        var request   = form.serializeForm();
        disabled.prop('disabled',true);
        var summit_id = $('#summit_id').val();
        var method    = (sponsor_id) ? 'PUT' : 'POST';
        var url       = (sponsor_id) ? 'api/v1/summits/'+summit_id+'/registration-codes/sponsors/'+sponsor_id : 'api/v1/summits/'+summit_id+'/registration-codes/sponsors';

        $.ajax({
            type: method,
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).done(function(sponsor_id) {
            swal({
                    title: "Updated!",
                    text: "Sponsor was updated successfully.",
                    type: "success"
                },
                function() {
                    window.location.href = this_url + sponsor_id;
                });
        }).fail(function(jqXHR) {
            var responseCode = jqXHR.status;
            if(responseCode == 412) {
                var response = $.parseJSON(jqXHR.responseText);
                swal('Validation error', response.messages[0].message, 'warning');
            } else {
                swal('Error', 'There was a problem updating the sponsor, please contact admin.', 'warning');
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
    var summit_id = $('#summit_id').val();
    var request   = {
        page: 1,
        items: code_qty,
        prefix: code_prefix,
        type: code_type
    };

    if (!code_qty) return false;

    $.getJSON('api/v1/summits/'+summit_id+'/registration-codes/free', request, function(data){
        var code_html = '';
        $('#set_qty').hide();

        if (data.codes.length) {
            $.each(data.codes,function(idx,code) {
                code_html += '<div class="col-md-2">'+code.code+'</div>';
            });

            $('#matching_codes').html(code_html).show();
            $('#bulk_action_1').prop('disabled',false);
            $('#bulk_action_2').prop('checked',false);
        } else {
            $('#matching_codes').html('<div class="col-md-12">No matches found.</div>').show();
            $('#bulk_action_1').prop('disabled',true);
            $('#bulk_action_2').prop('checked',true);
        }
    });

    return false;
}