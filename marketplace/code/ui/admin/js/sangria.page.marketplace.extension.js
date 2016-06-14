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

    var confirm_reject_dialog = $('#dialog-reject-review').dialog({
        resizable: false,
        autoOpen: false,
        height:180,
        width: 350,
        modal: true,
        buttons: {
            "Reject": function() {
                var form     = $('form',confirm_reject_dialog);
                var id  = parseInt(confirm_reject_dialog.data('id'));
                var row = confirm_reject_dialog.data('row');
                var url = 'api/v1/marketplace/reviews/reject/'+id;
                $.ajax({
                    type: 'POST',
                    url: url,
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

    var confirm_approve_dialog = $('#dialog-approve-review').dialog({
        resizable: false,
        autoOpen: false,
        height:180,
        width: 350,
        modal: true,
        buttons: {
            "Approve": function() {
                var btn = $(".ui-dialog-buttonset button:contains('Approve')",$(this).parent());
                btn.attr("disabled", true);
                var id  = $(this).data('id');
                var row = $(this).data('row');
                var url = 'api/v1/marketplace/reviews/approve/'+id;
                $.ajax({
                    type: 'POST',
                    url: url,
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        row.hide('slow', function(){ row.remove();});
                        btn.attr("disabled", false);
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

    $('.reject_review').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-request-id');
        var row = $(this).parent().parent();
        confirm_reject_dialog.data('id',id).data('row',row).dialog( "open");
        return false;
    });

    $('.approve_review').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var id  = $(this).attr('data-request-id');
        var row = $(this).parent().parent();
        confirm_approve_dialog.data('id',id).data('row',row).dialog( "open");
        return false;
    });

    $('#select-reviews').change(function(event){
        $('.approved_reviews').toggle();
        $('.not_approved_reviews').toggle();
    });

});