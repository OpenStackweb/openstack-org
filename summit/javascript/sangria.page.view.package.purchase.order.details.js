/**
 * Copyright 2015 OpenStack Foundation
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



$(document).ready(function ($) {

    $('.reject-purchase-order').live('click', function (evt) {
        evt.preventDefault();
        evt.stopPropagation();
        if(window.confirm('Are you sure?')) {
            var id = $(this).attr('data-purchase-order-id');
            var row = $(this).parent().parent();
            var row_count = $('#packages-purchare-orders-table tr').length - 1;
            var url = urls.rejectPackagePurchaseOrder.replace('%ID%', id);
            $.ajax({
                type: 'PUT',
                url: url,
                dataType: "json",
                success: function (data, textStatus, jqXHR) {
                    row.hide('slow', function () {
                        row.remove();
                        --row_count;
                        if (row_count == 0) {
                            $('#empty-message').show();
                            $('#packages-purchare-orders-table').hide();
                        }
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError(jqXHR, textStatus, errorThrown);
                }
            });
        }
        return false;
    });

    $('.approve-purchase-order').live('click', function (evt) {
        evt.preventDefault();
        evt.stopPropagation();
        if(window.confirm('Are you sure?')) {
            var row = $(this).parent().parent();
            var row_count = $('#packages-purchare-orders-table tr').length - 1;
            var id = $(this).attr('data-purchase-order-id');
            var url = urls.approvePackagePurchaseOrder.replace('%ID%', id);
            $.ajax({
                type: 'PUT',
                url: url,
                dataType: "json",
                success: function (data, textStatus, jqXHR) {
                    row.hide('slow', function () {
                        row.remove();
                        --row_count;
                        if (row_count == 0) {
                            $('#empty-message').show();
                            $('#packages-purchare-orders-table').hide();
                        }
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError(jqXHR, textStatus, errorThrown);
                }
            });
        }
        return false;
    });

    $('#purchase_order_status').change(function(evt){
        $('#purchase_order_filters').submit();
    });
});
